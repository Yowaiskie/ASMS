<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Core\Database;

class ExcuseRepository implements RepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        // Admin use mainly
        $this->db->query("SELECT e.*, s.name as server_name FROM excuses e JOIN servers s ON e.server_id = s.id ORDER BY e.created_at DESC");
        return $this->db->resultSet();
    }

    public function getById($id) {
        $this->db->query("SELECT * FROM excuses WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getByUserId($userId) {
        // Get server_id
        $this->db->query("SELECT server_id FROM users WHERE id = :user_id");
        $this->db->bind(':user_id', $userId);
        $user = $this->db->single();

        if (!$user || !$user->server_id) {
            return [];
        }

        $this->db->query("SELECT * FROM excuses WHERE server_id = :server_id ORDER BY created_at DESC");
        $this->db->bind(':server_id', $user->server_id);
        return $this->db->resultSet();
    }

    public function create(array $data) {
        // Get server_id if not provided (though Controller should provide it)
        if (!isset($data['server_id'])) {
             // Fetch logic here if needed, but better passed from controller
             return false;
        }

        $this->db->query("INSERT INTO excuses (server_id, type, absence_date, absence_time, reason, image_path, status) VALUES (:server_id, :type, :date, :time, :reason, :image, :status)");
        $this->db->bind(':server_id', $data['server_id']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':date', $data['absence_date']);
        $this->db->bind(':time', $data['absence_time']);
        $this->db->bind(':reason', $data['reason']);
        $this->db->bind(':image', $data['image_path']);
        $this->db->bind(':status', 'Pending');
        return $this->db->execute();
    }

    public function update($id, array $data) { return true; }

    public function updateStatus($id, $status) {
        $this->db->query("UPDATE excuses SET status = :status WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    public function delete($id) { 
        $this->db->query("DELETE FROM excuses WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}