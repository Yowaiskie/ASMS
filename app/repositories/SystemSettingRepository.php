<?php

namespace App\Repositories;

use App\Core\Database;

class SystemSettingRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // --- Key-Value Settings ---
    public function get($key, $default = null) {
        $this->db->query("SELECT setting_value FROM system_settings WHERE setting_key = :key");
        $this->db->bind(':key', $key);
        $row = $this->db->single();
        return $row ? $row->setting_value : $default;
    }

    public function set($key, $value) {
        $this->db->query("INSERT INTO system_settings (setting_key, setting_value) VALUES (:key, :val) 
                          ON DUPLICATE KEY UPDATE setting_value = :val2");
        $this->db->bind(':key', $key);
        $this->db->bind(':val', $value);
        $this->db->bind(':val2', $value);
        return $this->db->execute();
    }

    // --- Activity Types ---
    public function getActivityTypes($onlyActive = true) {
        $sql = "SELECT * FROM activity_types";
        if ($onlyActive) $sql .= " WHERE is_active = 1";
        $sql .= " ORDER BY name ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function addActivityType($name, $color = 'blue') {
        $this->db->query("INSERT INTO activity_types (name, default_color) VALUES (:name, :color)");
        $this->db->bind(':name', $name);
        $this->db->bind(':color', $color);
        return $this->db->execute();
    }

    public function deleteActivityType($id) {
        $this->db->query("DELETE FROM activity_types WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // --- Server Ranks ---
    public function getRanks($onlyActive = true) {
        $sql = "SELECT * FROM server_ranks";
        if ($onlyActive) $sql .= " WHERE is_active = 1";
        $sql .= " ORDER BY name ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function addRank($name) {
        $this->db->query("INSERT INTO server_ranks (name) VALUES (:name)");
        $this->db->bind(':name', $name);
        return $this->db->execute();
    }

    public function deleteRank($id) {
        $this->db->query("DELETE FROM server_ranks WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // --- Announcement Categories ---
    public function getCategories($onlyActive = true) {
        $sql = "SELECT * FROM announcement_categories";
        if ($onlyActive) $sql .= " WHERE is_active = 1";
        $sql .= " ORDER BY name ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function addCategory($name) {
        $this->db->query("INSERT INTO announcement_categories (name) VALUES (:name)");
        $this->db->bind(':name', $name);
        return $this->db->execute();
    }

    public function deleteCategory($id) {
        $this->db->query("DELETE FROM announcement_categories WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}