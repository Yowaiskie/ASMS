<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Core\Database;
use App\Models\Attendance;

class AttendanceRepository implements RepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        // Joining servers and schedules tables to get readable names and times
        $this->db->query("
            SELECT a.*, s.name as server_name, sch.mass_date, sch.mass_time 
            FROM attendance a
            JOIN servers s ON a.server_id = s.id
            JOIN schedules sch ON a.schedule_id = sch.id
            ORDER BY sch.mass_date DESC
        ");
        
        $results = $this->db->resultSet();
        
        // Map to simpler object structure expected by view
        $logs = [];
        foreach($results as $row) {
            $log = new Attendance();
            $log->id = $row->id;
            $log->server_name = $row->server_name;
            $log->date = $row->mass_date;
            $log->mass_time = $row->mass_time;
            $log->status = $row->status;
            $logs[] = $log;
        }
        return $logs;
    }

    public function getById($id) { return null; }
    public function create(array $data) { return true; }

    public function updateStatus($id, $status) {
        $this->db->query("UPDATE attendance SET status = :status WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    public function update($id, array $data) { return true; }
    public function delete($id) { return true; }
}