<?php

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\RepositoryInterface;

class PermissionRepository implements RepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $sql = "SELECT * FROM permissions ORDER BY module ASC, action ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // Alias for getAll to maintain compatibility
    public function all() {
        return $this->getAll();
    }

    public function getById($id) {
        $this->db->query("SELECT * FROM permissions WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Alias for getById to maintain compatibility
    public function find($id) {
        return $this->getById($id);
    }

    public function create(array $data) {
        $this->db->query("INSERT INTO permissions (name, module, action, description) VALUES (:name, :module, :action, :description)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':module', $data['module']);
        $this->db->bind(':action', $data['action']);
        $db_description = $data['description'] ?? null;
        $this->db->bind(':description', $db_description);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, array $data) {
        $this->db->query("UPDATE permissions SET name = :name, module = :module, action = :action, description = :description WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':module', $data['module']);
        $this->db->bind(':action', $data['action']);
        $db_description = $data['description'] ?? null;
        $this->db->bind(':description', $db_description);
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query("DELETE FROM permissions WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getModules() {
        $this->db->query("SELECT DISTINCT module FROM permissions ORDER BY module ASC");
        return $this->db->resultSet();
    }

    public function getByModule($module) {
        $this->db->query("SELECT * FROM permissions WHERE module = :module ORDER BY action ASC");
        $this->db->bind(':module', $module);
        return $this->db->resultSet();
    }
}
