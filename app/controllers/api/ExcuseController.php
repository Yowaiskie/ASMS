<?php

namespace App\Controllers\Api;

use App\Repositories\ExcuseRepository;
use App\Repositories\UserRepository;

class ExcuseController extends ApiController {
    private $excuseRepo;
    private $userRepo;
    private $db;

    public function __construct() {
        $this->excuseRepo = new ExcuseRepository();
        $this->userRepo = new UserRepository();
        $this->db = \App\Core\Database::getInstance();
    }

    public function index() {
        $this->requireLoginApi();

        if (($_SESSION['role'] ?? '') === 'User') {
            $user = $this->userRepo->getById($_SESSION['user_id']);
            $excuses = $this->excuseRepo->getByUserId($_SESSION['user_id']);
            $assignedSchedules = [];

            if ($user && $user->server_id) {
                $this->db->query("
                    SELECT s.mass_type, s.mass_date, s.mass_time 
                    FROM attendance a 
                    JOIN schedules s ON a.schedule_id = s.id 
                    WHERE a.server_id = :sid AND s.mass_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                    ORDER BY s.mass_date DESC, s.mass_time DESC
                ");
                $this->db->bind(':sid', $user->server_id);
                $assignedSchedules = $this->db->resultSet();
            }

            $this->ok([
                'excuses' => $excuses,
                'schedules' => $assignedSchedules
            ]);
        }

        $this->excuseRepo->markAsSeen($_SESSION['user_id']);
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $excuses = $this->excuseRepo->getAll($limit, $offset);
        $totalRecords = $this->excuseRepo->countAll();
        $totalPages = $totalRecords > 0 ? (int)ceil($totalRecords / $limit) : 0;

        $this->ok([
            'excuses' => $excuses,
            'pagination' => [
                'page' => $page,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords
            ]
        ]);
    }

    public function store() {
        $this->requireRoleApi('User');
        $this->verifyCsrfApi();

        $data = $this->getRequestData();
        $user = $this->userRepo->getById($_SESSION['user_id']);
        if (!$user || !$user->server_id) {
            $this->error('Profile not linked to a server record.', 422);
        }

        $type = !empty($data['type']) ? $data['type'] : ($data['manual_type'] ?? null);
        $date = !empty($data['date']) ? $data['date'] : ($data['manual_date'] ?? null);
        $time = !empty($data['time']) ? $data['time'] : ($data['manual_time'] ?? null);

        if (empty($type) || empty($date)) {
            $this->error('Activity type and date are required.', 422);
        }

        $payload = [
            'server_id' => $user->server_id,
            'type' => $type,
            'absence_date' => $date,
            'absence_time' => $time,
            'reason' => trim($data['reason'] ?? ''),
            'image_path' => null
        ];

        if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['proof_image']['tmp_name'];
            $fileName = $_FILES['proof_image']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

            if (in_array($fileExtension, $allowedfileExtensions, true)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = '../public/uploads/excuses/';

                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                    $payload['image_path'] = $newFileName;
                }
            }
        }

        if ($this->excuseRepo->create($payload)) {
            $admins = $this->userRepo->getAdmins();
            foreach ($admins as $admin) {
                sendEmailNotification(
                    $admin->email,
                    'New Excuse Letter Filed',
                    'A server has filed an excuse letter',
                    "User <b>{$_SESSION['full_name']}</b> has submitted a new excuse letter for <b>" . date('M d, Y', strtotime($payload['absence_date'])) . "</b>. <br><br><b>Reason:</b> {$payload['reason']}"
                );
            }
            $this->ok(['message' => 'Excuse letter submitted successfully.']);
        }

        $this->error('Failed to submit excuse letter.', 500);
    }

    public function updateStatus() {
        $this->requireAnyRoleApi(['Admin', 'Superadmin']);
        $this->verifyCsrfApi();

        $data = $this->getRequestData();
        $id = $data['id'] ?? null;
        $status = $data['status'] ?? null;

        if (!$id || !$status) {
            $this->error('ID and status are required.', 422);
        }

        if ($this->excuseRepo->updateStatus($id, $status)) {
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
            $this->ok(['message' => 'Status updated.']);
        }

        $this->error('Failed to update status.', 500);
    }

    public function bulkDelete() {
        $this->requireAnyRoleApi(['Admin', 'Superadmin']);
        $this->verifyCsrfApi();

        $data = $this->getRequestData();
        $allSelected = isset($data['all_selected']) && $data['all_selected'] == '1';

        if ($allSelected) {
            if ($this->excuseRepo->deleteAll()) {
                logAction('Delete', 'Excuses', "Bulk deleted ALL excuse letters.");
                $this->ok(['message' => 'Deleted all excuse letters successfully.']);
            }
            $this->error('Failed to delete all letters.', 500);
        }

        $ids = $data['ids'] ?? [];
        if (empty($ids)) {
            $this->error('No items selected.', 422);
        }

        $count = 0;
        foreach ($ids as $id) {
            if ($this->excuseRepo->delete($id)) {
                $count++;
            }
        }
        logAction('Delete', 'Excuses', "Bulk deleted $count excuse letters.");
        $this->ok(['message' => "Deleted $count excuse letters successfully."]);
    }

    public function markSeen() {
        $this->requireAnyRoleApi(['Admin', 'Superadmin']);
        $this->excuseRepo->markAsSeen($_SESSION['user_id']);
        $this->ok(['marked' => true]);
    }
}
