<?php

namespace App\Repositories;

use App\Core\Database;

class SchedulePresetRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $this->db->query("SELECT * FROM schedule_presets ORDER BY preset_group ASC, day_of_week ASC, mass_time ASC");
        return $this->db->resultSet();
    }

    public function getById($id) {
        $this->db->query("SELECT * FROM schedule_presets WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function create($data) {
        $this->db->query("INSERT INTO schedule_presets (name, preset_group, day_of_week, mass_time, mass_type, event_name, color) 
                         VALUES (:name, :group, :day, :time, :type, :event, :color)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':group', $data['preset_group'] ?? 'General');
        $this->db->bind(':day', $data['day_of_week']);
        $this->db->bind(':time', $data['mass_time']);
        $this->db->bind(':type', $data['mass_type']);
        $this->db->bind(':event', $data['event_name'] ?? null);
        $this->db->bind(':color', $data['color'] ?? 'blue');
        return $this->db->execute();
    }

    public function update($id, $data) {
        $this->db->query("UPDATE schedule_presets SET name = :name, preset_group = :group, day_of_week = :day, mass_time = :time, mass_type = :type, event_name = :event, color = :color WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':group', $data['preset_group'] ?? 'General');
        $this->db->bind(':day', $data['day_of_week']);
        $this->db->bind(':time', $data['mass_time']);
        $this->db->bind(':type', $data['mass_type']);
        $this->db->bind(':event', $data['event_name'] ?? null);
        $this->db->bind(':color', $data['color'] ?? 'blue');
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query("DELETE FROM schedule_presets WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}