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
            $excuses = $this->excuseRepo->getAll();
            $this->view('excuses/index', [
                'pageTitle' => 'Manage Excuse Letters',
                'title' => 'Excuse Management | ASMS',
                'excuses' => $excuses
            ]);
        }
    }

    public function updateStatus() {
        $this->verifyCsrf();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_SESSION['role'] ?? '') !== 'User') {
            $id = $_POST['id'];
            $status = $_POST['status'];

            if ($this->excuseRepo->updateStatus($id, $status)) {
                setFlash('msg_success', "Excuse letter marked as $status.");
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
                setFlash('msg_success', 'Excuse letter submitted successfully.');
            } else {
                setFlash('msg_error', 'Failed to submit excuse letter.');
            }
            redirect('excuses');
        }
    }
}