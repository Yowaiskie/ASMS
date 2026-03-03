<?php

namespace App\Controllers\Api;

use App\Repositories\UserRepository;
use App\Repositories\SystemSettingRepository;
use App\Models\SystemSetting;

class SettingsController extends ApiController {
    private $userRepo;
    private $systemRepo;
    private $settingModel;
    private $db;

    public function __construct() {
        $this->requireLoginApi();
        $this->userRepo = new UserRepository();
        $this->systemRepo = new SystemSettingRepository();
        $this->settingModel = new SystemSetting();
        $this->db = \App\Core\Database::getInstance();
    }

    public function index() {
        $userProfile = $this->userRepo->getUserProfile($_SESSION['user_id']);
        $systemSettings = $this->settingModel->getAll();

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

        $this->ok([
            'profile' => $userProfile,
            'system' => $systemSettings
        ]);
    }

    public function system() {
        $this->requireRoleApi('Superadmin');
        $data = [
            'activityTypes' => $this->systemRepo->getActivityTypes(false),
            'ranks' => $this->systemRepo->getRanks(false),
            'categories' => $this->systemRepo->getCategories(false),
            'system_name' => $this->systemRepo->get('system_name', 'Altar Servers Management System'),
            'admin_email' => $this->systemRepo->get('admin_email', ''),
            'parish_name' => $this->systemRepo->get('parish_name', 'Our Parish')
        ];
        $this->ok($data);
    }

    public function storeSystem() {
        $this->requireRoleApi('Superadmin');
        $this->verifyCsrfApi();

        $data = $this->getRequestData();
        $this->systemRepo->set('system_name', $data['system_name'] ?? '');
        $this->systemRepo->set('parish_name', $data['parish_name'] ?? '');
        $this->systemRepo->set('admin_email', $data['admin_email'] ?? '');
        $this->ok(['message' => 'System settings updated.']);
    }

    public function storeActivityType() {
        $this->requireRoleApi('Superadmin');
        $this->verifyCsrfApi();
        $data = $this->getRequestData();
        if ($this->systemRepo->addActivityType($data['name'] ?? '', $data['color'] ?? 'blue')) {
            $this->ok(['message' => 'Activity type added.']);
        }
        $this->error('Failed to add. Maybe it already exists?', 409);
    }

    public function deleteActivityType($id) {
        $this->requireRoleApi('Superadmin');
        $this->systemRepo->deleteActivityType($id);
        $this->ok(['message' => 'Activity type removed.']);
    }

    public function storeRank() {
        $this->requireRoleApi('Superadmin');
        $this->verifyCsrfApi();
        $data = $this->getRequestData();
        if ($this->systemRepo->addRank($data['name'] ?? '')) {
            $this->ok(['message' => 'Rank added.']);
        }
        $this->error('Failed to add.', 500);
    }

    public function deleteRank($id) {
        $this->requireRoleApi('Superadmin');
        $this->systemRepo->deleteRank($id);
        $this->ok(['message' => 'Rank removed.']);
    }

    public function storeCategory() {
        $this->requireRoleApi('Superadmin');
        $this->verifyCsrfApi();
        $data = $this->getRequestData();
        if ($this->systemRepo->addCategory($data['name'] ?? '')) {
            $this->ok(['message' => 'Category added.']);
        }
        $this->error('Failed to add.', 500);
    }

    public function deleteCategory($id) {
        $this->requireRoleApi('Superadmin');
        $this->systemRepo->deleteCategory($id);
        $this->ok(['message' => 'Category removed.']);
    }

