<?php

namespace App\Controllers\Api;

use App\Repositories\UserRepository;

class AuthController extends ApiController {
    private $userRepo;

    public function __construct() {
        $this->userRepo = new UserRepository();
    }

    public function login() {
        $data = $this->getRequestData();
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        if ($username === '' || $password === '') {
            $this->error('Username and password are required.', 422);
        }

        $user = $this->userRepo->findByUsername($username);
        if (!$user || !password_verify($password, $user->password)) {
            $this->error('Invalid username or password.', 401);
        }

        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['full_name'] = $user->full_name ?? $user->username;
        $_SESSION['role'] = $user->role;
        $_SESSION['is_verified'] = $user->is_verified;
        $_SESSION['force_reset'] = $user->force_password_reset;

        logAction('Login', 'Auth', 'User ' . $_SESSION['full_name'] . ' logged in.');

        $this->ok([
            'user_id' => $user->id,
            'username' => $user->username,
            'full_name' => $_SESSION['full_name'],
            'role' => $user->role,
            'is_verified' => (int)$user->is_verified,
            'force_password_reset' => (int)$user->force_password_reset
        ]);
    }

    public function register() {
        $allowRegistration = \App\Models\SystemSetting::get('allow_registration', 'off');
        if ($allowRegistration !== 'on') {
            $this->error('Public registration is currently disabled.', 403);
        }

        $data = $this->getRequestData();
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        if ($username === '' || $password === '') {
            $this->error('Username and password are required.', 422);
        }

        if ($this->userRepo->findByUsername($username)) {
            $this->error('Username already taken.', 409);
        }

        $created = $this->userRepo->create([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'User',
            'force_password_reset' => 0
        ]);

        if (!$created) {
            $this->error('Registration failed.', 500);
        }

        logAction('Create', 'Auth', 'New user registered: ' . $username);
        $this->ok(['username' => $username]);
    }

    public function logout() {
        if (isset($_SESSION['username'])) {
            logAction('Logout', 'Auth', 'User ' . $_SESSION['username'] . ' logged out.');
        }
        unset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role']);
        session_destroy();

        $this->ok(['message' => 'Logged out']);
    }

    public function me() {
        $this->requireLoginApi();
        $profile = $this->userRepo->getUserProfile($_SESSION['user_id']);
        $this->ok([
            'user' => $this->userRepo->getById($_SESSION['user_id']),
            'profile' => $profile
        ]);
    }
}
