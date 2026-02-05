<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepository;
use App\Models\SystemSetting;

class SettingsController extends Controller {
    private $userRepo;
    private $settingModel;

    public function __construct() {
        $this->requireLogin();
        $this->userRepo = new UserRepository();
        $this->settingModel = new SystemSetting();
    }

    public function index() {
        $userProfile = $this->userRepo->getUserProfile($_SESSION['user_id']);
        $systemSettings = $this->settingModel->getAll();

        // Fallback if no profile found
        if (!$userProfile) {
            $userProfile = (object)[
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'],
                'profile_image' => null,
                'name' => $_SESSION['username'],
                'age' => '',
                'phone' => '',
                'address' => '',
                'email' => ''
            ];
        }

        if (($_SESSION['role'] ?? '') === 'User') {
            $this->view('settings/user_index', [
                'pageTitle' => 'Account Settings',
                'title' => 'Settings | ASMS',
                'profile' => $userProfile
            ]);
        } else {
            $this->view('settings/index', [
                'pageTitle' => 'System & Account Settings',
                'title' => 'Settings | ASMS',
                'profile' => $userProfile,
                'system' => $systemSettings
            ]);
        }
    }

    public function store() {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $action = $_POST['action'] ?? '';
            $role = $_SESSION['role'] ?? 'User';

                        // 1. Update Profile (Both Admin & User)
                        if ($action === 'update_profile') {
                            $userProfile = $this->userRepo->getUserProfile($_SESSION['user_id']);
                            
                            // Restriction: Regular Users can only edit once unless allowed
                            if ($role === 'User' && !$userProfile->can_edit_profile) {
                                setFlash('msg_error', 'Profile editing is locked. Please contact an admin to allow changes.');
                                redirect('settings');
                            }
            
                            $data = [
                                'first_name' => trim($_POST['first_name']),
                                'middle_name' => trim($_POST['middle_name'] ?? ''),
                                'last_name' => trim($_POST['last_name']),
                                'nickname' => trim($_POST['nickname'] ?? ''),
                                'dob' => $_POST['dob'] ?? null,
                                'age' => trim($_POST['age']),
                                'address' => trim($_POST['address'] ?? ''),
                                'phone' => trim($_POST['phone']),
                                'email' => trim($_POST['email'] ?? '')
                            ];

                            // Server-side Validation
                            if (empty($data['first_name']) || empty($data['last_name']) || empty($data['age']) || empty($data['phone']) || empty($data['address']) || empty($data['email'])) {
                                setFlash('msg_error', 'All fields marked as required must be filled out.');
                                redirect('settings');
                            }
            
                            // Handle Cropped Profile Image (Base64)
                            if (!empty($_POST['cropped_image'])) {
                                $imgData = $_POST['cropped_image'];
                                if (preg_match('/^data:image\/(\w+);base64,/', $imgData, $type)) {
                                    $imgData = substr($imgData, strpos($imgData, ',') + 1);
                                    $type = strtolower($type[1]); // jpg, png, etc
            
                                    if (in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                                        $imgData = base64_decode($imgData);
            
                                        if ($imgData !== false) {
                                            $newFileName = md5(time() . uniqid()) . '.' . $type;
                                            $uploadFileDir = '../public/uploads/profiles/';
            
                                            if (!is_dir($uploadFileDir)) {
                                                mkdir($uploadFileDir, 0755, true);
                                            }
            
                                            if (file_put_contents($uploadFileDir . $newFileName, $imgData)) {
                                                $data['profile_image'] = $newFileName;
                                            }
                                        }
                                    }
                                }
                            } elseif (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                                // Fallback for non-cropped uploads
                                $fileTmpPath = $_FILES['profile_image']['tmp_name'];
                                $fileName = $_FILES['profile_image']['name'];
                                $fileNameCmps = explode(".", $fileName);
                                $fileExtension = strtolower(end($fileNameCmps));
                                $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');
            
                                if (in_array($fileExtension, $allowedfileExtensions)) {
                                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                                    $uploadFileDir = '../public/uploads/profiles/';
            
                                    if (!is_dir($uploadFileDir)) {
                                        mkdir($uploadFileDir, 0755, true);
                                    }
            
                                    if(move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                                        $data['profile_image'] = $newFileName;
                                    }
                                }
                            }
            
                            // Validation: Check if image is provided OR already exists
                            if (empty($data['profile_image']) && empty($userProfile->profile_image)) {
                                setFlash('msg_error', 'Profile photo is required. Please upload and crop a photo.');
                                redirect('settings');
                            }
            
                            if ($this->userRepo->updateProfile($_SESSION['user_id'], $data)) {
                                $_SESSION['full_name'] = $data['first_name'] . ' ' . $data['last_name']; 
                                
                                // If it's a regular user, lock their profile after first edit
                                if ($role === 'User') {
                                    $this->userRepo->updateEditRestriction($_SESSION['user_id'], 1);
                                }
            
                                logAction('Update', 'Settings', 'Updated personal profile information.');
                                setFlash('msg_success', 'Profile updated successfully.');
                            } else {
                                setFlash('msg_error', 'Failed to update profile.');
                            }
                        }
            // 2. Update Password (Both Admin & User)
            if (!empty($_POST['new_password'])) {
                $current = $_POST['current_password'];
                $new = $_POST['new_password'];
                $confirm = $_POST['confirm_password'];

                if ($new !== $confirm) {
                    setFlash('msg_error', 'New passwords do not match.');
                } else {
                    $user = $this->userRepo->getById($_SESSION['user_id']);
                    if ($user && password_verify($current, $user->password)) {
                        $hashed = password_hash($new, PASSWORD_DEFAULT);
                        if ($this->userRepo->update($_SESSION['user_id'], [
                            'password' => $hashed, 
                            'role' => $role,
                            'force_password_reset' => 0
                        ])) {
                            $_SESSION['force_reset'] = 0;
                            logAction('Update', 'Settings', 'Changed account password.');
                            setFlash('msg_success', 'Password updated successfully.');
                        }
                    } else {
                        setFlash('msg_error', 'Incorrect current password.');
                    }
                }
            }

            // 3. System Settings (Superadmin Only)
            if ($role === 'Superadmin' && isset($_POST['system_settings'])) {
                $settings = [
                    'system_name' => $_POST['system_name'] ?? '',
                    'admin_email' => $_POST['admin_email'] ?? '',
                    'contact_phone' => $_POST['contact_phone'] ?? '',
                    'maintenance_mode' => $_POST['maintenance_mode'] ?? 'off',
                    'allow_registration' => $_POST['allow_registration'] ?? 'off'
                ];

                foreach ($settings as $key => $val) {
                    $this->settingModel->update($key, $val);
                }
                logAction('Update', 'Settings', 'Updated system-wide configuration.');
                setFlash('msg_success', 'System settings updated successfully!');
            }

            redirect('settings');
        }
    }

    public function toggle_edit($userId) {
        $role = $_SESSION['role'] ?? '';
        if ($role !== 'Admin' && $role !== 'Superadmin') {
            setFlash('msg_error', 'Unauthorized.');
            redirect('users');
        }

        $user = $this->userRepo->getById($userId);
        if (!$user) {
            setFlash('msg_error', 'User not found.');
            redirect('users');
        }

        $newStatus = $user->can_edit_profile ? 0 : 1;
        if ($this->userRepo->toggleEditPermission($userId, $newStatus)) {
            $msg = $newStatus ? "Profile editing enabled for " . $user->username : "Profile editing disabled for " . $user->username;
            logAction('Update', 'Users', $msg);
            setFlash('msg_success', $msg);
        } else {
            setFlash('msg_error', 'Failed to update permission.');
        }

        redirect('users');
    }

    public function backup() {
        if (($_SESSION['role'] ?? '') !== 'Superadmin') {
            setFlash('msg_error', 'Unauthorized.');
            redirect('settings');
        }

        $filename = 'backup_' . DB_NAME . '_' . date('Y-m-d_H-i-s') . '.sql';
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Simple backup using SHOW CREATE TABLE and SELECT *
        $tables = ['announcements', 'attendance', 'logs', 'schedules', 'servers', 'system_settings', 'users'];
        
        foreach ($tables as $table) {
            $this->db->query("SHOW CREATE TABLE $table");
            $row = $this->db->single();
            $createTable = (array)$row;
            echo "\n\n" . $createTable['Create Table'] . ";\n\n";

            $this->db->query("SELECT * FROM $table");
            $rows = $this->db->resultSet();
            foreach ($rows as $r) {
                $rArray = (array)$r;
                $keys = array_keys($rArray);
                $values = array_values($rArray);
                $values = array_map(function($v) {
                    if (is_null($v)) return 'NULL';
                    return "'" . addslashes($v) . "'";
                }, $values);
                
                echo "INSERT INTO $table (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ");\n";
            }
        }
        exit;
    }
}