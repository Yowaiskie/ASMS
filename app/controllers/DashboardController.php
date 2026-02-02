<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class DashboardController extends Controller {
    private $db;

    public function __construct() {
        $this->requireLogin();
        $this->db = Database::getInstance();
    }

    public function index() {
        if (($_SESSION['role'] ?? '') === 'User') {
            // --- USER DASHBOARD LOGIC ---
            $userId = $_SESSION['user_id'];
            
            // Get Server ID
            $this->db->query("SELECT server_id FROM users WHERE id = :id");
            $this->db->bind(':id', $userId);
            $user = $this->db->single();
            
            if (!$user || !$user->server_id) {
                // Fallback
                $this->view('dashboard/user_index', [
                    'pageTitle' => 'My Dashboard', 
                    'title' => 'Dashboard | ASMS',
                    'stats' => [], 
                    'chartData' => [],
                    'nextSchedule' => null,
                    'announcements' => []
                ]);
                return;
            }

            $serverId = $user->server_id;

            // Fetch Attendance Records
            $this->db->query("
                SELECT a.status, s.mass_type, s.mass_date 
                FROM attendance a 
                JOIN schedules s ON a.schedule_id = s.id 
                WHERE a.server_id = :server_id
            ");
            $this->db->bind(':server_id', $serverId);
            $records = $this->db->resultSet();

            // Calculate Stats
            $massTotal = 0;
            $massPresent = 0;
            $meetingTotal = 0;
            $meetingPresent = 0;

            foreach ($records as $r) {
                $isMeeting = stripos($r->mass_type, 'Meeting') !== false;
                
                if ($isMeeting) {
                    $meetingTotal++;
                    if ($r->status === 'Present') $meetingPresent++;
                } else {
                    $massTotal++;
                    if ($r->status === 'Present') $massPresent++;
                }
            }

            $massRate = $massTotal > 0 ? round(($massPresent / $massTotal) * 100) : 0;
            $meetingRate = $meetingTotal > 0 ? round(($meetingPresent / $meetingTotal) * 100) : 0;

            // Prepare Chart Data (Monthly Presence)
            $months = [];
            $massCounts = [];
            $meetingCounts = [];
            
            for ($i = 5; $i >= 0; $i--) {
                $month = date('Y-m', strtotime("-$i months"));
                $label = date('M', strtotime("-$i months"));
                $months[$month] = $label;
                $massCounts[$month] = 0;
                $meetingCounts[$month] = 0;
            }

            foreach ($records as $r) {
                $month = date('Y-m', strtotime($r->mass_date));
                if (isset($months[$month]) && $r->status === 'Present') {
                    if (stripos($r->mass_type, 'Meeting') !== false) {
                        $meetingCounts[$month]++;
                    } else {
                        $massCounts[$month]++;
                    }
                }
            }

            // Next Schedule
            $this->db->query("
                SELECT s.* FROM schedules s
                JOIN attendance a ON s.id = a.schedule_id
                WHERE a.server_id = :sid AND s.mass_date >= CURDATE()
                ORDER BY s.mass_date ASC, s.mass_time ASC LIMIT 1
            ");
            $this->db->bind(':sid', $serverId);
            $nextSchedule = $this->db->single();

            // Recent Announcements
            $this->db->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3");
            $announcements = $this->db->resultSet();

            $this->view('dashboard/user_index', [
                'pageTitle' => 'My Dashboard',
                'title' => 'Dashboard | ASMS',
                'stats' => [
                    'massRate' => $massRate,
                    'meetingRate' => $meetingRate,
                    'massTotal' => $massTotal,
                    'meetingTotal' => $meetingTotal,
                    'massPresent' => $massPresent,
                    'meetingPresent' => $meetingPresent
                ],
                'nextSchedule' => $nextSchedule,
                'announcements' => $announcements,
                'chartData' => [
                    'labels' => array_values($months),
                    'mass' => array_values($massCounts),
                    'meeting' => array_values($meetingCounts)
                ]
            ]);

        } else {
            // --- ADMIN DASHBOARD LOGIC (Existing) ---
            // Fetch Real Stats
            $this->db->query("SELECT COUNT(*) as total FROM servers WHERE status = 'Active'");
            $totalServers = $this->db->single()->total;

            $this->db->query("SELECT COUNT(*) as total FROM schedules WHERE mass_date >= CURDATE()");
            $upcomingMassesCount = $this->db->single()->total;

            $this->db->query("SELECT COUNT(*) as total FROM attendance WHERE DATE(created_at) = CURDATE() AND status = 'Present'");
            $presentTodayCount = $this->db->single()->total;

            // Calculate Overall Attendance Rate
            $this->db->query("SELECT COUNT(*) as total FROM attendance");
            $totalAttendance = $this->db->single()->total;
            $this->db->query("SELECT COUNT(*) as total FROM attendance WHERE status = 'Present'");
            $totalPresent = $this->db->single()->total;
            $rate = $totalAttendance > 0 ? round(($totalPresent / $totalAttendance) * 100) : 0;

            // Fetch Today's Attendance List
            $this->db->query("
                SELECT a.*, s.name 
                FROM attendance a 
                JOIN servers s ON a.server_id = s.id 
                WHERE DATE(a.created_at) = CURDATE() 
                LIMIT 5
            ");
            $todayAttendance = $this->db->resultSet();

            // Fetch Upcoming Schedules
            $this->db->query("SELECT * FROM schedules WHERE mass_date >= CURDATE() ORDER BY mass_date ASC LIMIT 3");
            $upcomingSchedules = $this->db->resultSet();

            // Chart Data: Status Distribution
            $this->db->query("SELECT status, COUNT(*) as count FROM attendance GROUP BY status");
            $distData = $this->db->resultSet();
            $distribution = ['Present' => 0, 'Late' => 0, 'Absent' => 0];
            foreach($distData as $row) {
                $distribution[$row->status] = (int)$row->count;
            }

            $data = [
                'pageTitle' => 'Dashboard Overview',
                'title' => 'Dashboard | ASMS',
                'stats' => [
                    'totalServers' => $totalServers,
                    'upcomingMasses' => $upcomingMassesCount,
                    'presentToday' => $presentTodayCount,
                    'attendanceRate' => $rate
                ],
                'todayAttendance' => $todayAttendance,
                'upcomingSchedules' => $upcomingSchedules,
                'chartData' => [
                    'labels' => array_keys($distribution),
                    'values' => array_values($distribution)
                ]
            ];
            
            $this->view('dashboard', $data);
        }
    }
}