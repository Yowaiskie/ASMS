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
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $servers = $this->serverRepo->getAll($limit, $offset);
        $totalRecords = $this->serverRepo->countAll();
        $totalPages = $totalRecords > 0 ? (int)ceil($totalRecords / $limit) : 0;

        $this->view('servers/index', [
            'pageTitle' => 'Altar Servers Directory',
            'title' => 'Servers | ASMS',
            'servers' => $servers,
            'pagination' => [
                'page' => $page,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords
            ]
        ]);
    }

    public function store() {
        $this->verifyCsrf();
        $page = $_POST['page'] ?? 1;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? '';
            $data = [
                'first_name' => trim($_POST['first_name']),
                'middle_name' => trim($_POST['middle_name'] ?? ''),
                'last_name' => trim($_POST['last_name']),
                'nickname' => trim($_POST['nickname'] ?? ''),
                'dob' => $_POST['dob'] ?? null,
                'age' => trim($_POST['age'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'rank' => trim($_POST['rank']),
                'team' => trim($_POST['team'] ?? 'Unassigned'),
                'status' => trim($_POST['status']),
                'month_joined' => trim($_POST['month_joined'] ?? ''),
                'investiture_date' => $_POST['investiture_date'] ?? null,
                'order_name' => trim($_POST['order_name'] ?? ''),
                'position' => trim($_POST['position'] ?? '')
            ];

            if (!empty($id)) {
                if ($this->serverRepo->update($id, $data)) {
                    logAction('Update', 'Servers', 'Updated server profile: ' . $data['first_name'] . ' ' . $data['last_name']);
                    setFlash('msg_success', 'Server profile updated.');
                } else {
                    setFlash('msg_error', 'Update failed.');
                }
            } else {
                if ($this->serverRepo->create($data)) {
                    $db = \App\Core\Database::getInstance();
                    $serverId = $db->lastInsertId();
                    
                    // Create User Account automatically
                    $userRepo = new \App\Repositories\UserRepository();
                    
                    // Username Generation: Primary source is First Name
                    $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['first_name']));
                    $username = $baseUsername;
                    
                    // Check if username exists
                    if ($userRepo->findByUsername($username)) {
                        // Try First + Last Name
                        $username = $baseUsername . '.' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['last_name']));
                        
                        // If still exists, append unique ID
                        if ($userRepo->findByUsername($username)) {
                            $username .= $serverId;
                        }
                    }

                    $userData = [
                        'username' => $username,
                        'password' => password_hash(DEFAULT_USER_PASSWORD, PASSWORD_DEFAULT),
                        'role' => 'User',
                        'server_id' => $serverId
                    ];
                    
                    $userRepo->create($userData);

                    logAction('Create', 'Servers', 'Registered new server and user account: ' . $data['first_name'] . ' ' . $data['last_name']);
                    setFlash('msg_success', 'Server registered and user account created! (Pass: 12345)');
                } else {
                    setFlash('msg_error', 'Registration failed.');
                }
            }
            redirect('servers?page=' . $page);
        }
    }

    public function updateStatus() {
        $this->verifyCsrf();
        $page = $_POST['page'] ?? 1;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $action = $_POST['action'];

            $server = $this->serverRepo->getById($id);
            $name = $server ? $server->name : "ID: $id";

            if ($action === 'suspend') {
                $until = date('Y-m-d', strtotime('+30 days'));
                if ($this->serverRepo->suspendServer($id, $until)) {
                    logAction('Update', 'Servers', "Manually suspended server: $name until $until");
                    setFlash('msg_success', "Server suspended until " . date('M d, Y', strtotime($until)));
                }
            } elseif ($action === 'unsuspend') {
                $this->db = \App\Core\Database::getInstance();
                $this->db->query("UPDATE servers SET status = 'Active', suspension_until = NULL WHERE id = :id");
                $this->db->bind(':id', $id);
                if ($this->db->execute()) {
                    logAction('Update', 'Servers', "Manually unsuspended server: $name");
                    setFlash('msg_success', "Server unsuspended successfully.");
                }
            }
            redirect('servers?page=' . $page);
        }
    }

    public function download_pdf() {
        $autoloadPath = __DIR__ . '/../../vendor/autoload.php';
        if (file_exists($autoloadPath)) {
            require_once $autoloadPath;
        } else {
            die("Please run 'composer require dompdf/dompdf'.");
        }

        $servers = $this->serverRepo->getAll();

        $logoPath = __DIR__ . '/../../public/images/logo.png';
        $logoData = '';
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoData = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $parishLogoPath = __DIR__ . '/../../public/images/parish-logo.png';
        $parishLogoData = '';
        if (file_exists($parishLogoPath)) {
            $type = pathinfo($parishLogoPath, PATHINFO_EXTENSION);
            $parishLogoData = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($parishLogoPath));
        }

        $html = '
        <html>
        <head>
            <style>
                @page { margin: 0.5cm; }
                body { font-family: "Helvetica", sans-serif; font-size: 9px; color: #000; }
                .header-container { text-align: center; margin-bottom: 20px; }
                .title { font-size: 18px; font-weight: bold; text-align: right; text-transform: uppercase; margin-bottom: 5px; }
                .subtitle { font-size: 10px; text-align: right; }
                table.master-list { width: 100%; border-collapse: collapse; clear: both; }
                table.master-list th { border: 1px solid #000; padding: 5px; text-align: left; font-weight: bold; text-transform: uppercase; background: #f0f0f0; }
                table.master-list td { border: 1px solid #000; padding: 5px; vertical-align: top; }
                .text-center { text-align: center; }
                .font-bold { font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="header-container">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="width: 30%; text-align: left; border: none; vertical-align: middle;">
                            <div style="font-size: 14px; font-weight: bold; font-family: serif; font-style: italic;">Ministry of Altar Servers</div>
                            <div style="font-weight: bold;">Sacred Heart of Jesus Parish - MBS</div>
                            <div style="font-size: 8px;">Pilar Rd., Morning Breeze Subdivision, Caloocan City</div>
                        </td>
                        <td style="width: 40%; text-align: center; border: none; vertical-align: middle;">
                            ' . ($logoData ? '<img src="' . $logoData . '" style="width: 55px; margin-right: 10px; vertical-align: middle;">' : '') . '
                            ' . ($parishLogoData ? '<img src="' . $parishLogoData . '" style="width: 55px; vertical-align: middle;">' : '') . '
                        </td>
                        <td style="width: 30%; text-align: right; border: none; vertical-align: middle;">
                            <div class="title">Official Master List</div>
                            <div class="subtitle">As of ' . date('F Y') . '</div>
                        </td>
                    </tr>
                </table>
            </div>

            <table class="master-list">
                <thead>
                    <tr>
                        <th width="3%">#</th>
                        <th width="15%">Name</th>
                        <th width="8%">Nickname</th>
                        <th width="18%">Home Address</th>
                        <th width="8%">DOB</th>
                        <th width="10%">Contact</th>
                        <th width="8%">Joined</th>
                        <th width="10%">Investiture</th>
                        <th width="8%">Order</th>
                        <th width="10%">Position</th>
                        <th width="5%">Rank</th>
                    </tr>
                </thead>
                <tbody>';
        
        $i = 1;
        foreach ($servers as $s) {
            $html .= '<tr>
                <td class="text-center">' . str_pad($i++, 2, '0', STR_PAD_LEFT) . '.</td>
                <td class="font-bold">' . h($s->name) . '</td>
                <td>' . h($s->nickname) . '</td>
                <td style="font-size: 8px;">' . h($s->address) . '</td>
                <td>' . ($s->dob ? date('m-d-Y', strtotime($s->dob)) : '') . '</td>
                <td>' . h($s->phone) . '</td>
                <td>' . h($s->month_joined) . '</td>
                <td>' . ($s->investiture_date ? date('M d, Y', strtotime($s->investiture_date)) : '') . '</td>
                <td>' . h($s->order_name) . '</td>
                <td>' . h($s->position) . '</td>
                <td>' . h($s->rank) . '</td>
            </tr>';
        }

        $html .= '</tbody></table>
            <div style="margin-top: 30px; width: 100%;">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="width: 50%; border: none;">
                            <div style="border-top: 1px solid #000; width: 200px; margin-top: 40px; text-align: center; font-weight: bold;">Bro. BENAIKA LORENZO PARONABLE</div>
                            <div style="text-align: left; padding-left: 20px;">Admin Officer, MAS-SHJP MBS</div>
                        </td>
                        <td style="text-align: right; border: none;">
                            <div style="border-top: 1px solid #000; width: 200px; margin-top: 40px; text-align: center; font-weight: bold; float: right;">Bro. KYLE VINCENT MADRIAGA</div>
                            <div style="clear: both; text-align: right; padding-right: 20px;">Coordinator, MAS-SHJP MBS</div>
                        </td>
                    </tr>
                </table>
            </div>
        </body></html>';

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Master_List_" . date('Y') . ".pdf", ["Attachment" => true]);
        exit;
    }

    public function import() {
        $this->verifyCsrf();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
            $fileName = $_FILES['csv_file']['tmp_name'];
            
            if ($_FILES['csv_file']['size'] > 0) {
                $file = fopen($fileName, "r");
                $count = 0;
                $skipped = 0;
                $userRepo = new \App\Repositories\UserRepository();
                $db = \App\Core\Database::getInstance();
                
                $firstRow = true;
                while (($line = fgetcsv($file, 10000, ",")) !== FALSE) {
                    $column = $line;
                    
                    // Delimiter Detection: If only 1 column found, try semicolon
                    if (count($column) == 1 && strpos($column[0], ';') !== false) {
                        $column = str_getcsv($column[0], ';');
                    }

                    if ($firstRow) { $firstRow = false; continue; } // Skip Header

                    $colCount = count($column);
                    if ($colCount < 1) { $skipped++; continue; }

                    // Clean BOM from first column
                    $column[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $column[0]);

                    if ($colCount <= 14) {
                        // --- OLD FORMAT (1 Name Column + 13 others = 14 total) ---
                        $fullName = trim($column[0]);
                        if (empty($fullName)) { $skipped++; continue; }

                        $parts = explode(' ', $fullName);
                        $lastName = (count($parts) > 1) ? array_pop($parts) : 'Server';
                        $firstName = (count($parts) >= 1) ? array_shift($parts) : $fullName;
                        $middleName = implode(' ', $parts);

                        $data = [
                            'first_name' => $firstName,
                            'middle_name' => $middleName,
                            'last_name' => $lastName,
                            'nickname' => trim($column[1] ?? ''),
                            'address' => trim($column[2] ?? ''),
                            'dob' => !empty($column[3]) ? date('Y-m-d', strtotime($column[3])) : null,
                            'age' => trim($column[4] ?? ''),
                            'phone' => trim($column[5] ?? ''),
                            'month_joined' => trim($column[6] ?? ''),
                            'investiture_date' => !empty($column[7]) ? date('Y-m-d', strtotime($column[7])) : null,
                            'order_name' => trim($column[8] ?? ''),
                            'position' => trim($column[9] ?? ''),
                            'rank' => trim($column[10] ?? 'Server'),
                            'team' => trim($column[11] ?? 'Unassigned'),
                            'status' => trim($column[12] ?? 'Active'),
                            'email' => trim($column[13] ?? '')
                        ];
                    } else {
                        // --- NEW FORMAT (3 Name Columns + 13 others = 16 total) ---
                        $data = [
                            'first_name' => trim($column[0] ?? ''),
                            'middle_name' => trim($column[1] ?? ''),
                            'last_name' => trim($column[2] ?? ''),
                            'nickname' => trim($column[3] ?? ''),
                            'address' => trim($column[4] ?? ''),
                            'dob' => !empty($column[5]) ? date('Y-m-d', strtotime($column[5])) : null,
                            'age' => trim($column[6] ?? ''),
                            'phone' => trim($column[7] ?? ''),
                            'month_joined' => trim($column[8] ?? ''),
                            'investiture_date' => !empty($column[9]) ? date('Y-m-d', strtotime($column[9])) : null,
                            'order_name' => trim($column[10] ?? ''),
                            'position' => trim($column[11] ?? ''),
                            'rank' => trim($column[12] ?? 'Server'),
                            'team' => trim($column[13] ?? 'Unassigned'),
                            'status' => trim($column[14] ?? 'Active'),
                            'email' => trim($column[15] ?? '')
                        ];
                    }

                    if (empty($data['first_name'])) { $skipped++; continue; }

                    if ($this->serverRepo->create($data)) {
                        $serverId = $db->lastInsertId();
                        $count++;

                        // Username Generation: Primary source is First Name
                        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['first_name']));
                        $username = $baseUsername;
                        
                        // Check if username exists
                        if ($userRepo->findByUsername($username)) {
                            // Try First + Last Name
                            $username = $baseUsername . '.' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['last_name']));
                            
                            // If still exists, append unique ID
                            if ($userRepo->findByUsername($username)) {
                                $username .= $serverId;
                            }
                        }

                        $userData = [
                            'username' => $username,
                            'password' => password_hash(DEFAULT_USER_PASSWORD, PASSWORD_DEFAULT),
                            'role' => 'User',
                            'server_id' => $serverId
                        ];
                        
                        $userRepo->create($userData);
                    } else {
                        $skipped++;
                    }
                }
                
                fclose($file);
                logAction('Create', 'Servers', "Imported $count servers. Skipped $skipped rows.");
                setFlash('msg_success', "Imported $count servers and accounts successfully. " . ($skipped > 0 ? "Skipped $skipped rows." : ""));
            } else {
                setFlash('msg_error', 'Empty file.');
            }
        } else {
            setFlash('msg_error', 'Invalid upload.');
        }
        redirect('servers');
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        $page = $_GET['page'] ?? 1;
        $server = $this->serverRepo->getById($id);
        $name = $server ? $server->name : "ID: $id";

        if ($id && $this->serverRepo->delete($id)) {
            logAction('Delete', 'Servers', "Removed server: $name");
            setFlash('msg_success', 'Server removed successfully.');
        } else {
            setFlash('msg_error', 'Failed to remove server.');
        }
        redirect('servers?page=' . $page);
    }

    public function bulkDelete() {
        $this->verifyCsrf();
        $page = $_POST['page'] ?? 1;
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['ids'])) {
            $ids = $_POST['ids'];
            $count = 0;
            $names = [];
            foreach ($ids as $id) {
                $server = $this->serverRepo->getById($id);
                if ($server) $names[] = $server->name;
                if ($this->serverRepo->delete($id)) {
                    $count++;
                }
            }
            logAction('Delete', 'Servers', "Bulk deleted $count servers: " . implode(', ', $names));
            setFlash('msg_success', "Deleted $count servers successfully.");
        } else {
            setFlash('msg_error', "No servers selected.");
        }
        redirect('servers?page=' . $page);
    }
}