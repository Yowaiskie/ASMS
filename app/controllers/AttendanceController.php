<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\AttendanceRepository;

class AttendanceController extends Controller {
    private $attendanceRepo;

    public function __construct() {
        $this->requireLogin();
        $this->attendanceRepo = new AttendanceRepository();
    }

    public function index() {
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'User') {
            // ... (Keep existing User Logic) ...
            $allRecords = $this->attendanceRepo->getByUserId($_SESSION['user_id']);
            
            $stats = ['Present' => 0, 'Late' => 0, 'Absent' => 0];
            $massRecords = [];
            $meetingRecords = [];

            foreach ($allRecords as $r) {
                // Count Stats
                if (isset($stats[$r->status])) {
                    $stats[$r->status]++;
                }
                
                // Categorize
                if (stripos($r->mass_type, 'Meeting') !== false) {
                    $meetingRecords[] = $r;
                } else {
                    $massRecords[] = $r;
                }
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
            $rawLogs = $this->attendanceRepo->getDailyAttendance($date);
            
            $attendanceList = [];
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
                    } elseif (stripos($type, 'Mass') !== false) {
                        // Assume first mass found is primary, others are extra
                        if ($attendanceList[$sid]['mass'] === null) {
                            $attendanceList[$sid]['mass'] = $row;
                        } else {
                            $attendanceList[$sid]['others'][] = $row;
                        }
                    } else {
                        $attendanceList[$sid]['others'][] = $row;
                    }
                }
            }

            $this->view('attendance/index', [
                'pageTitle' => 'Attendance Management',
                'title' => 'Attendance | ASMS',
                'attendanceList' => $attendanceList,
                'date' => $date
            ]);
        }
    }

    public function update() {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['attendance_id'];
            $status = $_POST['status'];

            if ($this->attendanceRepo->updateStatus($id, $status)) {
                logAction('Update', 'Attendance', "Updated attendance ID: $id to $status");
                setFlash('msg_success', 'Attendance updated.');
            } else {
                setFlash('msg_error', 'Update failed.');
            }
            redirect('attendance');
        }
    }
}