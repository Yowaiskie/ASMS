<?php

namespace App\Repositories;

use App\Core\Database;

class LiturgicalSeasonRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $this->db->query("SELECT * FROM liturgical_seasons ORDER BY start_date DESC");
        return $this->db->resultSet();
    }

    public function getSeasonByDate($date) {
        $this->db->query("SELECT * FROM liturgical_seasons 
                         WHERE :date BETWEEN start_date AND end_date 
                         AND is_active = 1 
                         LIMIT 1");
        $this->db->bind(':date', $date);
        return $this->db->single();
    }

    public function create($data) {
        $this->db->query("INSERT INTO liturgical_seasons (name, start_date, end_date, color, exempted_types) 
                         VALUES (:name, :start, :end, :color, :exempted)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':start', $data['start_date']);
        $this->db->bind(':end', $data['end_date']);
        $this->db->bind(':color', $data['color']);
        $this->db->bind(':exempted', $data['exempted_types'] ?? null);
        return $this->db->execute();
    }

    public function update($id, $data) {
        $this->db->query("UPDATE liturgical_seasons SET name = :name, start_date = :start, end_date = :end, color = :color, exempted_types = :exempted WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':start', $data['start_date']);
        $this->db->bind(':end', $data['end_date']);
        $this->db->bind(':color', $data['color']);
        $this->db->bind(':exempted', $data['exempted_types'] ?? null);
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query("DELETE FROM liturgical_seasons WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}