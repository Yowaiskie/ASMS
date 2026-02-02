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

    public function findByUsername($username) {
        $this->db->query("SELECT * FROM users WHERE username = :username");
        $this->db->bind(':username', $username);
        return $this->db->single();
    }

    public function getAll() {
        $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    public function getById($id) {
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getUserProfile($userId) {
        $this->db->query("
            SELECT u.id as user_id, u.username, u.role, u.server_id,
                   s.name, s.email, s.phone, s.age, s.address, s.profile_image
            FROM users u
            LEFT JOIN servers s ON u.server_id = s.id
            WHERE u.id = :id
        ");
        $this->db->bind(':id', $userId);
        return $this->db->single();
    }

    public function updateProfile($userId, $data) {
        // Get server_id
        $this->db->query("SELECT server_id FROM users WHERE id = :id");
        $this->db->bind(':id', $userId);
        $user = $this->db->single();

        if ($user && $user->server_id) {
            $this->db->query("UPDATE servers SET name = :name, age = :age, address = :address, phone = :phone, email = :email WHERE id = :id");
            $this->db->bind(':id', $user->server_id);
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':age', $data['age']);
            $this->db->bind(':address', $data['address']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':email', $data['email']);
            $this->db->execute();
        }
        
        // Handle Image Upload if provided
        if (isset($data['profile_image']) && $user && $user->server_id) {
            $this->db->query("UPDATE servers SET profile_image = :img WHERE id = :id");
            $this->db->bind(':id', $user->server_id);
            $this->db->bind(':img', $data['profile_image']);
            $this->db->execute();
        }
        
        return true;
    }

    public function create(array $data) {
        $this->db->query("INSERT INTO users (username, password, role, server_id) VALUES (:username, :password, :role, :server_id)");
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', $data['password']); // Already hashed
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':server_id', $data['server_id'] ?? null);
        return $this->db->execute();
    }

    public function update($id, array $data) {
        if (isset($data['password'])) {
            $this->db->query("UPDATE users SET password = :password, role = :role WHERE id = :id");
            $this->db->bind(':password', $data['password']);
        } else {
            $this->db->query("UPDATE users SET role = :role WHERE id = :id");
        }
        
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query("DELETE FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}