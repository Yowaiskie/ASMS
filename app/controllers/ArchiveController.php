<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepository;
use App\Repositories\ServerRepository;

class ArchiveController extends Controller {
    private $userRepo;
    private $serverRepo;

    public function __construct() {
        $this->requireLogin();
        // Strictly Superadmin only
        if (($_SESSION['role'] ?? '') !== 'Superadmin') {
            $this->forbidden();
        }
        $this->userRepo = new UserRepository();
        $this->serverRepo = new ServerRepository();
    }

    public function index() {
        $archivedUsers = $this->userRepo->getArchived();
        
        $this->view('archives/index', [
            'pageTitle' => 'Archive Center',
            'title' => 'Archives | ASMS',
            'archived' => $archivedUsers
        ]);
    }

    public function restore($id) {
        if ($this->userRepo->restore($id)) {
            logAction('Restore', 'Archives', "Restored user account ID: $id");
            setFlash('msg_success', 'Account restored successfully.');
        } else {
            setFlash('msg_error', 'Failed to restore account.');
        }
        redirect('archives');
    }

    public function delete($id) {
        if ($this->userRepo->deletePermanently($id)) {
            logAction('Delete Permanent', 'Archives', "Permanently deleted user account ID: $id");
            setFlash('msg_success', 'Account permanently removed from the system.');
        } else {
            setFlash('msg_error', 'Failed to delete account permanently.');
        }
        redirect('archives');
    }
}
