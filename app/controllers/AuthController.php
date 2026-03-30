<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepository;

class AuthController extends Controller {
    private $userRepo;

    public function __construct() {
        $this->userRepo = new UserRepository();
    }

    // Show Login Page
    public function login() {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/dashboard');
            exit;
        }
        
        $allowRegistration = \App\Models\SystemSetting::get('allow_registration', 'off');
        
        // Load view directly (special layout for auth)
        require_once '../app/views/auth/login.php';
    }

    public function maintenance() {
        require_once '../app/views/pages/maintenance.php';
    }

    // Process Login
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            $user = $this->userRepo->findByUsername($username);

            if ($user && password_verify($password, $user->password)) {
                // Load role permissions
                $roleRepo = new \App\Repositories\RoleRepository();
                $permissions = $roleRepo->getPermissionsByRole($user->role_id);

                // Load user-specific permissions
                $userPermissions = $this->userRepo->getUserPermissions($user->id);
                
                // Create Session
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->username;
                $_SESSION['full_name'] = $user->full_name ?? $user->username;
                $_SESSION['role_id'] = $user->role_id;
                $_SESSION['role'] = $user->role; // Keeping this for backward compatibility
                $_SESSION['is_verified'] = $user->is_verified;
                $_SESSION['force_reset'] = $user->force_password_reset;
                $_SESSION['permissions'] = $permissions;
                $_SESSION['user_permissions'] = $userPermissions;

                logAction('Login', 'Auth', 'User ' . $_SESSION['full_name'] . ' logged in.');

                if ($user->force_password_reset) {
                    setFlash('msg_info', 'Please change your password to continue.');
                    header('Location: ' . URLROOT . '/settings');
                } else {
                    header('Location: ' . URLROOT . '/dashboard');
                }
            } else {
                $allowRegistration = \App\Models\SystemSetting::get('allow_registration', 'off');
                $error = "Invalid username or password.";
                require_once '../app/views/auth/login.php';
            }
        }
    }

    // Show Register Page
    public function register() {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/dashboard');
            exit;
        }

        $allowRegistration = \App\Models\SystemSetting::get('allow_registration', 'off');
        if ($allowRegistration !== 'on') {
            setFlash('msg_error', 'Public registration is currently disabled.');
            redirect('login');
        }

        require_once '../app/views/auth/register.php';
    }

    // Process Registration
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $roleRepo = new \App\Repositories\RoleRepository();
            $defaultRole = $roleRepo->getRoleByName('User');

            $data = [
                'username' => trim($_POST['username']),
                'password' => password_hash(trim($_POST['password']), PASSWORD_DEFAULT),
                'role' => 'User',
                'role_id' => $defaultRole ? $defaultRole->id : null,
                'force_password_reset' => 0
            ];

            // Check if user exists
            if ($this->userRepo->findByUsername($data['username'])) {
                $error = "Username already taken.";
                require_once '../app/views/auth/register.php';
                return;
            }

            if ($this->userRepo->create($data)) {
                logAction('Create', 'Auth', 'New user registered: ' . $data['username']);
                setFlash('msg_success', 'Registration successful! You can now login.');
                redirect('login');
            } else {
                die("Something went wrong");
            }
        }
    }

    // Logout
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            if (isset($_SESSION['username'])) {
                logAction('Logout', 'Auth', 'User ' . $_SESSION['username'] . ' logged out.');
            }
            
            // Mark notifications as checked upon logout so badge clears on next login
            $this->userRepo->updateLastCheckedNotifications($_SESSION['user_id']);
        }

        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['role']);
        session_destroy();
        header('Location: ' . URLROOT . '/login');
    }
}