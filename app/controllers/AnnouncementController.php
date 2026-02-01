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
        $this->view('announcements/index', [
            'pageTitle' => 'Announcements',
            'title' => 'Announcements | ASMS',
            'announcements' => $announcements
        ]);
    }

    public function store() {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'title' => trim($_POST['title']),
                'category' => trim($_POST['category']),
                'message' => trim($_POST['message']),
                'author' => $_SESSION['username'] ?? 'Admin'
            ];

            if ($this->announcementRepo->create($data)) {
                setFlash('msg_success', 'Announcement posted successfully!');
            } else {
                setFlash('msg_error', 'Failed to post announcement.');
            }
            redirect('announcements');
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id && $this->announcementRepo->delete($id)) {
            setFlash('msg_success', 'Announcement deleted.');
        } else {
            setFlash('msg_error', 'Failed to delete announcement.');
        }
        redirect('announcements');
    }
}