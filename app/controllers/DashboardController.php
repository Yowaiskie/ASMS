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