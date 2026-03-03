<?php

namespace App\Controllers\Api;

use App\Repositories\UserRepository;
use App\Repositories\ServerRepository;

class UserController extends ApiController {
    private $userRepo;

    public function __construct() {
        $this->requireRoleApi('Superadmin');
        $this->userRepo = new UserRepository();
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search' => $_GET['search'] ?? '',
            'role' => $_GET['role'] ?? ''
        ];

        $users = $this->userRepo->search($filters, $limit, $offset);
        $totalRecords = $this->userRepo->countSearch($filters);
        $totalPages = $totalRecords > 0 ? (int)ceil($totalRecords / $limit) : 0;

        $this->ok([
            'users' => $users,
            'filters' => $filters,
            'pagination' => [
                'page' => $page,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords
            ]
        ]);
    }

    private function normalizeRole($role) {
        $role = trim($role ?? 'User');
        $role = str_replace(' ', '', $role);
        return ucfirst(strtolower($role));
    }

    public function store() {
        $this->verifyCsrfApi();
        $data = $this->getRequestData();

        $username = trim($data['username'] ?? '');
        $firstName = trim($data['first_name'] ?? '');
        $middleName = trim($data['middle_name'] ?? '');
        $lastName = trim($data['last_name'] ?? '');
        $password = trim($data['password'] ?? DEFAULT_USER_PASSWORD);
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $role = $this->normalizeRole($data['role'] ?? 'User');

        if ($role === 'Superadmin') {
            $this->error('Cannot create Superadmin accounts.', 403);
        }
        if ($username === '') {
            $this->error('Username is required.', 422);
        }
        if ($this->userRepo->findByUsername($username)) {
            $this->error('Username already taken.', 409);
        }

        $serverRepo = new ServerRepository();
        $db = \App\Core\Database::getInstance();

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

            $userData = [
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
                'server_id' => $serverId,
                'force_password_reset' => 1
            ];

                if ($this->userRepo->create($userData)) {
                    $userId = $db->lastInsertId();
                    logAction('Create', 'Users', "Created user account: $username with role $role");
                    $this->ok(['message' => 'User created successfully.', 'user_id' => $userId, 'server_id' => $serverId]);
                }
            $this->error('Failed to create user account.', 500);
        }

        $this->error('Failed to create server profile.', 500);
    }

    public function update() {
        $this->verifyCsrfApi();
        $data = $this->getRequestData();

        $id = $data['id'] ?? null;
        $firstName = trim($data['first_name'] ?? '');
        $middleName = trim($data['middle_name'] ?? '');
        $lastName = trim($data['last_name'] ?? '');
        $role = $this->normalizeRole($data['role'] ?? '');
        $password = $data['password'] ?? '';

        if ($role === 'Superadmin') {
            $this->error('Cannot promote to Superadmin role.', 403);
        }

        $user = $this->userRepo->getById($id);
        if (!$user) {
            $this->error('User not found.', 404);
        }

        $db = \App\Core\Database::getInstance();
        if ($user->server_id) {
            $db->query("UPDATE servers SET first_name = :fname, middle_name = :mname, last_name = :lname WHERE id = :sid");
            $db->bind(':fname', $firstName);
            $db->bind(':mname', $middleName);
            $db->bind(':lname', $lastName);
            $db->bind(':sid', $user->server_id);
            $db->execute();
        }

        $updateData = ['role' => $role];
        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->userRepo->update($id, $updateData)) {
            logAction('Update', 'Users', "Updated user account: " . $user->username);
            $this->ok(['message' => 'User updated successfully.']);
        }
        $this->error('Failed to update user.', 500);
    }

    public function delete() {
        $this->verifyCsrfApi();
        $data = $this->getRequestData();
        $id = $data['id'] ?? null;
        $user = $this->userRepo->getById($id);
        $username = $user ? $user->username : "ID: $id";

        if ($this->userRepo->delete($id)) {
            logAction('Delete', 'Users', "Removed user account: $username");
            $this->ok(['message' => 'User removed successfully.']);
        }
        $this->error('Failed to remove user.', 500);
    }

    public function bulkDelete() {
        $this->verifyCsrfApi();
        $data = $this->getRequestData();
        $ids = $data['ids'] ?? [];
        if (empty($ids)) {
            $this->error('No users selected.', 422);
        }

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
        $this->ok(['message' => "Deleted $count users successfully."]);
    }

    public function import() {
        $this->verifyCsrfApi();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
            $fileName = $_FILES['csv_file']['tmp_name'];

            if ($_FILES['csv_file']['size'] > 0) {
                $file = fopen($fileName, "r");
                $count = 0;
                $duplicates = 0;

                $serverRepo = new ServerRepository();
                $db = \App\Core\Database::getInstance();

                $firstRow = true;
                while (($line = fgetcsv($file, 10000, ",")) !== false) {
                    $column = $line;
                    if (count($column) == 1 && strpos($column[0], ';') !== false) {
                        $column = str_getcsv($column[0], ';');
                    }
                    if ($firstRow) { $firstRow = false; continue; }
                    if (count($column) < 2) continue;

                    $username = trim($column[0]);
                    $fullName = trim($column[1]);
                    $role = $this->normalizeRole($column[2] ?? 'User');

                    if (empty($username)) continue;
                    if ($this->userRepo->findByUsername($username)) {
                        $duplicates++;
                        continue;
                    }

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
                $this->ok(['message' => $msg, 'count' => $count, 'duplicates' => $duplicates]);
            }
            $this->error('Empty file uploaded.', 422);
        }
        $this->error('Invalid file upload.', 422);
    }
}
