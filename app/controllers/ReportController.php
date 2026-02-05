<?php

namespace App\Controllers;

use App\Core\Controller;

use App\Repositories\AttendanceRepository;
use App\Repositories\ServerRepository;
use App\Core\Database;

class ReportController extends Controller {
    private $attendanceRepo;
    private $serverRepo;
    private $db;

    public function __construct() {
        $this->requireLogin();
        // Restrict to Admin and Superadmin
        $role = $_SESSION['role'] ?? 'User';
        if ($role !== 'Admin' && $role !== 'Superadmin') {
            $this->forbidden();
        }
        $this->attendanceRepo = new AttendanceRepository();
        $this->serverRepo = new ServerRepository();
        $this->db = Database::getInstance();
    }

    public function index() {
        // 1. Overall Stats (Current Month)
        $this->db->query("
            SELECT 
                SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as total_present,
                SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as total_late,
                SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as total_absent,
                COUNT(*) as total_records
            FROM attendance 
            WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
            AND YEAR(created_at) = YEAR(CURRENT_DATE())
        ");
        $stats = $this->db->single();

        // Avoid division by zero
        $total = $stats->total_records > 0 ? $stats->total_records : 1;
        $rates = [
            'present' => round(($stats->total_present / $total) * 100, 1),
            'late' => round(($stats->total_late / $total) * 100, 1),
            'absent' => round(($stats->total_absent / $total) * 100, 1)
        ];

        // Total Activities (Schedules) this month
        $this->db->query("SELECT COUNT(*) as count FROM schedules WHERE MONTH(mass_date) = MONTH(CURRENT_DATE()) AND YEAR(mass_date) = YEAR(CURRENT_DATE())");
        $totalActivities = $this->db->single()->count;

        // 2. Monthly Trends (Last 6 Months)
        $this->db->query("
            SELECT 
                DATE_FORMAT(s.mass_date, '%b') as month_name,
                SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as present_count
            FROM attendance a
            JOIN schedules s ON a.schedule_id = s.id
            WHERE s.mass_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY YEAR(s.mass_date), MONTH(s.mass_date)
            ORDER BY s.mass_date ASC
        ");
        $trends = $this->db->resultSet();
        
        $trendLabels = [];
        $trendData = [];
        foreach($trends as $t) {
            $trendLabels[] = $t->month_name;
            $trendData[] = $t->present_count;
        }

        // 3. Server Performance (Top 5)
        $this->db->query("
            SELECT 
                CONCAT_WS(' ', srv.first_name, srv.middle_name, srv.last_name) as name, 
                COUNT(*) as total_assigned,
                SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as present_count
            FROM attendance a
            JOIN servers srv ON a.server_id = srv.id
            GROUP BY srv.id
            ORDER BY present_count DESC
            LIMIT 5
        ");
        $topServers = $this->db->resultSet();

        $this->view('reports/index', [
            'pageTitle' => 'Reports & Analytics',
            'title' => 'Reports | ASMS',
            'stats' => $stats,
            'rates' => $rates,
            'totalActivities' => $totalActivities,
            'trendLabels' => json_encode($trendLabels),
            'trendData' => json_encode($trendData),
            'topServers' => $topServers
        ]);
    }

    public function download() {
        // Load Composer Autoloader
        $autoloadPath = __DIR__ . '/../../vendor/autoload.php';
        if (file_exists($autoloadPath)) {
            require_once $autoloadPath;
        } else {
            die("Error: 'vendor/autoload.php' not found. Please run 'composer require dompdf/dompdf'.");
        }

        // --- FETCH REAL DATA (Current Month) ---
        $currentMonth = date('m');
        $currentYear = date('Y');

        // 1. Stats Summary
        $this->db->query("SELECT COUNT(*) as total FROM servers WHERE status = 'Active'");
        $activeServersCount = $this->db->single()->total;

        $this->db->query("
            SELECT 
                SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as total_present,
                SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as total_absent,
                COUNT(*) as total_records
            FROM attendance 
            WHERE MONTH(created_at) = :m AND YEAR(created_at) = :y
        ");
        $this->db->bind(':m', $currentMonth);
        $this->db->bind(':y', $currentYear);
        $monthStats = $this->db->single();

        $overallRate = $monthStats->total_records > 0 
            ? round(($monthStats->total_present / $monthStats->total_records) * 100, 1) . '%' 
            : '0%';

        $stats = [
            'active_servers' => $activeServersCount,
            'rate' => $overallRate,
            'absences' => $monthStats->total_absent ?? 0
        ];
        
        // 2. Performance Breakdown per Server
        $this->db->query("
            SELECT 
                CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) as name,
                SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) as absent,
                COUNT(*) as total
            FROM servers s
            JOIN attendance a ON s.id = a.server_id
            JOIN schedules sch ON a.schedule_id = sch.id
            WHERE MONTH(sch.mass_date) = :m AND YEAR(sch.mass_date) = :y
            GROUP BY s.id
            ORDER BY s.last_name ASC, s.first_name ASC
        ");
        $this->db->bind(':m', $currentMonth);
        $this->db->bind(':y', $currentYear);
        $rawPerformance = $this->db->resultSet();

        $data = [];
        foreach ($rawPerformance as $row) {
            $rate = $row->total > 0 ? round(($row->present / $row->total) * 100) . '%' : '0%';
            $data[] = [
                'name' => $row->name,
                'present' => $row->present,
                'late' => $row->late,
                'absent' => $row->absent,
                'rate' => $rate
            ];
        }
        // ---------------------------------------------------------------------

        // Prepare Logo
        $logoPath = __DIR__ . '/../../public/images/logo.png';
        $parishLogoPath = __DIR__ . '/../../public/images/parish-logo.png';
        
        $logoData = '';
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoData = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $parLogoData = '';
        if (file_exists($parishLogoPath)) {
            $type = pathinfo($parishLogoPath, PATHINFO_EXTENSION);
            $parLogoData = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($parishLogoPath));
        }

        // HTML Content
        $html = '
        <html>
        <head>
            <style>
                body { font-family: Helvetica, sans-serif; color: #333; margin: 10px; }
                .header { border-bottom: 2px solid #1e63d4; padding-bottom: 15px; margin-bottom: 20px; }
                .logo { width: 55px; height: auto; }
                
                .header-table { width: 100%; border: none; border-collapse: collapse; }
                .header-side { width: 20%; text-align: center; border: none; }
                .header-center { width: 60%; text-align: center; border: none; }
                
                .parish-name { font-size: 14px; font-weight: bold; color: #1e63d4; text-transform: uppercase; margin: 0; }
                .ministry-name { font-size: 11px; font-weight: bold; color: #444; margin: 2px 0; }
                .report-title { font-size: 18px; font-weight: 900; color: #1a202c; margin-top: 10px; text-transform: uppercase; letter-spacing: 1px; }
                .report-subtitle { font-size: 10px; color: #666; margin-top: 2px; }

                h3 { border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-top: 25px; font-size: 12px; text-transform: uppercase; color: #1e63d4; font-weight: bold; }
                
                table.stats-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                table.stats-table th { background-color: #f8fafc; text-align: left; padding: 10px; border: 1px solid #e2e8f0; font-weight: bold; color: #475569; font-size: 10px; }
                table.stats-table td { padding: 10px; border: 1px solid #e2e8f0; color: #334155; font-size: 10px; }

                table.data-table { width: 100%; border-collapse: collapse; font-size: 9px; margin-top: 10px; }
                table.data-table th { background-color: #f8fafc; color: #1e293b; font-weight: bold; text-transform: uppercase; padding: 8px; border: 1px solid #e2e8f0; text-align: left; }
                table.data-table td { padding: 8px; border: 1px solid #e2e8f0; color: #334155; }
                
                .text-center { text-align: center; }
                .text-red { color: #dc2626; font-weight: bold; }
                .text-green { color: #059669; font-weight: bold; }
                
                .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
            </style>
        </head>
        <body>
            <div class="header">
                <table class="header-table">
                    <tr>
                        <td class="header-side">
                            ' . ($logoData ? '<img src="' . $logoData . '" class="logo">' : '') . '
                        </td>
                        <td class="header-center">
                            <div class="parish-name">Sacred Heart of Jesus Parish</div>
                            <div class="ministry-name">Ministry of Altar Servers (MAS-SHJP MBS)</div>
                            <div class="report-title">Monthly Attendance Report</div>
                            <div class="report-subtitle">Generated on ' . date('F d, Y') . '</div>
                        </td>
                        <td class="header-side">
                            ' . ($parLogoData ? '<img src="' . $parLogoData . '" class="logo">' : '') . '
                        </td>
                    </tr>
                </table>
            </div>

            <h3>Executive Summary</h3>
            <table class="stats-table">
                <tr>
                    <th width="70%">Metric</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>Total Active Servers</td>
                    <td>' . $stats['active_servers'] . '</td>
                </tr>
                <tr>
                    <td>Overall Attendance Rate</td>
                    <td class="text-green">' . $stats['rate'] . '</td>
                </tr>
                <tr>
                    <td>Total Absences</td>
                    <td class="text-red">' . $stats['absences'] . '</td>
                </tr>
            </table>

            <h3>Performance Breakdown</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Server Name</th>
                        <th class="text-center">Present</th>
                        <th class="text-center">Late</th>
                        <th class="text-center">Absent</th>
                        <th class="text-center">Rate</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($data as $row) {
            $html .= '<tr>
                <td style="font-weight: bold;">' . h(strtoupper($row['name'])) . '</td>
                <td class="text-center">' . $row['present'] . '</td>
                <td class="text-center">' . $row['late'] . '</td>
                <td class="text-center">' . $row['absent'] . '</td>
                <td class="text-center" style="font-weight: bold; color: #1e63d4;">' . $row['rate'] . '</td>
            </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div class="footer">
                ASMS System Generated Document • Generated by ' . ($_SESSION['full_name'] ?? 'Administrator') . '
            </div>
        </body>
        </html>';

        // Render PDF
        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Output
        $dompdf->stream("Attendance_Report_" . date('Y-m-d') . ".pdf", ["Attachment" => true]);
        exit;
    }
}