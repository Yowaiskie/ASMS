<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepository;
use App\Repositories\SystemSettingRepository;
use App\Models\SystemSetting;

class SettingsController extends Controller {
    private $userRepo;
    private $systemRepo;
    private $settingModel;
    private $db;

    public function __construct() {
        $this->requireLogin();
        $this->userRepo = new UserRepository();
        $this->systemRepo = new SystemSettingRepository();
        $this->settingModel = new SystemSetting();
        $this->db = \App\Core\Database::getInstance();
    }

    public function index() {
        $userProfile = $this->userRepo->getUserProfile($_SESSION['user_id']);
        
        $systemSettings = [
            'system_name' => $this->systemRepo->get('system_name', 'Altar Servers Management System'),
            'admin_email' => $this->systemRepo->get('admin_email', ''),
            'parish_name' => $this->systemRepo->get('parish_name', 'Our Parish')
        ];

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

    public function system() {
        $this->requireRole(['Admin', 'Superadmin']);
        
        $data = [
            'pageTitle' => 'System Configuration',
            'title' => 'System Settings | ASMS',
            'activityTypes' => $this->systemRepo->getActivityTypes(false),
            'ranks' => $this->systemRepo->getRanks(false),
            'categories' => $this->systemRepo->getCategories(false),
            'system_name' => $this->systemRepo->get('system_name', 'Altar Servers Management System'),
            'admin_email' => $this->systemRepo->get('admin_email', ''),
            'parish_name' => $this->systemRepo->get('parish_name', 'Our Parish'),
            // Policy Settings
            'policy_suspension_threshold' => $this->systemRepo->get('policy_suspension_threshold', 3),
            'policy_suspension_warning' => $this->systemRepo->get('policy_suspension_warning', 2),
            'policy_suspension_duration' => $this->systemRepo->get('policy_suspension_duration', 30),
            'policy_late_to_absent_ratio' => $this->systemRepo->get('policy_late_to_absent_ratio', 2),
            'policy_excuse_lead_time' => $this->systemRepo->get('policy_excuse_lead_time', 24),
            'policy_schedule_duration' => $this->systemRepo->get('policy_schedule_duration', 1),
            'policy_auto_remove_on_suspension' => $this->systemRepo->get('policy_auto_remove_on_suspension', 1),
            'policy_suspension_activity_types' => json_decode($this->systemRepo->get('policy_suspension_activity_types', '[]'), true)
        ];
        
        $this->view('settings/system', $data);
    }

    public function storeSystem() {
        $this->requireRole('Superadmin');
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->systemRepo->set('system_name', $_POST['system_name']);
            $this->systemRepo->set('parish_name', $_POST['parish_name']);
            $this->systemRepo->set('admin_email', $_POST['admin_email']);
            
            // Policy Settings
            if (isset($_POST['policy_suspension_threshold'])) {
                $this->systemRepo->set('policy_suspension_threshold', (int)$_POST['policy_suspension_threshold']);
                $this->systemRepo->set('policy_suspension_warning', (int)$_POST['policy_suspension_warning']);
                $this->systemRepo->set('policy_suspension_duration', (int)$_POST['policy_suspension_duration']);
                $this->systemRepo->set('policy_late_to_absent_ratio', (int)$_POST['policy_late_to_absent_ratio']);
                $this->systemRepo->set('policy_excuse_lead_time', (int)$_POST['policy_excuse_lead_time']);
                $this->systemRepo->set('policy_schedule_duration', (int)$_POST['policy_schedule_duration']);
                $this->systemRepo->set('policy_auto_remove_on_suspension', isset($_POST['policy_auto_remove_on_suspension']) ? 1 : 0);
                
                $activityTypes = $_POST['policy_activity_types'] ?? [];
                $this->systemRepo->set('policy_suspension_activity_types', json_encode($activityTypes));
            }
            
            setFlash('msg_success', 'System settings updated.');
        }
        redirect('settings/system');
    }

    public function storeActivityType() {
        $this->requireRole('Superadmin');
        $this->verifyCsrf();
        if ($this->systemRepo->addActivityType($_POST['name'], $_POST['color'] ?? 'blue')) {
            setFlash('msg_success', 'Activity type added.');
        } else {
            setFlash('msg_error', 'Failed to add. Maybe it already exists?');
        }
        redirect('settings/system');
    }

    public function deleteActivityType($id) {
        $this->requireRole('Superadmin');
        $this->systemRepo->deleteActivityType($id);
        setFlash('msg_success', 'Activity type removed.');
        redirect('settings/system');
    }

    public function storeRank() {
        $this->requireRole('Superadmin');
        $this->verifyCsrf();
        if ($this->systemRepo->addRank($_POST['name'])) {
            setFlash('msg_success', 'Rank added.');
        } else {
            setFlash('msg_error', 'Failed to add.');
        }
        redirect('settings/system');
    }

    public function deleteRank($id) {
        $this->requireRole('Superadmin');
        $this->systemRepo->deleteRank($id);
        setFlash('msg_success', 'Rank removed.');
        redirect('settings/system');
    }

    public function storeCategory() {
        $this->requireRole('Superadmin');
        $this->verifyCsrf();
        if ($this->systemRepo->addCategory($_POST['name'])) {
            setFlash('msg_success', 'Category added.');
        } else {
            setFlash('msg_error', 'Failed to add.');
        }
        redirect('settings/system');
    }

