<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\AnnouncementRepository;

class AnnouncementController extends Controller {
    private $announcementRepo;

    public function __construct() {
        $this->requireLogin();
        $this->announcementRepo = new AnnouncementRepository();
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $announcements = $this->announcementRepo->getAll($limit, $offset);
        $totalRecords = $this->announcementRepo->countAll();
        $totalPages = ceil($totalRecords / $limit);
        
        // Mark as read when ANY user views the list
        if (isset($_SESSION['user_id'])) {
            $this->announcementRepo->markAsRead($_SESSION['user_id']);
        }

        $data = [
            'pageTitle' => 'Announcements',
            'title' => 'Announcements | ASMS',
            'announcements' => $announcements,
            'pagination' => [
                'page' => $page,
                'totalPages' => $totalPages
            ]
        ];

        if (isset($_SESSION['role']) && $_SESSION['role'] === 'User') {
            $this->view('announcements/user_index', $data);
        } else {
            $this->view('announcements/index', $data);
        }
    }

    public function store() {
        $this->verifyCsrf();
        $page = $_POST['page'] ?? 1;

        if (($_SESSION['role'] ?? '') === 'User') {
            setFlash('msg_error', 'Unauthorized access.');
            redirect('announcements?page=' . $page);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // ... (keep logic) ...
            $data = [
                'title' => trim($_POST['title']),
                'category' => trim($_POST['category']),
                'message' => trim($_POST['message']),
                'author' => $_SESSION['full_name'] ?? 'Admin'
            ];

            if ($this->announcementRepo->create($data)) {
                logAction('Create', 'Announcements', "Created announcement: " . $data['title']);
                setFlash('msg_success', 'Announcement posted successfully!');
            } else {
                setFlash('msg_error', 'Failed to post announcement.');
            }
            redirect('announcements?page=' . $page);
        }
    }

    public function delete() {
        $page = $_GET['page'] ?? 1;
        if (($_SESSION['role'] ?? '') === 'User') {
            setFlash('msg_error', 'Unauthorized access to Audit Logs.');
            redirect('announcements?page=' . $page);
            return;
        }

        $id = $_GET['id'] ?? null;
        $announcement = $this->announcementRepo->getById($id);
        $title = $announcement ? $announcement->title : "ID: $id";

        if ($id && $this->announcementRepo->delete($id)) {
            logAction('Delete', 'Announcements', "Deleted announcement: $title");
            setFlash('msg_success', 'Announcement deleted.');
        } else {
            setFlash('msg_error', 'Failed to delete announcement.');
        }
        redirect('announcements?page=' . $page);
    }
}