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
        $this->db->query("
            SELECT a.*, CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) as server_name, sch.mass_date, sch.mass_time 
            FROM attendance a
            JOIN servers s ON a.server_id = s.id
            JOIN schedules sch ON a.schedule_id = sch.id
            WHERE s.deleted_at IS NULL
            ORDER BY sch.mass_date DESC
        ");
        
        $results = $this->db->resultSet();
        
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

    public function assign($scheduleId, $serverId, $status) {
        $this->db->query("INSERT INTO attendance (schedule_id, server_id, status) VALUES (:sid, :uid, :status)");
        $this->db->bind(':sid', $scheduleId);
        $this->db->bind(':uid', $serverId);
        $this->db->bind(':status', $status);
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function getByUserId($userId) {
        $this->db->query("SELECT server_id FROM users WHERE id = :user_id AND deleted_at IS NULL");
        $this->db->bind(':user_id', $userId);
        $user = $this->db->single();

        if (!$user || !$user->server_id) {
            return [];
        }

        $this->db->query("
            SELECT a.*, s.mass_type, s.mass_date, s.mass_time 
            FROM attendance a 
            JOIN schedules s ON a.schedule_id = s.id 
            JOIN servers srv ON a.server_id = srv.id
            WHERE a.server_id = :server_id AND srv.deleted_at IS NULL
            ORDER BY s.mass_date DESC
        ");
        $this->db->bind(':server_id', $user->server_id);
        return $this->db->resultSet();
    }

    public function getDailyAttendance($date, $search = '', $limit = 100, $offset = 0) {
        $sql = "
            SELECT 
                s.id as server_id, 
                CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) as name, 
                a.id as attendance_id,
                a.status,
                sch.id as schedule_id,
                sch.mass_type,
                sch.mass_time
            FROM (
                SELECT id, first_name, middle_name, last_name FROM servers 
                WHERE status = 'Active' AND deleted_at IS NULL
                " . (!empty($search) ? "AND (first_name LIKE :search OR last_name LIKE :search)" : "") . "
                ORDER BY last_name ASC, first_name ASC 
                LIMIT :limit OFFSET :offset
            ) s
            LEFT JOIN attendance a ON s.id = a.server_id
            LEFT JOIN schedules sch ON a.schedule_id = sch.id AND sch.mass_date = :date
            ORDER BY s.last_name ASC, s.first_name ASC
        ";
        
        $this->db->query($sql);
        $this->db->bind(':date', $date);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        if (!empty($search)) {
            $this->db->bind(':search', "%$search%");
        }
        
        return $this->db->resultSet();
    }

    public function countActiveServers($search = '') {
        $sql = "SELECT COUNT(*) as count FROM servers WHERE status = 'Active' AND deleted_at IS NULL";
        if (!empty($search)) {
            $sql .= " AND (first_name LIKE :search OR last_name LIKE :search)";
        }
        $this->db->query($sql);
        if (!empty($search)) {
            $this->db->bind(':search', "%$search%");
        }
        $row = $this->db->single();
        return $row ? (int)$row->count : 0;
    }

    public function getMonthlyAttendance($month, $year) {
        $this->db->query("
            SELECT 
                s.id as server_id, 
                CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) as name, 
                a.status,
                DAY(sch.mass_date) as day,
                sch.mass_type
            FROM servers s
            JOIN attendance a ON s.id = a.server_id
            JOIN schedules sch ON a.schedule_id = sch.id
            WHERE MONTH(sch.mass_date) = :month 
            AND YEAR(sch.mass_date) = :year
            AND s.status = 'Active' AND s.deleted_at IS NULL
            ORDER BY s.last_name ASC, s.first_name ASC, sch.mass_date ASC
        ");
        $this->db->bind(':month', $month);
        $this->db->bind(':year', $year);
        return $this->db->resultSet();
    }

    public function update($id, array $data) { return true; }
    public function delete($id) { return true; }
}