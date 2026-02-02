<?php

namespace App\Models;

use App\Core\Database;

class SystemSetting {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $this->db->query("SELECT * FROM system_settings");
        $results = $this->db->resultSet();
        $settings = [];
        foreach ($results as $row) {
            $settings[$row->setting_key] = $row->setting_value;
        }
        return $settings;
    }

    public function update($key, $value) {
        $this->db->query("INSERT INTO system_settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value");
        $this->db->bind(':key', $key);
        $this->db->bind(':value', $value);
        return $this->db->execute();
    }

    public static function get($key, $default = null) {
        try {
            $db = Database::getInstance();
            $db->query("SELECT setting_value FROM system_settings WHERE setting_key = :key");
            $db->bind(':key', $key);
            $row = $db->single();
            return $row ? $row->setting_value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}