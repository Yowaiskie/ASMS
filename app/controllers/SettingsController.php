<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepository;

class SettingsController extends Controller {
    private $userRepo;

    public function __construct() {
        $this->requireLogin();
        $this->userRepo = new UserRepository();
    }

    public function index() {
        $userProfile = $this->userRepo->getUserProfile($_SESSION['user_id']);

        // Fallback if no profile found
        if (!$userProfile) {
            $userProfile = (object)[
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'],
                'profile_image' => null,
                'name' => '',
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
                'pageTitle' => 'System Settings',
                'title' => 'Settings | ASMS',
                'profile' => $userProfile
            ]);
        }
    }

    public function store() {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $action = $_POST['action'] ?? '';

            // Handle Profile Update (Name, Age, Address, Photo, etc.)
            if (($_SESSION['role'] ?? '') === 'User' && $action === 'update_profile') {
                $data = [
                    'name' => trim($_POST['name']),
                    'age' => trim($_POST['age']),
                    'address' => trim($_POST['address']),
                    'phone' => trim($_POST['phone']),
                    'email' => trim($_POST['email'])
                ];

                // Handle File Upload
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['profile_image']['tmp_name'];
                    $fileName = $_FILES['profile_image']['name'];
                    $fileSize = $_FILES['profile_image']['size'];
                    $fileType = $_FILES['profile_image']['type'];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));

                    $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
                    
                    if (in_array($fileExtension, $allowedfileExtensions)) {
                        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                        $uploadFileDir = '../public/uploads/profiles/';
                        
                        if (!is_dir($uploadFileDir)) {
                            mkdir($uploadFileDir, 0755, true);
                        }

                        $dest_path = $uploadFileDir . $newFileName;

                        if(move_uploaded_file($fileTmpPath, $dest_path)) {
                            $data['profile_image'] = $newFileName;
                        } else {
                            setFlash('msg_error', 'There was an error moving the uploaded file.');
                        }
                    } else {
                        setFlash('msg_error', 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions));
                    }
                }

                if ($this->userRepo->updateProfile($_SESSION['user_id'], $data)) {
                    setFlash('msg_success', 'Profile updated successfully.');
                } else {
                    setFlash('msg_error', 'Failed to update profile.');
                }
                redirect('settings');
                return;
            }

            // Handle User Password Update
            if (($_SESSION['role'] ?? '') === 'User' && $action === 'update_password') {
                $current = $_POST['current_password'];
                $new = $_POST['new_password'];
                $confirm = $_POST['confirm_password'];

                if ($new !== $confirm) {
                    setFlash('msg_error', 'New passwords do not match.');
                    redirect('settings');
                    return;
                }

                $user = $this->userRepo->getById($_SESSION['user_id']);
                
                if ($user && password_verify($current, $user->password)) {
                    $hashed = password_hash($new, PASSWORD_DEFAULT);
                    if ($this->userRepo->update($_SESSION['user_id'], ['password' => $hashed])) {
                        setFlash('msg_success', 'Password updated successfully.');
                    } else {
                        setFlash('msg_error', 'Failed to update password.');
                    }
                } else {
                    setFlash('msg_error', 'Incorrect current password.');
                }
                redirect('settings');
                return;
            }

            // Admin Logic
            // In a real app, you'd save these to a 'settings' table in DB
            // For now, we'll mock success
            setFlash('msg_success', 'System settings updated successfully!');
            redirect('settings');
        }
    }
}