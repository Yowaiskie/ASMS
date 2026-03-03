<?php

namespace App\Controllers\Api;

use App\Repositories\UserRepository;
use App\Repositories\ServerRepository;

class ArchiveController extends ApiController {
    private $userRepo;
    private $serverRepo;

    public function __construct() {
        $this->requireRoleApi('Superadmin');
        $this->userRepo = new UserRepository();
        $this->serverRepo = new ServerRepository();
    }

    public function index() {
        $archivedUsers = $this->userRepo->getArchived();
        $this->ok(['archived' => $archivedUsers]);
    }

    public function restore($id) {
        if ($this->userRepo->restore($id)) {
            logAction('Restore', 'Archives', "Restored user account ID: $id");
            $this->ok(['message' => 'Account restored successfully.']);
        }
        $this->error('Failed to restore account.', 500);
    }

    public function delete($id) {
        if ($this->userRepo->deletePermanently($id)) {
            logAction('Delete Permanent', 'Archives', "Permanently deleted user account ID: $id");
            $this->ok(['message' => 'Account permanently removed from the system.']);
        }
        $this->error('Failed to delete account permanently.', 500);
    }
}
