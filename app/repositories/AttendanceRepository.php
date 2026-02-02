<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Core\Database;
use App\Models\Attendance;

class AttendanceRepository implements RepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        // Joining servers and schedules tables to get readable names and times
        $this->db->query("
            SELECT a.*, s.name as server_name, sch.mass_date, sch.mass_time 
            FROM attendance a
            JOIN servers s ON a.server_id = s.id
            JOIN schedules sch ON a.schedule_id = sch.id
            ORDER BY sch.mass_date DESC
        ");
        
        $results = $this->db->resultSet();
        
        // Map to simpler object structure expected by view
        $logs = [];
        foreach($results as $row) {
            $log = new Attendance();
            $log->id = $row->id;
            $log->server_name = $row->server_name;
            $log->date = $row->mass_date;
            $log->mass_time = $row->mass_time;
            $log->status = $row->status;
            $logs[] = $log;
        }
        return $logs;
    }

    public function getById($id) { return null; }
    public function create(array $data) { return true; }

    public function updateStatus($id, $status) {
        $this->db->query("UPDATE attendance SET status = :status WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    public function getByUserId($userId) {
        // Fetch server_id for the user first (assuming 1:1 relation users -> servers)
        $this->db->query("SELECT server_id FROM users WHERE id = :user_id");
        $this->db->bind(':user_id', $userId);
        $user = $this->db->single();

        if (!$user || !$user->server_id) {
            return [];
        }

        $this->db->query("
            SELECT a.*, s.mass_type, s.mass_date, s.mass_time 
            FROM attendance a 
            JOIN schedules s ON a.schedule_id = s.id 
            WHERE a.server_id = :server_id 
            ORDER BY s.mass_date DESC
        ");
        $this->db->bind(':server_id', $user->server_id);
        return $this->db->resultSet();
    }

    public function getDailyAttendance($date) {
        $this->db->query("
            SELECT 
                s.id as server_id, 
                s.name, 
                a.id as attendance_id,
                a.status,
                sch.id as schedule_id,
                sch.mass_type,
                sch.mass_time
            FROM servers s
            LEFT JOIN attendance a ON s.id = a.server_id
            LEFT JOIN schedules sch ON a.schedule_id = sch.id AND sch.mass_date = :date
            WHERE s.status = 'Active'
            ORDER BY s.name ASC
        ");
        $this->db->bind(':date', $date);
        return $this->db->resultSet();
    }

    public function update($id, array $data) { return true; }
    public function delete($id) { return true; }
}