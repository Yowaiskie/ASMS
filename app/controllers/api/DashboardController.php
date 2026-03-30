<?php

namespace App\Controllers\Api;

use App\Core\Database;

class DashboardController extends ApiController {
    private $db;

    public function __construct() {
        $this->requireLoginApi();
        $this->db = Database::getInstance();
    }

    public function index() {
        if (($_SESSION['role'] ?? '') === 'User') {
            $userId = $_SESSION['user_id'];

            $this->db->query("SELECT server_id FROM users WHERE id = :id");
            $this->db->bind(':id', $userId);
            $user = $this->db->single();

            if (!$user || !$user->server_id) {
                $this->ok([
                    'stats' => [],
                    'chartData' => [],
                    'nextSchedule' => null,
                    'announcements' => []
                ]);
            }

            $serverId = $user->server_id;

            $this->db->query("
                SELECT a.status, s.mass_type, s.mass_date 
                FROM attendance a 
                JOIN schedules s ON a.schedule_id = s.id 
                WHERE a.server_id = :server_id
            ");
            $this->db->bind(':server_id', $serverId);
            $records = $this->db->resultSet();

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

            $this->db->query("
                SELECT s.* FROM schedules s
                JOIN attendance a ON s.id = a.schedule_id
                WHERE a.server_id = :sid AND s.mass_date >= CURDATE()
                ORDER BY s.mass_date ASC, s.mass_time ASC LIMIT 1
            ");
            $this->db->bind(':sid', $serverId);
            $nextSchedule = $this->db->single();

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
            $this->db->bind(':sid', $serverId);
            $monthlyAbsences = $this->db->single()->count;

            $this->db->query("SELECT * FROM servers WHERE id = :sid");
            $this->db->bind(':sid', $serverId);
            $serverProfile = $this->db->single();

            $this->db->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3");
            $announcements = $this->db->resultSet();

            $this->ok([
                'stats' => [
                    'massRate' => $massRate,
                    'meetingRate' => $meetingRate,
                    'massTotal' => $massTotal,
                    'meetingTotal' => $meetingTotal,
                    'massPresent' => $massPresent,
                    'meetingPresent' => $meetingPresent
                ],
                'server' => $serverProfile,
                'monthlyAbsences' => $monthlyAbsences,
                'nextSchedule' => $nextSchedule,
                'announcements' => $announcements,
                'chartData' => [
                    'labels' => array_values($months),
                    'mass' => array_values($massCounts),
                    'meeting' => array_values($meetingCounts)
                ]
            ]);
        }

        $this->db->query("SELECT COUNT(*) as total FROM servers WHERE status = 'Active'");
        $totalServers = $this->db->single()->total;

        $this->db->query("SELECT COUNT(*) as total FROM schedules WHERE mass_date >= CURDATE()");
        $upcomingMassesCount = $this->db->single()->total;

        $this->db->query("SELECT COUNT(*) as total FROM attendance WHERE DATE(created_at) = CURDATE() AND status = 'Present'");
        $presentTodayCount = $this->db->single()->total;

        $this->db->query("SELECT COUNT(*) as total FROM attendance");
        $totalAttendance = $this->db->single()->total;
        $this->db->query("SELECT COUNT(*) as total FROM attendance WHERE status = 'Present'");
        $totalPresent = $this->db->single()->total;
        $rate = $totalAttendance > 0 ? round(($totalPresent / $totalAttendance) * 100) : 0;

        $this->db->query("
            SELECT a.*, CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) as name 
            FROM attendance a 
            JOIN servers s ON a.server_id = s.id 
            WHERE DATE(a.created_at) = CURDATE() 
            LIMIT 5
        ");
        $todayAttendance = $this->db->resultSet();

        $this->db->query("SELECT * FROM schedules WHERE mass_date >= CURDATE() ORDER BY mass_date ASC LIMIT 3");
        $upcomingSchedules = $this->db->resultSet();

        $this->db->query("SELECT status, COUNT(*) as count FROM attendance GROUP BY status");
        $distData = $this->db->resultSet();
        $distribution = ['Present' => 0, 'Late' => 0, 'Absent' => 0];
        foreach ($distData as $row) {
            $distribution[$row->status] = (int)$row->count;
        }

        $this->ok([
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
        ]);
    }
}
