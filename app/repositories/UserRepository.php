<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Core\Database;
use App\Models\User;

class UserRepository implements RepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function search($filters = [], $limit = 10, $offset = 0) {
        $sql = "
            SELECT u.*, s.first_name, s.middle_name, s.last_name 
            FROM users u 
            LEFT JOIN servers s ON u.server_id = s.id 
            WHERE u.role != 'Superadmin' AND u.deleted_at IS NULL";
        
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (u.username LIKE :search OR s.first_name LIKE :search OR s.last_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['role'])) {
            $sql .= " AND u.role = :role";
            $params[':role'] = $filters['role'];
        }

        $sql .= " ORDER BY s.last_name ASC, s.first_name ASC LIMIT :limit OFFSET :offset";
        
        $this->db->query($sql);
        foreach ($params as $key => $val) {
            $this->db->bind($key, $val);
        }
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        
        return $this->db->resultSet();
    }

    public function countSearch($filters = []) {
        $sql = "
            SELECT COUNT(*) as count 
            FROM users u 
            LEFT JOIN servers s ON u.server_id = s.id 
            WHERE u.role != 'Superadmin' AND u.deleted_at IS NULL";
        
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (u.username LIKE :search OR s.first_name LIKE :search OR s.last_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['role'])) {
            $sql .= " AND u.role = :role";
            $params[':role'] = $filters['role'];
        }

        $this->db->query($sql);
        foreach ($params as $key => $val) {
            $this->db->bind($key, $val);
        }
        
        $row = $this->db->single();
        return $row ? (int)$row->count : 0;
    }

    public function findByUsername($username) {
        $this->db->query("
            SELECT u.*, CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) as full_name 
            FROM users u 
            LEFT JOIN servers s ON u.server_id = s.id 
            WHERE u.username = :username AND u.deleted_at IS NULL
        ");
        $this->db->bind(':username', $username);
        return $this->db->single();
    }

    public function getAll($limit = 1000, $offset = 0) {
        $this->db->query("
            SELECT u.*, s.first_name, s.middle_name, s.last_name 
            FROM users u 
            LEFT JOIN servers s ON u.server_id = s.id 
            WHERE u.role != 'Superadmin' AND u.deleted_at IS NULL
            ORDER BY s.last_name ASC, s.first_name ASC 
            LIMIT :limit OFFSET :offset
        ");
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    public function countAll() {
        $this->db->query("SELECT COUNT(*) as count FROM users WHERE role != 'Superadmin' AND deleted_at IS NULL");
        $row = $this->db->single();
        return $row ? (int)$row->count : 0;
    }

    public function getById($id) {
        $this->db->query("SELECT * FROM users WHERE id = :id AND deleted_at IS NULL");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getUserProfile($userId) {
        $this->db->query("
            SELECT u.id as user_id, u.username, u.role, u.server_id, u.is_verified, u.can_edit_profile, u.has_edited_profile,
                   s.first_name, s.middle_name, s.last_name, s.nickname, s.dob, s.email, s.phone, s.age, s.address, s.profile_image
            FROM users u
            LEFT JOIN servers s ON u.server_id = s.id
            WHERE u.id = :id AND u.deleted_at IS NULL
        ");
        $this->db->bind(':id', $userId);
        return $this->db->single();
    }

    public function toggleEditPermission($userId, $canEdit) {
        $this->db->query("UPDATE users SET can_edit_profile = :can_edit WHERE id = :id AND deleted_at IS NULL");
        $this->db->bind(':can_edit', $canEdit);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    public function updateEditRestriction($userId, $hasEdited) {
        $this->db->query("UPDATE users SET can_edit_profile = 0, has_edited_profile = :has_edited WHERE id = :id AND deleted_at IS NULL");
        $this->db->bind(':has_edited', $hasEdited);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    public function updateProfile($userId, $data) {
        // Get user and server_id
        $this->db->query("SELECT server_id FROM users WHERE id = :id AND deleted_at IS NULL");
        $this->db->bind(':id', $userId);
        $user = $this->db->single();

        $serverId = $user ? $user->server_id : null;

        // If no server_id, create a new server record first
        if (!$serverId) {
            $this->db->query("INSERT INTO servers (first_name, last_name, phone, email, address, status) VALUES (:fname, :lname, :phone, :email, :address, 'Active')");
            $this->db->bind(':fname', $data['first_name']);
            $this->db->bind(':lname', $data['last_name']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':address', $data['address']);
            
            if ($this->db->execute()) {
                $serverId = $this->db->lastInsertId();
                // Link to user
                $this->db->query("UPDATE users SET server_id = :sid WHERE id = :uid");
                $this->db->bind(':sid', $serverId);
                $this->db->bind(':uid', $userId);
                $this->db->execute();
            } else {
                return false;
            }
        }

        if ($serverId) {
            $this->db->query("UPDATE servers SET first_name = :fname, middle_name = :mname, last_name = :lname, nickname = :nickname, dob = :dob, age = :age, address = :address, phone = :phone, email = :email WHERE id = :id AND deleted_at IS NULL");
            $this->db->bind(':id', $serverId);
            $this->db->bind(':fname', $data['first_name']);
            $this->db->bind(':mname', $data['middle_name'] ?? '');
            $this->db->bind(':lname', $data['last_name']);
            $this->db->bind(':nickname', $data['nickname'] ?? null);
            $this->db->bind(':dob', $data['dob'] ?? null);
            $this->db->bind(':age', $data['age']);
            $this->db->bind(':address', $data['address']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':email', $data['email']);
            $this->db->execute();

            // Set verified if all critical info provided
            if (!empty($data['first_name']) && !empty($data['last_name']) && !empty($data['nickname']) && !empty($data['dob']) && !empty($data['phone']) && !empty($data['email']) && !empty($data['address'])) {
                $this->db->query("UPDATE users SET is_verified = 1 WHERE id = :uid");
                $this->db->bind(':uid', $userId);
                $this->db->execute();
                $_SESSION['is_verified'] = 1; // Update session
            }
        }
        
        // Handle Image Upload if provided
        if (isset($data['profile_image']) && $serverId) {
            $this->db->query("UPDATE servers SET profile_image = :img WHERE id = :id AND deleted_at IS NULL");
            $this->db->bind(':id', $serverId);
            $this->db->bind(':img', $data['profile_image']);
            $this->db->execute();
        }
        
        return true;
    }

    public function create(array $data) {
        $this->db->query("INSERT INTO users (username, password, role, server_id, force_password_reset) VALUES (:username, :password, :role, :server_id, :force_reset)");
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', $data['password']); // Already hashed
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':server_id', $data['server_id'] ?? null);
        $this->db->bind(':force_reset', $data['force_password_reset'] ?? 0);
        return $this->db->execute();
    }

    public function update($id, array $data) {
        $fields = [];
        if (isset($data['password'])) $fields[] = "password = :password";
        if (isset($data['role'])) $fields[] = "role = :role";
        if (isset($data['force_password_reset'])) $fields[] = "force_password_reset = :force_reset";

        if (empty($fields)) return false;

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id AND deleted_at IS NULL";
        $this->db->query($sql);

        if (isset($data['password'])) $this->db->bind(':password', $data['password']);
        if (isset($data['role'])) $this->db->bind(':role', $data['role']);
        if (isset($data['force_password_reset'])) $this->db->bind(':force_reset', $data['force_password_reset']);
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    public function delete($id) {
        // Soft Delete
        $this->db->query("SELECT server_id FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        $user = $this->db->single();

        if ($user && $user->server_id) {
            $this->db->query("UPDATE servers SET deleted_at = NOW() WHERE id = :sid");
            $this->db->bind(':sid', $user->server_id);
            $this->db->execute();
        }

        $this->db->query("UPDATE users SET deleted_at = NOW() WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function restore($id) {
        $this->db->query("SELECT server_id FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        $user = $this->db->single();

        if ($user && $user->server_id) {
            $this->db->query("UPDATE servers SET deleted_at = NULL WHERE id = :sid");
            $this->db->bind(':sid', $user->server_id);
            $this->db->execute();
        }

        $this->db->query("UPDATE users SET deleted_at = NULL WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function deletePermanently($id) {
        $this->db->query("SELECT server_id FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        $user = $this->db->single();

        if ($user && $user->server_id) {
            $sid = $user->server_id;
            $this->db->query("DELETE FROM attendance WHERE server_id = :sid");
            $this->db->bind(':sid', $sid);
            $this->db->execute();

            $this->db->query("DELETE FROM excuses WHERE server_id = :sid");
            $this->db->bind(':sid', $sid);
            $this->db->execute();

            $this->db->query("DELETE FROM servers WHERE id = :sid");
            $this->db->bind(':sid', $sid);
            $this->db->execute();
        }

        $this->db->query("DELETE FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getArchived() {
        $this->db->query("
            SELECT u.*, s.first_name, s.last_name 
            FROM users u 
            LEFT JOIN servers s ON u.server_id = s.id 
            WHERE u.deleted_at IS NOT NULL 
            ORDER BY u.deleted_at DESC
        ");
        return $this->db->resultSet();
    }

    public function getAdmins() {
        $this->db->query("
            SELECT u.username, s.email 
            FROM users u
            JOIN servers s ON u.server_id = s.id
            WHERE u.role IN ('Admin', 'Superadmin') AND s.email IS NOT NULL AND s.email != ''
        ");
        return $this->db->resultSet();
    }
}