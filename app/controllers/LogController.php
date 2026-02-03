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
        $search = trim($_GET['search'] ?? '');
        $role = trim($_GET['role'] ?? '');
        $action = trim($_GET['action'] ?? '');
        $startDate = trim($_GET['start_date'] ?? '');
        $endDate = trim($_GET['end_date'] ?? '');

        $sql = "
            SELECT l.*, u.username, u.role as user_role, s.name as real_name
            FROM logs l 
            LEFT JOIN users u ON l.user_id = u.id 
            LEFT JOIN servers s ON u.server_id = s.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (l.description LIKE :search OR l.ip_address LIKE :search OR u.username LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if (!empty($role)) {
            $sql .= " AND u.role = :role";
            $params[':role'] = $role;
        }

        if (!empty($action)) {
            $sql .= " AND l.action = :action";
            $params[':action'] = $action;
        }

        if (!empty($startDate)) {
            $sql .= " AND DATE(l.created_at) >= :start_date";
            $params[':start_date'] = $startDate;
        }

        if (!empty($endDate)) {
            $sql .= " AND DATE(l.created_at) <= :end_date";
            $params[':end_date'] = $endDate;
        }

        $sql .= " ORDER BY l.created_at DESC";

        if (isset($_GET['export'])) {
            $this->export($sql, $params);
            return;
        }

        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Get Total Count for Pagination
        $countSql = "SELECT COUNT(*) as count FROM logs l LEFT JOIN users u ON l.user_id = u.id WHERE 1=1";
        if (!empty($search)) $countSql .= " AND (l.description LIKE :search OR l.ip_address LIKE :search OR u.username LIKE :search)";
        if (!empty($role)) $countSql .= " AND u.role = :role";
        if (!empty($action)) $countSql .= " AND l.action = :action";
        if (!empty($startDate)) $countSql .= " AND DATE(l.created_at) >= :start_date";
        if (!empty($endDate)) $countSql .= " AND DATE(l.created_at) <= :end_date";

        $this->db->query($countSql);
        foreach ($params as $key => $val) {
            $this->db->bind($key, $val);
        }
        $totalRecords = $this->db->single()->count;
        $totalPages = ceil($totalRecords / $limit);

        // Main Query with Limit
        $sql .= " LIMIT :limit OFFSET :offset";
        $this->db->query($sql);
        foreach ($params as $key => $val) {
            $this->db->bind($key, $val);
        }
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        
        $logs = $this->db->resultSet();

        $this->view('logs/index', [
            'pageTitle' => 'Audit Logs',
            'title' => 'Logs | ASMS',
            'logs' => $logs,
            'filters' => [
                'search' => $search,
                'role' => $role,
                'action' => $action,
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'pagination' => [
                'page' => $page,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords
            ]
        ]);
    }

    private function export($sql, $params) {
        $this->db->query($sql);
        foreach ($params as $key => $val) {
            $this->db->bind($key, $val);
        }
        $logs = $this->db->resultSet();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="audit_logs_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Timestamp', 'Username', 'Role', 'Action', 'Module', 'Description', 'IP Address']);

        foreach ($logs as $log) {
            fputcsv($output, [
                $log->created_at,
                $log->username ?? 'System',
                $log->user_role ?? 'N/A',
                $log->action,
                $log->module,
                $log->description,
                $log->ip_address
            ]);
        }

        fclose($output);
        exit;
    }
}