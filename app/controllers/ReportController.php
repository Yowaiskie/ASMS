<?php

namespace App\Controllers;

use App\Core\Controller;

class ReportController extends Controller {
    public function __construct() {
        $this->requireLogin();
    }

    public function index() {
        $this->view('reports/index', [
            'pageTitle' => 'Reports & Analytics',
            'title' => 'Reports | ASMS'
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

        // --- FETCH DATA (Mock for now, replace with Repository calls later) ---
        $stats = [
            'active_servers' => 24,
            'rate' => '88.8%',
            'absences' => 30
        ];
        
        $data = [
            ['name' => 'John Doe', 'present' => 18, 'late' => 1, 'absent' => 1, 'rate' => '90%'],
            ['name' => 'Jane Smith', 'present' => 19, 'late' => 0, 'absent' => 0, 'rate' => '100%'],
            ['name' => 'Michael Brown', 'present' => 15, 'late' => 2, 'absent' => 3, 'rate' => '75%'],
        ];
        // ---------------------------------------------------------------------

        // Prepare Logo
        $logoPath = __DIR__ . '/../../public/images/logo.png';
        $logoData = '';
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $dataImg = file_get_contents($logoPath);
            $logoData = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
        }

        // HTML Content
        $html = '
        <html>
        <head>
            <style>
                body { font-family: Helvetica, sans-serif; color: #333; margin: 20px; }
                .header { border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
                h1 { text-transform: uppercase; font-size: 18px; margin: 0 0 5px 0; color: #1a202c; }
                p { font-size: 12px; color: #666; margin: 2px 0; }
                
                .logo { width: 60px; height: auto; margin-right: 15px; }
                .header-content { display: inline-block; vertical-align: middle; }
                
                h3 { border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-top: 30px; font-size: 14px; text-transform: uppercase; color: #4a5568; }
                
                table { width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 10px; }
                th { background-color: #f3f4f6; text-align: left; padding: 10px; border: 1px solid #e5e7eb; font-weight: bold; color: #374151; }
                td { padding: 10px; border: 1px solid #e5e7eb; color: #4b5563; }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .text-red { color: #dc2626; font-weight: bold; }
                .text-green { color: #059669; font-weight: bold; }
                
                .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
            </style>
        </head>
        <body>
            <div class="header">
                <table style="border: none; margin: 0; width: 100%;">
                    <tr style="border: none;">
                        <td style="border: none; width: 70%; vertical-align: middle;">
                            ' . ($logoData ? '<img src="' . $logoData . '" class="logo" style="vertical-align: middle;">' : '') . '
                            <div class="header-content">
                                <h1>Monthly Attendance Report</h1>
                                <p>Altar Servers Management System</p>
                            </div>
                        </td>
                        <td style="border: none; text-align: right; vertical-align: middle;">
                            <p><strong>Date Generated</strong></p>
                            <p>' . date('F d, Y') . '</p>
                        </td>
                    </tr>
                </table>
            </div>

            <h3>Executive Summary</h3>
            <table>
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
            <table>
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
                <td>' . $row['name'] . '</td>
                <td class="text-center">' . $row['present'] . '</td>
                <td class="text-center">' . $row['late'] . '</td>
                <td class="text-center">' . $row['absent'] . '</td>
                <td class="text-center">' . $row['rate'] . '</td>
            </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div class="footer">
                Confidential Document • Generated by System Administrator
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