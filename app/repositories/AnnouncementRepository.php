<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Core\Database;
use App\Models\Announcement;

class AnnouncementRepository implements RepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $this->db->query("SELECT * FROM announcements ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    public function getById($id) { return null; }

    public function create(array $data) {
        $this->db->query("INSERT INTO announcements (title, category, message, author) VALUES (:title, :category, :message, :author)");
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':message', $data['message']);
        $this->db->bind(':author', $data['author']);
        return $this->db->execute();
    }

    public function update($id, array $data) { return true; }
    
    public function delete($id) {
        $this->db->query("DELETE FROM announcements WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}