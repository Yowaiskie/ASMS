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
        $announcements = $this->announcementRepo->getAll();
        
        // Mark as read when ANY user views the list
        if (isset($_SESSION['user_id'])) {
            $this->announcementRepo->markAsRead($_SESSION['user_id']);
        }

        if (isset($_SESSION['role']) && $_SESSION['role'] === 'User') {
            $this->view('announcements/user_index', [
                'pageTitle' => 'Announcements',
                'title' => 'Announcements | ASMS',
                'announcements' => $announcements
            ]);
        } else {
            $this->view('announcements/index', [
                'pageTitle' => 'Announcements',
                'title' => 'Announcements | ASMS',
                'announcements' => $announcements
            ]);
        }
    }

    public function store() {
        $this->verifyCsrf();

        if (($_SESSION['role'] ?? '') === 'User') {
            setFlash('msg_error', 'Unauthorized access.');
            redirect('announcements');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'title' => trim($_POST['title']),
                'category' => trim($_POST['category']),
                'message' => trim($_POST['message']),
                'author' => $_SESSION['username'] ?? 'Admin'
            ];

            if ($this->announcementRepo->create($data)) {
                logAction('Create', 'Announcements', "Created announcement: " . $data['title']);
                setFlash('msg_success', 'Announcement posted successfully!');
            } else {
                setFlash('msg_error', 'Failed to post announcement.');
            }
            redirect('announcements');
        }
    }

    public function delete() {
        if (($_SESSION['role'] ?? '') === 'User') {
            setFlash('msg_error', 'Unauthorized access.');
            redirect('announcements');
            return;
        }

        $id = $_GET['id'] ?? null;
        if ($id && $this->announcementRepo->delete($id)) {
            logAction('Delete', 'Announcements', "Deleted announcement ID: $id");
            setFlash('msg_success', 'Announcement deleted.');
        } else {
            setFlash('msg_error', 'Failed to delete announcement.');
        }
        redirect('announcements');
    }
}