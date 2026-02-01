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
        // Not implemented for auth
        return [];
    }

    public function getById($id) { return null; }

    public function create(array $data) {
        $this->db->query("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', $data['password']); // Already hashed
        $this->db->bind(':role', $data['role']);
        return $this->db->execute();
    }

    public function update($id, array $data) { return true; }
    public function delete($id) { return true; }
}