    public function deleteCategory($id) {
        $this->requireRole('Superadmin');
        $this->systemRepo->deleteCategory($id);
        setFlash('msg_success', 'Category removed.');
        redirect('settings/system');
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

                if (empty($current)) {
                    setFlash('msg_error', 'Please enter your current password.');
                } elseif ($new !== $confirm) {
                    setFlash('msg_error', 'New passwords do not match.');
                } else {
                    $user = $this->userRepo->getById($_SESSION['user_id']);
                    if ($user && password_verify($current, $user->password)) {
                        $hashed = password_hash($new, PASSWORD_DEFAULT);
                        if ($this->userRepo->update($_SESSION['user_id'], [
                            'password' => $hashed, 
                            'force_password_reset' => 0
                        ])) {
                            $_SESSION['force_reset'] = 0;
                            logAction('Update', 'Settings', 'Changed account password.');
                            setFlash('msg_success', 'Password updated successfully.');
                        } else {
                            setFlash('msg_error', 'Something went wrong while updating the password.');
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

    public function database() {
        $this->requireRole('Superadmin');
        
        $userProfile = $this->userRepo->getUserProfile($_SESSION['user_id']);
        
        $this->view('settings/database', [
            'pageTitle' => 'Database Management',
            'title' => 'Database Management | ASMS',
            'profile' => $userProfile
        ]);
    }

    public function backup() {
        if (($_SESSION['role'] ?? '') !== 'Superadmin') {
            setFlash('msg_error', 'Unauthorized.');
            redirect('settings');
        }

        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        $filename = 'backup_' . DB_NAME . '_' . date('Y-m-d_H-i-s');
        if ($startDate) $filename .= '_from_' . $startDate;
        if ($endDate) $filename .= '_to_' . $endDate;
        $filename .= '.sql';
        
        // Set cookie to notify JS that download has started/finished
        setcookie('download_started', 'true', time() + 3600, '/');

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Simple backup using SHOW CREATE TABLE and SELECT *
        $tables = [
            'servers' => 'created_at',
            'users' => 'created_at',
            'schedules' => 'mass_date',
            'attendance' => 'created_at',
            'logs' => 'created_at',
            'announcements' => 'created_at',
            'excuses' => 'absence_date',
            'system_settings' => null,
            'schedule_templates' => 'created_at',
            'activity_types' => null,
            'server_ranks' => null,
            'announcement_categories' => null
        ];
        
        echo "-- ASMS Database Backup\n";
        echo "-- Date: " . date('Y-m-d H:i:s') . "\n";
        if ($startDate) echo "-- Filter Start: $startDate\n";
        if ($endDate) echo "-- Filter End: $endDate\n\n";
        echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table => $dateCol) {
            // Get table creation
            $this->db->query("SHOW CREATE TABLE $table");
            $row = $this->db->single();
            $createTable = (array)$row;
            echo "DROP TABLE IF EXISTS `$table`;\n";
            echo $createTable['Create Table'] . ";\n\n";

            // Get table data
            $sql = "SELECT * FROM $table";
            $where = [];
            if ($dateCol && $startDate) {
                $where[] = "$dateCol >= '$startDate'";
            }
            if ($dateCol && $endDate) {
                $where[] = "$dateCol <= '$endDate 23:59:59'";
            }

            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }

            $this->db->query($sql);
            $rows = $this->db->resultSet();
            
            if (!empty($rows)) {
                echo "-- Data for table `$table`\n";
                foreach ($rows as $r) {
                    $rArray = (array)$r;
                    $keys = array_keys($rArray);
                    $values = array_values($rArray);
                    $values = array_map(function($v) {
                        if (is_null($v)) return 'NULL';
                        return "'" . addslashes($v) . "'";
                    }, $values);
                    
                    echo "INSERT INTO `$table` (`" . implode('`, `', $keys) . "`) VALUES (" . implode(', ', $values) . ");\n";
                }
                echo "\n";
            }
        }
        echo "SET FOREIGN_KEY_CHECKS=1;\n";
        exit;
    }

    public function restore() {
        if (($_SESSION['role'] ?? '') !== 'Superadmin') {
            setFlash('msg_error', 'Unauthorized.');
            redirect('settings/database');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['backup_file'])) {
            $file = $_FILES['backup_file'];
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                setFlash('msg_error', 'Error uploading file.');
                redirect('settings/database');
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            if (strtolower($ext) !== 'sql') {
                setFlash('msg_error', 'Invalid file type. Please upload a .sql file.');
                redirect('settings/database');
            }

            $sql = file_get_contents($file['tmp_name']);
            if (empty($sql)) {
                setFlash('msg_error', 'The uploaded file is empty.');
                redirect('settings/database');
            }

            try {
                // Split SQL into individual queries more robustly
                // This handles both \n and \r\n
                $sql = preg_replace('/--.*?\n/', '', $sql); // Remove comments
                $queries = explode(";\n", str_replace(";\r\n", ";\n", $sql));
                
                $successCount = 0;

                // Note: MySQL DDL (DROP/CREATE) implicitly commits transactions.
                // We cannot wrap the entire restore in one transaction if it contains DDL.
                foreach ($queries as $query) {
                    $query = trim($query);
                    if (!empty($query)) {
                        $this->db->query($query);
                        if ($this->db->execute()) {
                            $successCount++;
                        }
                    }
                }
                
                logAction('Restore', 'Database', "Successfully restored database from backup file: " . $file['name']);
                setFlash('msg_success', 'Database restored successfully! ' . $successCount . ' queries executed.');
            } catch (\Exception $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
                setFlash('msg_error', 'Restore failed: ' . $e->getMessage());
            }
        }
        
        redirect('settings/database');
    }
}