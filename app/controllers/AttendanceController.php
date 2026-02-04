<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\AttendanceRepository;
use App\Repositories\ServerRepository;
use App\Core\Database;

use App\Repositories\ScheduleRepository; // Add this import

class AttendanceController extends Controller {
    private $attendanceRepo;
    private $serverRepo;
    private $scheduleRepo;
    private $db;

    public function __construct() {
        $this->requireLogin();
        $this->attendanceRepo = new AttendanceRepository();
        $this->serverRepo = new ServerRepository();
        $this->scheduleRepo = new ScheduleRepository();
        $this->db = Database::getInstance();
    }

    public function index() {
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'User') {
            // ... (User View Logic remains same) ...
            $allRecords = $this->attendanceRepo->getByUserId($_SESSION['user_id']);
            
            $stats = ['Present' => 0, 'Late' => 0, 'Absent' => 0, 'Excused' => 0];
            $massRecords = [];
            $meetingRecords = [];

            foreach ($allRecords as $r) {
                if (isset($stats[$r->status])) $stats[$r->status]++;
                if (stripos($r->mass_type, 'Meeting') !== false) $meetingRecords[] = $r;
                else $massRecords[] = $r;
            }

            $this->view('attendance/user_index', [
                'pageTitle' => 'My Attendance',
                'title' => 'My Attendance | ASMS',
                'stats' => $stats,
                'massRecords' => $massRecords,
                'meetingRecords' => $meetingRecords
            ]);
        } else {
            // Admin View
            $date = $_GET['date'] ?? date('Y-m-d');
            $search = trim($_GET['search'] ?? '');
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $rawLogs = $this->attendanceRepo->getDailyAttendance($date, $search, $limit, $offset);
            $totalRecords = $this->attendanceRepo->countActiveServers($search);
            $totalPages = ceil($totalRecords / $limit);

            $dailySchedules = $this->scheduleRepo->getByDate($date);
            
            // Find if there is a meeting today
            $meetingSchedule = null;
            foreach ($dailySchedules as $sch) {
                if (stripos($sch->mass_type, 'Meeting') !== false) {
                    $meetingSchedule = $sch;
                    break;
                }
            }

            $attendanceList = [];
            
            // First pass: Process existing attendance records
            foreach($rawLogs as $row) {
                $sid = $row->server_id;
                if (!isset($attendanceList[$sid])) {
                    $attendanceList[$sid] = [
                        'id' => $sid,
                        'name' => $row->name,
                        'mass' => null,
                        'meeting' => null,
                        'others' => []
                    ];
                }

                if ($row->schedule_id) {
                    $type = $row->mass_type;
                    if (stripos($type, 'Meeting') !== false) {
                        $attendanceList[$sid]['meeting'] = $row;
                    } else {
                        if ($attendanceList[$sid]['mass'] === null) {
                            $attendanceList[$sid]['mass'] = $row;
                        }
                    }
                }
            }

            // Second pass: Ensure everyone has the meeting if it exists
            if ($meetingSchedule) {
                foreach ($attendanceList as $sid => &$data) {
                    if ($data['meeting'] === null) {
                        // Inject placeholder
                        $data['meeting'] = (object) [
                            'attendance_id' => null, // No record yet
                            'status' => 'Pending',
                            'schedule_id' => $meetingSchedule->id,
                            'mass_type' => $meetingSchedule->mass_type,
                            'mass_time' => $meetingSchedule->mass_time
                        ];
                    }
                }
            }

            $this->view('attendance/index', [
                'pageTitle' => 'Attendance Management',
                'title' => 'Attendance | ASMS',
                'attendanceList' => $attendanceList,
                'date' => $date,
                'search' => $search,
                'pagination' => [
                    'page' => $page,
                    'totalPages' => $totalPages,
                    'totalRecords' => $totalRecords
                ]
            ]);
        }
    }

    public function update() {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['attendance_id'] ?? '';
            $status = $_POST['status'];
            $date = $_POST['date'] ?? date('Y-m-d');
            $scheduleId = $_POST['schedule_id'] ?? '';
            $serverId = $_POST['server_id'] ?? '';
            $page = $_POST['page'] ?? 1;
            $search = $_POST['search'] ?? '';

            // Handle creation if no ID
            // ... (keep logic) ...
            if (empty($id) && !empty($scheduleId) && !empty($serverId)) {
                $id = $this->attendanceRepo->assign($scheduleId, $serverId, $status);
                if (!$id) {
                    setFlash('msg_error', 'Failed to create attendance record.');
                    redirect("attendance?date=$date&page=$page&search=" . urlencode($search));
                    return;
                }
            }

            if ($this->attendanceRepo->updateStatus($id, $status)) {
                // Email Notification and Suspension Logic
                $this->db->query("
                    SELECT s.id as server_id, s.email, CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) as name, sch.mass_type, sch.mass_date 
                    FROM attendance a 
                    JOIN servers s ON a.server_id = s.id 
                    JOIN schedules sch ON a.schedule_id = sch.id 
                    WHERE a.id = :id
                ");
                $this->db->bind(':id', $id);
                $info = $this->db->single();

                if ($info && $info->email) {
                    // ... (keep email logic) ...
                    $color = match($status) {
                        'Present' => '#10b981',
                        'Late' => '#f59e0b',
                        'Absent' => '#ef4444',
                        'Excused' => '#3b82f6',
                        default => '#6b7280'
                    };
                    
                    sendEmailNotification(
                        $info->email,
                        'Attendance Update',
                        'Your Attendance has been marked!',
                        "Hi {$info->name}, your attendance for <b>{$info->mass_type}</b> on <b>" . date('M d, Y', strtotime($info->mass_date)) . "</b> has been marked as <b style='color:{$color}'>{$status}</b>."
                    );

                    // --- START SUSPENSION LOGIC ---
                    if ($status === 'Absent' && stripos($info->mass_type, 'Meeting') === false) {
                        // Count absences for this month (excluding meetings)
                        $this->db->query("
                            SELECT COUNT(*) as count 
                            FROM attendance a
                            JOIN schedules sch ON a.schedule_id = sch.id
                            WHERE a.server_id = :sid 
                            AND a.status = 'Absent'
                            AND MONTH(sch.mass_date) = MONTH(CURRENT_DATE())
                            AND YEAR(sch.mass_date) = YEAR(CURRENT_DATE())
                            AND sch.mass_type NOT LIKE '%Meeting%'
                        ");
                        $this->db->bind(':sid', $info->server_id);
                        $absenceCount = $this->db->single()->count;

                        if ($absenceCount == 2) {
                            // WARNING
                            sendEmailNotification(
                                $info->email,
                                'URGENT: Attendance Warning',
                                'Second Absence Recorded',
                                "Hi {$info->name}, you have recorded your <b>2nd absence</b> this month. Please be reminded that a 3rd absence will result in an automatic 30-day suspension from joining schedules. Keep serving!"
                            );
                        } elseif ($absenceCount >= 3) {
                            // SUSPENSION
                            $until = date('Y-m-d', strtotime('+30 days'));
                            $this->serverRepo->suspendServer($info->server_id, $until);
                            
                            sendEmailNotification(
                                $info->email,
                                'Account Suspended',
                                'Automatic Suspension Triggered',
                                "Hi {$info->name}, you have recorded your <b>3rd absence</b> this month. As per system rules, your account has been <b>Suspended until " . date('M d, Y', strtotime($until)) . "</b>. You will not be able to join new schedules during this period."
                            );
                        }
                    }
                    // --- END SUSPENSION LOGIC ---
                }

                $logName = $info ? $info->name : "ID: $id";
                logAction('Update', 'Attendance', "Updated attendance for $logName to $status");
                setFlash('msg_success', 'Attendance updated.');
            } else {
                setFlash('msg_error', 'Update failed.');
            }
            redirect("attendance?date=$date&page=$page&search=" . urlencode($search));
        }
    }

    public function downloadReport() {
        $dateInput = $_GET['date'] ?? date('Y-m-d');
        $month = date('m', strtotime($dateInput));
        $year = date('Y', strtotime($dateInput));
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Load Dompdf
        $autoloadPath = __DIR__ . '/../../vendor/autoload.php';
        if (file_exists($autoloadPath)) {
            require_once $autoloadPath;
        }

        // Fetch Data
        $allServers = $this->serverRepo->getAll(); // Fetch ALL servers
        $rawLogs = $this->attendanceRepo->getMonthlyAttendance($month, $year);
        
        // Process Data into Grid
        $reportData = [];
        $serverNames = [];
        
        // 1. Initialize all servers
        foreach($allServers as $svr) {
            if ($svr->status === 'Active') { // Only active servers
                $serverNames[$svr->id] = $svr->name;
            }
        }

        // 2. Map Attendance
        foreach($rawLogs as $row) {
            $sid = $row->server_id;
            // Only process if server is still in the active list
            if (isset($serverNames[$sid])) {
                $day = $row->day;
                
                $status = $row->status;
                $code = '';

                // Only show finalized attendance statuses
                if ($status === 'Present') $code = 'P';
                elseif ($status === 'Late') $code = 'L';
                elseif ($status === 'Absent') $code = 'A';
                elseif ($status === 'Excused') $code = 'E';

                if ($code) {
                    $reportData[$sid][$day] = $code; 
                }
            }
        }

        // Sort servers by name (alphabetical)
        asort($serverNames);

        // Prepare Logos
        $minLogoPath = __DIR__ . '/../../public/images/logo.png';
        $parLogoPath = __DIR__ . '/../../public/images/parish-logo.png';
        
        $minLogoData = '';
        if (file_exists($minLogoPath)) {
            $type = pathinfo($minLogoPath, PATHINFO_EXTENSION);
            $minLogoData = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($minLogoPath));
        }

        $parLogoData = '';
        if (file_exists($parLogoPath)) {
            $type = pathinfo($parLogoPath, PATHINFO_EXTENSION);
            $parLogoData = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($parLogoPath));
        }

        $monthName = date('F Y', strtotime($dateInput));

        $html = '
        <html>
        <head>
            <style>
                @page { margin: 20px; }
                body { font-family: Arial, sans-serif; font-size: 9px; color: #000; }
                
                .header-table { width: 100%; border: none; margin-bottom: 10px; }
                .header-side { width: 15%; text-align: center; vertical-align: middle; }
                .header-center { width: 70%; text-align: center; vertical-align: middle; }
                
                h1 { margin: 0; font-size: 14px; font-weight: bold; text-transform: uppercase; }
                h2 { margin: 2px 0; font-size: 11px; font-weight: normal; }
                .sub-text { font-size: 9px; margin: 0; }
                
                .report-title { font-size: 14px; font-weight: bold; text-decoration: underline; text-transform: uppercase; margin-top: 5px; }
                
                .logo { height: 55px; width: auto; }
                
                table.data-table { width: 100%; border-collapse: collapse; margin-top: 5px; table-layout: fixed; }
                table.data-table th, table.data-table td { border: 1px solid #000; padding: 2px; vertical-align: middle; text-align: center; }
                table.data-table th { background-color: #f2f2f2; font-weight: bold; height: 18px; font-size: 8px; }
                table.data-table td { height: 16px; }
                
                .col-name { text-align: left !important; padding-left: 5px !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 8px; }
                
                .status-P { color: #059669; font-weight: bold; }
                .status-L { color: #d97706; font-weight: bold; }
                .status-A { color: #dc2626; font-weight: bold; }
                .status-E { color: #2563eb; font-weight: bold; }
                
                .footer { margin-top: 20px; width: 100%; }
                .legend { margin-top: 8px; font-size: 8px; border: 1px solid #ddd; padding: 5px; width: fit-content; }
            </style>
        </head>
        <body>
            <table class="header-table">
                <tr>
                    <td class="header-side">
                        ' . ($minLogoData ? '<img src="' . $minLogoData . '" class="logo">' : '') . '
                    </td>
                    <td class="header-center">
                        <div style="font-weight: bold; font-size: 11px;">Ministry of Altar Servers</div>
                        <div style="font-size: 10px;">SACRED HEART OF JESUS PARISH</div>
                        <div style="font-size: 8px;">Sto. Niño, Marikina City</div>
                        <div class="report-title">ATTENDANCE MONITORING RECORD</div>
                        <div style="font-size: 10px; font-weight: bold; margin-top: 2px;">' . $monthName . '</div>
                    </td>
                    <td class="header-side">
                        ' . ($parLogoData ? '<img src="' . $parLogoData . '" class="logo">' : '') . '
                    </td>
                </tr>
            </table>

            <table class="data-table">
                <thead>
                    <tr>
                        <th width="25">#</th>
                        <th width="180">NAME</th>';
        
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $html .= '<th>' . $i . '</th>';
        }

        $html .= '  </tr>
                </thead>
                <tbody>';
        
        if (empty($serverNames)) {
            $html .= '<tr><td colspan="' . ($daysInMonth + 2) . '" style="padding: 20px;">No active servers found.</td></tr>';
        } else {
            $count = 1;
            foreach ($serverNames as $sid => $name) {
                $html .= '<tr>
                    <td>' . $count++ . '</td>
                    <td class="col-name"><b>' . htmlspecialchars(strtoupper($name)) . '</b></td>';
                
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $status = $reportData[$sid][$d] ?? '';
                    $class = $status ? 'status-' . $status : '';
                    $html .= '<td class="' . $class . '">' . $status . '</td>';
                }
                
                $html .= '</tr>';
            }
        }

        $html .= '
                </tbody>
            </table>
            
            <div class="legend">
                <b>Legend:</b> &nbsp; 
                <span class="status-P">P</span> Present &nbsp; 
                <span class="status-L">L</span> Late &nbsp; 
                <span class="status-A">A</span> Absent &nbsp; 
                <span class="status-E">E</span> Excused
            </div>

            <div class="footer">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="width: 50%; border: none; vertical-align: top;">
                            <div style="font-size: 8px; margin-bottom: 25px;">Prepared by:</div>
                            <div style="border-bottom: 1px solid #000; width: 180px; text-align: center; font-weight: bold; font-size: 9px;">Bro. BENAIKA LORENZO PARONABLE</div>
                            <div style="width: 180px; text-align: center; font-size: 8px;">Admin Officer, MAS-SHJP MBS</div>
                        </td>
                        <td style="width: 50%; border: none; vertical-align: top; text-align: right;">
                            <div style="font-size: 8px; margin-bottom: 25px; text-align: left; padding-left: 55%;">Noted by:</div>
                            <div style="float: right;">
                                <div style="border-bottom: 1px solid #000; width: 180px; text-align: center; font-weight: bold; font-size: 9px;">Bro. KYLE VINCENT MADRIAGA</div>
                                <div style="width: 180px; text-align: center; font-size: 8px;">Coordinator, MAS-SHJP MBS</div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div style="text-align: right; font-size: 7px; color: #777; margin-top: 10px;">
                    Generated: ' . date('Y-m-d H:i') . '
                </div>
            </div>
        </body>
        </html>';

        // Render PDF
        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $dompdf->stream("Attendance_Report_" . $monthName . ".pdf", ["Attachment" => true]);
        exit;
    }
}
