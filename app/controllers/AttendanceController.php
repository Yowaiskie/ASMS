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
                    } else {
                        // Any other activity (Mass, Event, etc.) goes to the main column
                        if ($attendanceList[$sid]['mass'] === null) {
                            $attendanceList[$sid]['mass'] = $row;
                        } else {
                            // If they have multiple assignments, we can handle it or just overwrite
                            // For now, let's keep it simple.
                        }
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
            $date = $_POST['date'] ?? date('Y-m-d');

            if ($this->attendanceRepo->updateStatus($id, $status)) {
                // Email Notification
                $db = \App\Core\Database::getInstance();
                $db->query("
                    SELECT s.email, s.name, sch.mass_type, sch.mass_date 
                    FROM attendance a 
                    JOIN servers s ON a.server_id = s.id 
                    JOIN schedules sch ON a.schedule_id = sch.id 
                    WHERE a.id = :id
                ");
                $db->bind(':id', $id);
                $info = $db->single();

                if ($info && $info->email) {
                    $color = $status === 'Present' ? '#10b981' : ($status === 'Late' ? '#f59e0b' : '#ef4444');
                    sendEmailNotification(
                        $info->email,
                        'Attendance Update',
                        'Your Attendance has been marked!',
                        "Hi {$info->name}, your attendance for <b>{$info->mass_type}</b> on <b>" . date('M d, Y', strtotime($info->mass_date)) . "</b> has been marked as <b style='color:{$color}'>{$status}</b>."
                    );
                }

                logAction('Update', 'Attendance', "Updated attendance ID: $id to $status");
                setFlash('msg_success', 'Attendance updated.');
            } else {
                setFlash('msg_error', 'Update failed.');
            }
            redirect('attendance?date=' . $date);
        }
    }
}