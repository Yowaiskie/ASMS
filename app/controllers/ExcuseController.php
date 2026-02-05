<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ExcuseRepository;
use App\Repositories\UserRepository;

class ExcuseController extends Controller {
    private $excuseRepo;
    private $userRepo;
    private $db;

    public function __construct() {
        $this->requireLogin();
        $this->excuseRepo = new ExcuseRepository();
        $this->userRepo = new UserRepository();
        $this->db = \App\Core\Database::getInstance();
    }

    public function index() {
        if (($_SESSION['role'] ?? '') === 'User') {
            $user = $this->userRepo->getById($_SESSION['user_id']);
            $excuses = $this->excuseRepo->getByUserId($_SESSION['user_id']);
            
            // Fetch server's assigned schedules (for the dropdown)
            $assignedSchedules = [];
            if ($user && $user->server_id) {
                $db = \App\Core\Database::getInstance();
                $db->query("
                    SELECT s.mass_type, s.mass_date, s.mass_time 
                    FROM attendance a 
                    JOIN schedules s ON a.schedule_id = s.id 
                    WHERE a.server_id = :sid AND s.mass_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                    ORDER BY s.mass_date DESC, s.mass_time DESC
                ");
                $db->bind(':sid', $user->server_id);
                $assignedSchedules = $db->resultSet();
            }

            $this->view('excuses/user_index', [
                'pageTitle' => 'File Excuse Letter',
                'title' => 'Excuses | ASMS',
                'excuses' => $excuses,
                'schedules' => $assignedSchedules
            ]);
        } else {
            // Admin View
            $this->excuseRepo->markAsSeen($_SESSION['user_id']);
            
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $excuses = $this->excuseRepo->getAll($limit, $offset);
            $totalRecords = $this->excuseRepo->countAll();
            $totalPages = $totalRecords > 0 ? (int)ceil($totalRecords / $limit) : 0;

            $this->view('excuses/index', [
                'pageTitle' => 'Manage Excuse Letters',
                'title' => 'Excuse Management | ASMS',
                'excuses' => $excuses,
                'pagination' => [
                    'page' => $page,
                    'totalPages' => $totalPages,
                    'totalRecords' => $totalRecords
                ]
            ]);
        }
    }

    public function updateStatus() {
        $this->verifyCsrf();
        $page = $_POST['page'] ?? 1;
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_SESSION['role'] ?? '') !== 'User') {
            $id = $_POST['id'];
            $status = $_POST['status'];

            if ($this->excuseRepo->updateStatus($id, $status)) {
                // Email Notification
                $this->db->query("
                    SELECT s.email, CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) as name, e.reason 
                    FROM excuses e
                    JOIN servers s ON e.server_id = s.id 
                    WHERE e.id = :id
                ");
                $this->db->bind(':id', $id);
                $info = $this->db->single();

                if ($info && $info->email) {
                    $color = $status === 'Approved' ? '#10b981' : '#ef4444';
                    sendEmailNotification(
                        $info->email,
                        'Excuse Letter Update',
                        "Your Excuse Letter was {$status}!",
                        "Hi {$info->name}, your excuse letter for '{$info->reason}' has been <b style='color:{$color}'>{$status}</b> by the coordinator."
                    );
                }

                $logName = $info ? $info->name : "ID: $id";
                logAction('Update', 'Excuses', "Updated excuse status for $logName to $status");
            } else {
                setFlash('msg_error', "Failed to update status.");
            }
            redirect('excuses?page=' . $page);
        }
    }

    public function bulkDelete() {
        $this->verifyCsrf();
        $page = $_POST['page'] ?? 1;
        $allSelected = isset($_POST['all_selected']) && $_POST['all_selected'] == '1';

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_SESSION['role'] ?? '') !== 'User') {
            if ($allSelected) {
                if ($this->excuseRepo->deleteAll()) {
                    logAction('Delete', 'Excuses', "Bulk deleted ALL excuse letters.");
                    setFlash('msg_success', "Deleted all excuse letters successfully.");
                } else {
                    setFlash('msg_error', "Failed to delete all letters.");
                }
            } elseif (!empty($_POST['ids'])) {
                $ids = $_POST['ids'];
                $count = 0;
                foreach ($ids as $id) {
                    if ($this->excuseRepo->delete($id)) {
                        $count++;
                    }
                }
                logAction('Delete', 'Excuses', "Bulk deleted $count excuse letters.");
                setFlash('msg_success', "Deleted $count excuse letters successfully.");
            } else {
                setFlash('msg_error', "No items selected.");
            }
            redirect('excuses?page=' . $page);
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

            // If hidden values are empty, use manual input (Others)
            $type = !empty($_POST['type']) ? $_POST['type'] : ($_POST['manual_type'] ?? null);
            $date = !empty($_POST['date']) ? $_POST['date'] : ($_POST['manual_date'] ?? null);
            $time = !empty($_POST['time']) ? $_POST['time'] : ($_POST['manual_time'] ?? null);

            if (empty($type) || empty($date)) {
                setFlash('msg_error', 'Activity type and date are required.');
                redirect('excuses');
                return;
            }

            $data = [
                'server_id' => $user->server_id,
                'type' => $type,
                'absence_date' => $date,
                'absence_time' => $time,
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