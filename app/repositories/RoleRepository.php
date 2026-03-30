<?php

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\RepositoryInterface;

class RoleRepository implements RepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $sql = "SELECT * FROM roles ORDER BY name ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // Alias for getAll to maintain compatibility
    public function all() {
        return $this->getAll();
    }

    public function getById($id) {
        $this->db->query("SELECT * FROM roles WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Alias for getById to maintain compatibility
    public function find($id) {
        return $this->getById($id);
    }

    public function create(array $data) {
        $this->db->query("INSERT INTO roles (name, description) VALUES (:name, :description)");
        $this->db->bind(':name', $data['name']);
        $db_description = $data['description'] ?? null;
        $this->db->bind(':description', $db_description);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, array $data) {
        $this->db->query("UPDATE roles SET name = :name, description = :description WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $db_description = $data['description'] ?? null;
        $this->db->bind(':description', $db_description);
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query("DELETE FROM roles WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Permission Management
    public function getPermissionsByRole($role_id) {
        $sql = "SELECT p.* FROM permissions p 
                JOIN role_permissions rp ON p.id = rp.permission_id 
                WHERE rp.role_id = :role_id";
        $this->db->query($sql);
        $this->db->bind(':role_id', $role_id);
        return $this->db->resultSet();
    }

    public function syncPermissions($role_id, $permission_ids) {
        // Clear existing permissions
        $this->db->query("DELETE FROM role_permissions WHERE role_id = :role_id");
        $this->db->bind(':role_id', $role_id);
        $this->db->execute();

        // Add new permissions
        if (!empty($permission_ids)) {
            foreach ($permission_ids as $p_id) {
                $this->db->query("INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :p_id)");
                $this->db->bind(':role_id', $role_id);
                $this->db->bind(':p_id', $p_id);
                $this->db->execute();
            }
        }
        return true;
    }

    public function getRoleByName($name) {
        $this->db->query("SELECT * FROM roles WHERE name = :name");
        $this->db->bind(':name', $name);
        return $this->db->single();
    }
}
