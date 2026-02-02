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
        return $this->db->resultSet();
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
        $this->db->query("SELECT server_id FROM attendance WHERE schedule_id = :id");
        $this->db->bind(':id', $scheduleId);
        $results = $this->db->resultSet();
        return array_map(function($r) { return $r->server_id; }, $results);
    }

    public function getFullAssignments($scheduleId) {
        $this->db->query("
            SELECT s.name, s.rank, a.status 
            FROM attendance a 
            JOIN servers s ON a.server_id = s.id 
            WHERE a.schedule_id = :id
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
        // 1. Remove assignments not in the list
        if (empty($serverIds)) {
            $this->db->query("DELETE FROM attendance WHERE schedule_id = :id");
            $this->db->bind(':id', $scheduleId);
            $this->db->execute();
        } else {
            // Create placeholders for NOT IN clause
            $placeholders = implode(',', array_fill(0, count($serverIds), '?'));
            $sql = "DELETE FROM attendance WHERE schedule_id = ? AND server_id NOT IN ($placeholders)";
            
            // params: scheduleId, ...serverIds
            $params = array_merge([$scheduleId], $serverIds);
            
            // Execute raw PDO for variable args if needed, or use a loop. 
            // Since Core\Database might not support variable binds easily in one go with IN clause properly without abstraction:
            // Simpler approach: Fetch current, compare in PHP.
            
            // Actually, let's just do it cleanly:
            
            // Get current
            $current = $this->getAssignments($scheduleId);
            
            // To Delete
            $toDelete = array_diff($current, $serverIds);
            foreach($toDelete as $sid) {
                $this->db->query("DELETE FROM attendance WHERE schedule_id = :sid AND server_id = :uid");
                $this->db->bind(':sid', $scheduleId);
                $this->db->bind(':uid', $sid);
                $this->db->execute();
            }
            
            // To Add
            $toAdd = array_diff($serverIds, $current);
            foreach($toAdd as $sid) {
                $this->db->query("INSERT INTO attendance (schedule_id, server_id, status) VALUES (:sid, :uid, 'Pending')");
                $this->db->bind(':sid', $scheduleId);
                $this->db->bind(':uid', $sid);
                $this->db->execute();
            }
        }
        return true;
    }
}