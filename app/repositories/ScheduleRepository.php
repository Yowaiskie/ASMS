<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Core\Database;
use App\Models\Schedule;

class ScheduleRepository implements RepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $this->db->query("SELECT * FROM schedules ORDER BY mass_date ASC, mass_time ASC");
        $results = $this->db->resultSet();
        
        // Populate assigned_ids for each schedule
        foreach ($results as $row) {
            $assignments = $this->getAssignments($row->id);
            $row->assigned_ids = array_map(function($a) { return (int)$a->id; }, $assignments);
        }
        
        return $results;
    }

    public function getById($id) {
        $this->db->query("SELECT * FROM schedules WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getByUserId($userId) {
        // Get server_id first
        $this->db->query("SELECT server_id FROM users WHERE id = :user_id");
        $this->db->bind(':user_id', $userId);
        $user = $this->db->single();

        if (!$user || !$user->server_id) {
            return [];
        }

        $this->db->query("
            SELECT s.*, a.status as attendance_status 
            FROM schedules s
            JOIN attendance a ON s.id = a.schedule_id
            WHERE a.server_id = :server_id
            ORDER BY s.mass_date ASC, s.mass_time ASC
        ");
        $this->db->bind(':server_id', $user->server_id);
        return $this->db->resultSet();
    }

    public function getAssignedScheduleIds($userId) {
        $this->db->query("SELECT server_id FROM users WHERE id = :user_id");
        $this->db->bind(':user_id', $userId);
        $user = $this->db->single();

        if (!$user || !$user->server_id) return [];

        $this->db->query("SELECT schedule_id FROM attendance WHERE server_id = :sid");
        $this->db->bind(':sid', $user->server_id);
        $results = $this->db->resultSet();
        return array_map(function($r) { return (int)$r->schedule_id; }, $results);
    }

    public function getByDate($date) {
        $this->db->query("SELECT * FROM schedules WHERE mass_date = :date");
        $this->db->bind(':date', $date);
        return $this->db->resultSet();
    }

    public function create(array $data) {
        $this->db->query("INSERT INTO schedules (mass_type, event_name, color, mass_date, mass_time, status) VALUES (:mass_type, :event_name, :color, :mass_date, :mass_time, :status)");
        $this->db->bind(':mass_type', $data['mass_type']);
        $this->db->bind(':event_name', $data['event_name'] ?? null);
        $this->db->bind(':color', $data['color'] ?? null);
        $this->db->bind(':mass_date', $data['mass_date']);
        $this->db->bind(':mass_time', $data['mass_time']);
        $this->db->bind(':status', $data['status']);
        return $this->db->execute();
    }

    public function update($id, array $data) {
        $this->db->query("UPDATE schedules SET mass_type = :type, event_name = :event_name, color = :color, mass_date = :date, mass_time = :time, status = :status WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':type', $data['mass_type']);
        $this->db->bind(':event_name', $data['event_name'] ?? null);
        $this->db->bind(':color', $data['color'] ?? null);
        $this->db->bind(':date', $data['mass_date']);
        $this->db->bind(':time', $data['mass_time']);
        $this->db->bind(':status', $data['status']);
        return $this->db->execute();
    }

    public function updateStatus($id, $status) {
        $this->db->query("UPDATE schedules SET status = :status WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query("DELETE FROM schedules WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getAssignments($scheduleId) {
        $this->db->query("SELECT s.id, CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) as name, s.rank, a.status 
                          FROM attendance a 
                          JOIN servers s ON a.server_id = s.id 
                          WHERE a.schedule_id = :sid
                          ORDER BY s.last_name ASC, s.first_name ASC");
        $this->db->bind(':sid', $scheduleId);
        return $this->db->resultSet();
    }

    public function getFullAssignments($scheduleId) {
        $this->db->query("
            SELECT 
                CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) as name,
                s.rank, 
                a.status 
            FROM attendance a 
            JOIN servers s ON a.server_id = s.id 
            WHERE a.schedule_id = :id
            ORDER BY s.last_name ASC, s.first_name ASC
        ");
        $this->db->bind(':id', $scheduleId);
        return $this->db->resultSet();
    }

    public function selfAssign($userId, $scheduleId) {
        // 1. Get server_id
        $this->db->query("SELECT server_id FROM users WHERE id = :uid");
        $this->db->bind(':uid', $userId);
        $user = $this->db->single();
        if (!$user || !$user->server_id) return false;

        // 2. Check if already assigned
        $this->db->query("SELECT id FROM attendance WHERE schedule_id = :sid AND server_id = :svid");
        $this->db->bind(':sid', $scheduleId);
        $this->db->bind(':svid', $user->server_id);
        if ($this->db->single()) return true; // Already assigned

        // 3. Assign
        $this->db->query("INSERT INTO attendance (schedule_id, server_id, status) VALUES (:sid, :svid, 'Confirmed')");
        $this->db->bind(':sid', $scheduleId);
        $this->db->bind(':svid', $user->server_id);
        return $this->db->execute();
    }

    public function syncAssignments($scheduleId, array $serverIds) {
        // 1. Get current assigned server IDs
        $currentAssignments = $this->getAssignments($scheduleId);
        $currentIds = array_map(function($a) { return $a->id; }, $currentAssignments);
        
        // 2. To Delete: IDs in current but not in new list
        $toDelete = array_diff($currentIds, $serverIds);
        foreach($toDelete as $sid) {
            $this->db->query("DELETE FROM attendance WHERE schedule_id = :sid AND server_id = :uid");
            $this->db->bind(':sid', $scheduleId);
            $this->db->bind(':uid', $sid);
            $this->db->execute();
        }
        
        // 3. To Add: IDs in new list but not in current
        $toAdd = array_diff($serverIds, $currentIds);
        foreach($toAdd as $sid) {
            $this->db->query("INSERT INTO attendance (schedule_id, server_id, status) VALUES (:sid, :uid, 'Pending')");
            $this->db->bind(':sid', $scheduleId);
            $this->db->bind(':uid', $sid);
            $this->db->execute();
        }
        
        return true;
    }
}