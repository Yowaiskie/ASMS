<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ServerRepository;

class ServerController extends Controller {
    private $serverRepo;

    public function __construct() {
        $this->requireLogin();
        $this->serverRepo = new ServerRepository();
    }

    public function index() {
        $servers = $this->serverRepo->getAll();
        $this->view('servers/index', [
            'pageTitle' => 'Altar Servers Directory',
            'title' => 'Servers | ASMS',
            'servers' => $servers
        ]);
    }

    public function store() {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'rank' => trim($_POST['rank']),
                'team' => trim($_POST['team']),
                'email' => trim($_POST['email']),
                'status' => trim($_POST['status'])
            ];

            if ($this->serverRepo->create($data)) {
                logAction('Create', 'Servers', 'Registered new server: ' . $data['name']);
                setFlash('msg_success', 'Server registered successfully!');
            } else {
                setFlash('msg_error', 'Failed to register server.');
            }
            redirect('servers');
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        
        if ($id && $this->serverRepo->delete($id)) {
            logAction('Delete', 'Servers', 'Removed server ID: ' . $id);
            setFlash('msg_success', 'Server removed successfully.');
        } else {
            setFlash('msg_error', 'Failed to remove server.');
        }
        redirect('servers');
    }
}