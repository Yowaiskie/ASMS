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
            $this->forbidden();
        }
        $this->userRepo = new UserRepository();
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search' => $_GET['search'] ?? '',
            'role' => $_GET['role'] ?? ''
        ];

        $users = $this->userRepo->search($filters, $limit, $offset);
        $totalRecords = $this->userRepo->countSearch($filters);
        $totalPages = $totalRecords > 0 ? (int)ceil($totalRecords / $limit) : 0;

        $this->view('users/index', [
            'pageTitle' => 'User Management',
            'title' => 'Users | ASMS',
            'users' => $users,
            'filters' => $filters,
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
        $page = $_POST['page'] ?? $_GET['page'] ?? 1;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $firstName = trim($_POST['first_name'] ?? '');
            $middleName = trim($_POST['middle_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $password = trim($_POST['password'] ?? DEFAULT_USER_PASSWORD);
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $role = $this->normalizeRole($_POST['role'] ?? 'User');

            // Block Superadmin creation via UI
            if ($role === 'Superadmin') {
                setFlash('msg_error', 'Cannot create Superadmin accounts.');
                redirect('users?page=' . $page);
                return;
            }

            // Validation: Only username is strictly required now
            if (empty($username)) {
                setFlash('msg_error', 'Username is required.');
                redirect('users?page=' . $page);
                return;
            }

            // Check if user exists
            if ($this->userRepo->findByUsername($username)) {
                setFlash('msg_error', 'Username already taken.');
                redirect('users?page=' . $page);
                return;
            }

            $serverRepo = new \App\Repositories\ServerRepository();
            $db = \App\Core\Database::getInstance();

            // 1. Create Server Profile (Use username parts if names are missing)
            $serverData = [
                'first_name' => !empty($firstName) ? $firstName : $username,
                'middle_name' => $middleName,
                'last_name' => !empty($lastName) ? $lastName : 'User',
                'email' => $email,
                'phone' => $phone,
                'rank' => 'Server',
                'team' => 'Unassigned',
                'status' => 'Active',
                'month_joined' => date('Y-m')
            ];
            
            if ($serverRepo->create($serverData)) {
                $serverId = $db->lastInsertId();

                // 2. Create User Account
                $userData = [
                    'username' => $username,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => $role,
                    'server_id' => $serverId,
                    'force_password_reset' => 1
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
            redirect('users?page=' . $page);
        }
    }

    public function update() {
        $this->verifyCsrf();
        $page = $_POST['page'] ?? $_GET['page'] ?? 1;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $firstName = trim($_POST['first_name']);
            $middleName = trim($_POST['middle_name'] ?? '');
            $lastName = trim($_POST['last_name']);
            $role = $this->normalizeRole($_POST['role']);
            $password = $_POST['password'] ?? '';

            // Block Superadmin promotion via UI
            if ($role === 'Superadmin') {
                setFlash('msg_error', 'Cannot promote to Superadmin role.');
                redirect('users?page=' . $page);
                return;
            }

            $user = $this->userRepo->getById($id);
            if (!$user) {
                setFlash('msg_error', 'User not found.');
                redirect('users?page=' . $page);
                return;
            }

            $db = \App\Core\Database::getInstance();
            
            // 1. Update Server Info
            if ($user->server_id) {
                $db->query("UPDATE servers SET first_name = :fname, middle_name = :mname, last_name = :lname WHERE id = :sid");
                $db->bind(':fname', $firstName);
                $db->bind(':mname', $middleName);
                $db->bind(':lname', $lastName);
                $db->bind(':sid', $user->server_id);
                $db->execute();
            }

            // 2. Update User Account
            $data = ['role' => $role];
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            if ($this->userRepo->update($id, $data)) {
                logAction('Update', 'Users', "Updated user account: " . $user->username);
                setFlash('msg_success', 'User updated successfully.');
            } else {
                setFlash('msg_error', 'Failed to update user.');
            }
            redirect('users?page=' . $page);
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->verifyCsrf();
            $id = $_POST['id'];
            $page = $_POST['page'] ?? $_GET['page'] ?? 1;
            
            $user = $this->userRepo->getById($id);
            $username = $user ? $user->username : "ID: $id";

            if ($this->userRepo->delete($id)) {
                logAction('Delete', 'Users', "Removed user account: $username");
                setFlash('msg_success', 'User removed successfully.');
            } else {
                setFlash('msg_error', 'Failed to remove user.');
            }
            redirect('users?page=' . $page);
        }
    }

    public function bulkDelete() {
        $this->verifyCsrf();
        $page = $_POST['page'] ?? $_GET['page'] ?? 1;
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['ids'])) {
            $ids = $_POST['ids'];
            $count = 0;
            $usernames = [];
            foreach ($ids as $id) {
                $user = $this->userRepo->getById($id);
                if ($user) $usernames[] = $user->username;
                if ($this->userRepo->delete($id)) {
                    $count++;
                }
            }
            logAction('Delete', 'Users', "Bulk deleted $count users: " . implode(', ', $usernames));
            setFlash('msg_success', "Deleted $count users successfully.");
        }
        redirect('users?page=' . $page);
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
                while (($line = fgetcsv($file, 10000, ",")) !== FALSE) {
                    $column = $line;

                    // Delimiter Detection: If only 1 column found, try semicolon
                    if (count($column) == 1 && strpos($column[0], ';') !== false) {
                        $column = str_getcsv($column[0], ';');
                    }

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
                    $parts = explode(' ', $fullName);
                    $lastName = (count($parts) > 1) ? array_pop($parts) : 'Server';
                    $firstName = (count($parts) >= 1) ? array_shift($parts) : $fullName;
                    $middleName = implode(' ', $parts);

                    $serverData = [
                        'first_name' => $firstName,
                        'middle_name' => $middleName,
                        'last_name' => $lastName,
                        'rank' => 'Server',
                        'team' => 'Unassigned',
                        'status' => 'Active',
                        'email' => '',
                        'month_joined' => date('Y-m')
                    ];
                    
                    if ($serverRepo->create($serverData)) {
                        $serverId = $db->lastInsertId();

                        // 2. Create User Account
                        $userData = [
                            'username' => $username,
                            'password' => password_hash(DEFAULT_USER_PASSWORD, PASSWORD_DEFAULT),
                            'role' => $role,
                            'server_id' => $serverId,
                            'force_password_reset' => 1
                        ];
                        
                        if ($this->userRepo->create($userData)) {
                            $count++;
                        }
                    }
                }
                
                fclose($file);
                
                logAction('Create', 'Users', "Imported $count users via CSV.");
                $msg = "Imported $count users successfully. (Default Pass: " . DEFAULT_USER_PASSWORD . ")";
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