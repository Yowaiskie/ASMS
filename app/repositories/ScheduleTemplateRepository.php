<?php

namespace App\Repositories;

use App\Core\Database;

class ScheduleTemplateRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $this->db->query("SELECT * FROM schedule_templates ORDER BY day_of_week ASC, mass_time ASC");
        return $this->db->resultSet();
    }

    public function getByDay($day) {
        $this->db->query("SELECT * FROM schedule_templates WHERE day_of_week = :day ORDER BY mass_time ASC");
        $this->db->bind(':day', $day);
        return $this->db->resultSet();
    }

    public function hasConflict($day, $time) {
        $this->db->query("SELECT id FROM schedule_templates WHERE day_of_week = :day AND mass_time = :time");
        $this->db->bind(':day', $day);
        $this->db->bind(':time', $time);
        return $this->db->single();
    }

    public function create($data) {
        if ($this->hasConflict($data['day_of_week'], $data['mass_time'])) {
            return false;
        }
        $this->db->query("INSERT INTO schedule_templates (day_of_week, mass_time, mass_type, event_name, color) VALUES (:day, :time, :type, :name, :color)");
        $this->db->bind(':day', $data['day_of_week']);
        $this->db->bind(':time', $data['mass_time']);
        $this->db->bind(':type', $data['mass_type']);
        $this->db->bind(':name', $data['event_name'] ?? null);
        $this->db->bind(':color', $data['color'] ?? 'blue');
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query("DELETE FROM schedule_templates WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function clearDay($day) {
        $this->db->query("DELETE FROM schedule_templates WHERE day_of_week = :day");
        $this->db->bind(':day', $day);
        return $this->db->execute();
    }

    public function update($id, $data) {
        $this->db->query("UPDATE schedule_templates SET mass_time = :time, mass_type = :type, event_name = :name, color = :color WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':time', $data['mass_time']);
        $this->db->bind(':type', $data['mass_type']);
        $this->db->bind(':name', $data['event_name'] ?? null);
        $this->db->bind(':color', $data['color'] ?? 'blue');
        return $this->db->execute();
    }
}