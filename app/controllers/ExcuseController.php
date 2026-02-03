<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ExcuseRepository;
use App\Repositories\UserRepository;

class ExcuseController extends Controller {
    private $excuseRepo;
    private $userRepo;

    public function __construct() {
        $this->requireLogin();
        $this->excuseRepo = new ExcuseRepository();
        $this->userRepo = new UserRepository();
    }

    public function index() {
        if (($_SESSION['role'] ?? '') === 'User') {
            $excuses = $this->excuseRepo->getByUserId($_SESSION['user_id']);
            
            $this->view('excuses/user_index', [
                'pageTitle' => 'File Excuse Letter',
                'title' => 'Excuses | ASMS',
                'excuses' => $excuses
            ]);
        } else {
            // Admin View
            $this->excuseRepo->markAsSeen($_SESSION['user_id']);
            
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $excuses = $this->excuseRepo->getAll($limit, $offset);
            $totalRecords = $this->excuseRepo->countAll();
            $totalPages = ceil($totalRecords / $limit);

            $this->view('excuses/index', [
                'pageTitle' => 'Manage Excuse Letters',
                'title' => 'Excuse Management | ASMS',
                'excuses' => $excuses,
                'pagination' => [
                    'page' => $page,
                    'totalPages' => $totalPages
                ]
            ]);
        }
    }

    public function updateStatus() {
        $this->verifyCsrf();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_SESSION['role'] ?? '') !== 'User') {
            $id = $_POST['id'];
            $status = $_POST['status'];

            if ($this->excuseRepo->updateStatus($id, $status)) {
                // Email Notification
                $db = \App\Core\Database::getInstance();
                $db->query("
                    SELECT s.email, s.name, e.reason 
                    FROM excuses e
                    JOIN servers s ON e.server_id = s.id 
                    WHERE e.id = :id
                ");
                $db->bind(':id', $id);
                $info = $db->single();

                if ($info && $info->email) {
                    $color = $status === 'Approved' ? '#10b981' : '#ef4444';
                    sendEmailNotification(
                        $info->email,
                        'Excuse Letter Update',
                        "Your Excuse Letter was {$status}!",
                        "Hi {$info->name}, your excuse letter for '{$info->reason}' has been <b style='color:{$color}'>{$status}</b> by the coordinator."
                    );
                }

                logAction('Update', 'Excuses', "Updated excuse ID: $id status to $status");
            } else {
                setFlash('msg_error', "Failed to update status.");
            }
            redirect('excuses');
        }
    }

    public function store() {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_SESSION['role'] ?? '') === 'User') {
            
            // Get Server ID
            $user = $this->userRepo->getById($_SESSION['user_id']);
            if (!$user || !$user->server_id) {
                setFlash('msg_error', 'Profile not linked to a server record.');
                redirect('excuses');
                return;
            }

            $data = [
                'server_id' => $user->server_id,
                'type' => $_POST['type'],
                'absence_date' => $_POST['date'],
                'absence_time' => $_POST['time'] ?? null,
                'reason' => trim($_POST['reason']),
                'image_path' => null
            ];

            // Handle Image Upload
            if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['proof_image']['tmp_name'];
                $fileName = $_FILES['proof_image']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = '../public/uploads/excuses/';
                    
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    if(move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                        $data['image_path'] = $newFileName;
                    }
                }
            }

            if ($this->excuseRepo->create($data)) {
                // Notify Admins
                $admins = $this->userRepo->getAdmins();
                foreach ($admins as $admin) {
                    sendEmailNotification(
                        $admin->email,
                        'New Excuse Letter Filed',
                        'A server has filed an excuse letter',
                        "User <b>{$_SESSION['full_name']}</b> has submitted a new excuse letter for <b>" . date('M d, Y', strtotime($data['absence_date'])) . "</b>. <br><br><b>Reason:</b> {$data['reason']}"
                    );
                }

                setFlash('msg_success', 'Excuse letter submitted successfully.');
            } else {
                setFlash('msg_error', 'Failed to submit excuse letter.');
            }
            redirect('excuses');
        }
    }
}