    public function store() {
        $this->verifyCsrfApi();
        $data = $this->getRequestData();
        $action = $data['action'] ?? '';
        $role = $_SESSION['role'] ?? 'User';

        if ($action === 'update_profile') {
            $userProfile = $this->userRepo->getUserProfile($_SESSION['user_id']);
            if ($role === 'User' && !$userProfile->can_edit_profile) {
                $this->error('Profile editing is locked. Please contact an admin to allow changes.', 403);
            }

            $payload = [
                'first_name' => trim($data['first_name'] ?? ''),
                'middle_name' => trim($data['middle_name'] ?? ''),
                'last_name' => trim($data['last_name'] ?? ''),
                'nickname' => trim($data['nickname'] ?? ''),
                'dob' => $data['dob'] ?? null,
                'age' => trim($data['age'] ?? ''),
                'address' => trim($data['address'] ?? ''),
                'phone' => trim($data['phone'] ?? ''),
                'email' => trim($data['email'] ?? '')
            ];

            if (empty($payload['first_name']) || empty($payload['last_name']) || empty($payload['age']) || empty($payload['phone']) || empty($payload['address']) || empty($payload['email'])) {
                $this->error('All required fields must be filled out.', 422);
            }

            if (!empty($data['cropped_image'])) {
                $imgData = $data['cropped_image'];
                if (preg_match('/^data:image\/(\w+);base64,/', $imgData, $type)) {
                    $imgData = substr($imgData, strpos($imgData, ',') + 1);
                    $type = strtolower($type[1]);

                    if (in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'], true)) {
                        $imgData = base64_decode($imgData);
                        if ($imgData !== false) {
                            $newFileName = md5(time() . uniqid()) . '.' . $type;
                            $uploadFileDir = '../public/uploads/profiles/';

                            if (!is_dir($uploadFileDir)) {
                                mkdir($uploadFileDir, 0755, true);
                            }

                            if (file_put_contents($uploadFileDir . $newFileName, $imgData)) {
                                $payload['profile_image'] = $newFileName;
                            }
                        }
                    }
                }
            } elseif (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['profile_image']['tmp_name'];
                $fileName = $_FILES['profile_image']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');

                if (in_array($fileExtension, $allowedfileExtensions, true)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = '../public/uploads/profiles/';

                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                        $payload['profile_image'] = $newFileName;
                    }
                }
            }

            if (empty($payload['profile_image']) && empty($userProfile->profile_image)) {
                $this->error('Profile photo is required. Please upload and crop a photo.', 422);
            }

            if ($this->userRepo->updateProfile($_SESSION['user_id'], $payload)) {
                $_SESSION['full_name'] = $payload['first_name'] . ' ' . $payload['last_name'];
                if ($role === 'User') {
                    $this->userRepo->updateEditRestriction($_SESSION['user_id'], 1);
                }
                logAction('Update', 'Settings', 'Updated personal profile information.');
                $this->ok(['message' => 'Profile updated successfully.']);
            }
            $this->error('Failed to update profile.', 500);
        }

        if (!empty($data['new_password'])) {
            $current = $data['current_password'] ?? '';
            $new = $data['new_password'];
            $confirm = $data['confirm_password'] ?? '';

            if (empty($current)) {
                $this->error('Please enter your current password.', 422);
            } elseif ($new !== $confirm) {
                $this->error('New passwords do not match.', 422);
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
                        $this->ok(['message' => 'Password updated successfully.']);
                    }
                    $this->error('Something went wrong while updating the password.', 500);
                }
                $this->error('Incorrect current password.', 422);
            }
        }

        if ($role === 'Superadmin' && isset($data['system_settings'])) {
            $settings = [
                'system_name' => $data['system_name'] ?? '',
                'admin_email' => $data['admin_email'] ?? '',
                'contact_phone' => $data['contact_phone'] ?? '',
                'maintenance_mode' => $data['maintenance_mode'] ?? 'off',
                'allow_registration' => $data['allow_registration'] ?? 'off'
            ];

            foreach ($settings as $key => $val) {
                $this->settingModel->update($key, $val);
            }
            logAction('Update', 'Settings', 'Updated system-wide configuration.');
            $this->ok(['message' => 'System settings updated successfully!']);
        }

        $this->error('No valid action provided.', 422);
    }

    public function toggle_edit($userId) {
        $this->requireAnyRoleApi(['Admin', 'Superadmin']);

        $user = $this->userRepo->getById($userId);
        if (!$user) {
            $this->error('User not found.', 404);
        }

        $newStatus = $user->can_edit_profile ? 0 : 1;
        if ($this->userRepo->toggleEditPermission($userId, $newStatus)) {
            $msg = $newStatus ? "Profile editing enabled for " . $user->username : "Profile editing disabled for " . $user->username;
            logAction('Update', 'Users', $msg);
            $this->ok(['message' => $msg]);
        }

        $this->error('Failed to update permission.', 500);
    }

    public function backup() {
        $this->requireRoleApi('Superadmin');

        $filename = 'backup_' . DB_NAME . '_' . date('Y-m-d_H-i-s') . '.sql';
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

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
