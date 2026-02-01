<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class LogController extends Controller {
    private $db;

    public function __construct() {
        $this->requireLogin();
        $this->db = Database::getInstance();
    }

    public function index() {
        $this->db->query("
            SELECT l.*, u.username 
            FROM logs l 
            LEFT JOIN users u ON l.user_id = u.id 
            ORDER BY l.created_at DESC 
            LIMIT 50
        ");
        $logs = $this->db->resultSet();

        $this->view('logs/index', [
            'pageTitle' => 'Audit Logs',
            'title' => 'Logs | ASMS',
            'logs' => $logs
        ]);
    }
}