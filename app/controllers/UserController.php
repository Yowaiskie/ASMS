<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepository;

class UserController extends Controller {
    private $userRepo;

    public function __construct() {
        $this->requireLogin();
        // Restrict to Superadmin
        if (($_SESSION['role'] ?? '') !== 'Superadmin') {
            setFlash('msg_error', 'Unauthorized access.');
            redirect('dashboard');
            exit;
        }
        $this->userRepo = new UserRepository();
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $users = $this->userRepo->getAll($limit, $offset);
        $totalRecords = $this->userRepo->countAll();
        $totalPages = ceil($totalRecords / $limit);

        $this->view('users/index', [
            'pageTitle' => 'User Management',
            'title' => 'Users | ASMS',
            'users' => $users,
            'pagination' => [
                'page' => $page,
                'totalPages' => $totalPages
            ]
        ]);
    }

    private function normalizeRole($role) {
        $role = trim($role ?? 'User');
        $role = str_replace(' ', '', $role);
        return ucfirst(strtolower($role));
    }

    public function store() {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $fullName = trim($_POST['full_name']);
            $password = trim($_POST['password']);
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $role = $this->normalizeRole($_POST['role'] ?? 'User');

            // Check if user exists
            if ($this->userRepo->findByUsername($username)) {
                setFlash('msg_error', 'Username already taken.');
                redirect('users');
                return;
            }

            $serverRepo = new \App\Repositories\ServerRepository();
            $db = \App\Core\Database::getInstance();

            // 1. Create Server Profile
            $serverData = [
                'name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'rank' => 'Server',
                'team' => 'Unassigned',
                'status' => 'Active'
            ];
            
            if ($serverRepo->create($serverData)) {
                $serverId = $db->lastInsertId();

                // 2. Create User Account
                $userData = [
                    'username' => $username,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => $role,
                    'server_id' => $serverId
                ];
                
                if ($this->userRepo->create($userData)) {
                    logAction('Create', 'Users', "Created user account: $username with role $role");
                    setFlash('msg_success', 'User created successfully.');
                } else {
                    setFlash('msg_error', 'Failed to create user account.');
                }
            } else {
                setFlash('msg_error', 'Failed to create server profile.');
            }
            redirect('users');
        }
    }

    public function update() {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $role = $this->normalizeRole($_POST['role']);
            $password = $_POST['password'] ?? '';

            $data = ['role' => $role];
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            if ($this->userRepo->update($id, $data)) {
                logAction('Update', 'Users', "Updated user ID: $id. Role: $role");
                setFlash('msg_success', 'User updated successfully.');
            } else {
                setFlash('msg_error', 'Failed to update user.');
            }
            redirect('users');
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->verifyCsrf();
            $id = $_POST['id'];

            if ($this->userRepo->delete($id)) {
                logAction('Delete', 'Users', "Deleted user ID: $id");
                setFlash('msg_success', 'User deleted successfully.');
            } else {
                setFlash('msg_error', 'Failed to delete user.');
            }
            redirect('users');
        }
    }

    public function bulkDelete() {
        $this->verifyCsrf();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['ids'])) {
            $ids = $_POST['ids'];
            $count = 0;
            foreach ($ids as $id) {
                if ($this->userRepo->delete($id)) {
                    $count++;
                }
            }
            logAction('Delete', 'Users', "Bulk deleted $count users.");
            setFlash('msg_success', "Deleted $count users successfully.");
        } else {
            setFlash('msg_error', "No users selected.");
        }
        redirect('users');
    }

    public function import() {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
            $fileName = $_FILES['csv_file']['tmp_name'];
            
            if ($_FILES['csv_file']['size'] > 0) {
                $file = fopen($fileName, "r");
                $count = 0;
                $duplicates = 0;
                
                $serverRepo = new \App\Repositories\ServerRepository();
                $db = \App\Core\Database::getInstance();

                $firstRow = true;
                while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                    if ($firstRow) { $firstRow = false; continue; } // Skip Header

                    if (count($column) < 2) continue;
                    
                    $username = trim($column[0]);
                    $fullName = trim($column[1]);
                    $role = $this->normalizeRole($column[2] ?? 'User');

                    if (empty($username)) continue;

                    // Check if user already exists
                    if ($this->userRepo->findByUsername($username)) {
                        $duplicates++;
                        continue;
                    }

                    // 1. Create Server Profile
                    $serverData = [
                        'name' => $fullName,
                        'rank' => 'Server',
                        'team' => 'Unassigned',
                        'status' => 'Active',
                        'email' => ''
                    ];
                    
                    if ($serverRepo->create($serverData)) {
                        $serverId = $db->lastInsertId();

                        // 2. Create User Account
                        $userData = [
                            'username' => $username,
                            'password' => password_hash('12345', PASSWORD_DEFAULT),
                            'role' => $role,
                            'server_id' => $serverId
                        ];
                        
                        if ($this->userRepo->create($userData)) {
                            $count++;
                        }
                    }
                }
                
                fclose($file);
                
                logAction('Create', 'Users', "Imported $count users via CSV.");
                $msg = "Imported $count users successfully. (Default Pass: 12345)";
                if ($duplicates > 0) $msg .= " ($duplicates skipped as duplicates).";
                
                setFlash('msg_success', $msg);
            } else {
                setFlash('msg_error', 'Empty file uploaded.');
            }
        } else {
            setFlash('msg_error', 'Invalid file upload.');
        }
        redirect('users');
    }
}