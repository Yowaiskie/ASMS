<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Core\Database;
use App\Models\Server;

class ServerRepository implements RepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $this->db->query("SELECT * FROM servers ORDER BY name ASC");
        return $this->db->resultSet();
    }

    public function getById($id) {
        $this->db->query("SELECT * FROM servers WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function create(array $data) {
        $this->db->query("INSERT INTO servers (name, rank, team, status, email) VALUES (:name, :rank, :team, :status, :email)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':rank', $data['rank']);
        $this->db->bind(':team', $data['team']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':email', $data['email']);
        return $this->db->execute();
    }

    public function update($id, array $data) {
        $this->db->query("UPDATE servers SET name = :name, rank = :rank, team = :team, status = :status WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':rank', $data['rank']);
        $this->db->bind(':team', $data['team']);
        $this->db->bind(':status', $data['status']);
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query("DELETE FROM servers WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}