<?php

namespace App\Controllers\Api;

use App\Repositories\AnnouncementRepository;
use App\Repositories\SystemSettingRepository;

class AnnouncementController extends ApiController {
    private $announcementRepo;
    private $systemRepo;

    public function __construct() {
        $this->announcementRepo = new AnnouncementRepository();
        $this->systemRepo = new SystemSettingRepository();
    }

    public function index() {
        $this->requireLoginApi();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $announcements = $this->announcementRepo->getAll($limit, $offset);
        $totalRecords = $this->announcementRepo->countAll();
        $totalPages = $totalRecords > 0 ? (int)ceil($totalRecords / $limit) : 0;
        $categories = $this->systemRepo->getCategories();

        if (isset($_SESSION['user_id'])) {
            $this->announcementRepo->markAsRead($_SESSION['user_id']);
        }

        $this->ok([
            'announcements' => $announcements,
            'categories' => $categories,
            'pagination' => [
                'page' => $page,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords
            ]
        ]);
    }

    public function store() {
        $this->requireAnyRoleApi(['Admin', 'Superadmin']);
        $this->verifyCsrfApi();

        $data = $this->getRequestData();
        $payload = [
            'title' => trim($data['title'] ?? ''),
            'category' => trim($data['category'] ?? ''),
            'message' => trim($data['message'] ?? ''),
            'author' => $_SESSION['full_name'] ?? 'Admin'
        ];

        if ($payload['title'] === '' || $payload['category'] === '' || $payload['message'] === '') {
            $this->error('Title, category, and message are required.', 422);
        }

        if ($this->announcementRepo->create($payload)) {
            logAction('Create', 'Announcements', "Created announcement: " . $payload['title']);
            $this->ok(['message' => 'Announcement posted successfully.']);
        }

        $this->error('Failed to post announcement.', 500);
    }

    public function delete() {
        $this->requireAnyRoleApi(['Admin', 'Superadmin']);
        $this->verifyCsrfApi();

        $data = $this->getRequestData();
        $id = $data['id'] ?? null;

        if ($id && $this->announcementRepo->delete($id)) {
            logAction('Delete', 'Announcements', "Deleted announcement ID: $id");
            $this->ok(['message' => 'Announcement deleted.']);
        }

        $this->error('Failed to delete announcement.', 500);
    }

    public function markRead() {
        $this->requireLoginApi();
        $this->announcementRepo->markAsRead($_SESSION['user_id']);
        $this->ok(['marked' => true]);
    }
}
