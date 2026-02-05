<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ServerRepository;

class ServerController extends Controller {
    private $serverRepo;

    public function __construct() {
        $this->requireLogin();
        // Restrict to Admin and Superadmin
        $role = $_SESSION['role'] ?? 'User';
        if ($role !== 'Admin' && $role !== 'Superadmin') {
            $this->forbidden();
        }
        $this->serverRepo = new ServerRepository();
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search' => $_GET['search'] ?? '',
            'rank' => $_GET['rank'] ?? '',
            'team' => $_GET['team'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];

        $servers = $this->serverRepo->search($filters, $limit, $offset);
        $totalRecords = $this->serverRepo->countSearch($filters);
        $totalPages = $totalRecords > 0 ? (int)ceil($totalRecords / $limit) : 0;

        $this->view('servers/index', [
            'pageTitle' => 'Altar Servers Directory',
            'title' => 'Servers | ASMS',
            'servers' => $servers,
            'filters' => $filters,
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

        // Prepare Logos
        $logoPath = __DIR__ . '/../../public/images/logo.png';
        $parishLogoPath = __DIR__ . '/../../public/images/parish-logo.png';
        
        $logoData = '';
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoData = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $parLogoData = '';
        if (file_exists($parishLogoPath)) {
            $type = pathinfo($parishLogoPath, PATHINFO_EXTENSION);
            $parLogoData = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($parishLogoPath));
        }

        $html = '
        <html>
        <head>
            <style>
                body { font-family: Helvetica, sans-serif; color: #333; margin: 10px; }
                .header { border-bottom: 2px solid #1e63d4; padding-bottom: 15px; margin-bottom: 20px; }
                .logo { width: 55px; height: auto; }
                
                .header-table { width: 100%; border: none; border-collapse: collapse; }
                .header-side { width: 20%; text-align: center; border: none; }
                .header-center { width: 60%; text-align: center; border: none; }
                
                .parish-name { font-size: 14px; font-weight: bold; color: #1e63d4; text-transform: uppercase; margin: 0; }
                .ministry-name { font-size: 11px; font-weight: bold; color: #444; margin: 2px 0; }
                .report-title { font-size: 18px; font-weight: 900; color: #1a202c; margin-top: 10px; text-transform: uppercase; letter-spacing: 1px; }
                .report-subtitle { font-size: 10px; color: #666; margin-top: 2px; }

                table.data-table { width: 100%; border-collapse: collapse; font-size: 9px; margin-top: 10px; }
                table.data-table th { background-color: #f8fafc; color: #1e293b; font-weight: bold; text-transform: uppercase; padding: 8px; border: 1px solid #e2e8f0; text-align: left; }
                table.data-table td { padding: 8px; border: 1px solid #e2e8f0; color: #334155; vertical-align: top; }
                
                .font-bold { font-weight: bold; color: #0f172a; }
                .text-center { text-align: center; }
                .status-active { color: #059669; font-weight: bold; }
                
                .footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 1px solid #e2e8f0; padding-top: 10px; }
                .signatories { width: 100%; margin-top: 30px; }
                .sig-box { text-align: center; width: 45%; }
                .sig-line { border-top: 1.5px solid #333; width: 200px; margin: 40px auto 5px; font-weight: bold; font-size: 10px; }
                .sig-label { font-size: 8px; color: #64748b; }
            </style>
        </head>
        <body>
            <div class="header">
                <table class="header-table">
                    <tr>
                        <td class="header-side">
                            ' . ($logoData ? '<img src="' . $logoData . '" class="logo">' : '') . '
                        </td>
                        <td class="header-center">
                            <div class="parish-name">Sacred Heart of Jesus Parish</div>
                            <div class="ministry-name">Ministry of Altar Servers (MAS-SHJP MBS)</div>
                            <div class="report-title">Official Master List</div>
                            <div class="report-subtitle">Generated on ' . date('F d, Y') . '</div>
                        </td>
                        <td class="header-side">
                            ' . ($parLogoData ? '<img src="' . $parLogoData . '" class="logo">' : '') . '
                        </td>
                    </tr>
                </table>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th width="3%">#</th>
                        <th width="18%">Server Name</th>
                        <th width="8%">Nickname</th>
                        <th width="15%">Home Address</th>
                        <th width="10%">Date of Birth</th>
                        <th width="10%">Contact</th>
                        <th width="8%">Joined</th>
                        <th width="10%">Rank</th>
                        <th width="10%">Position</th>
                        <th width="8%">Status</th>
                    </tr>
                </thead>
                <tbody>';
        
        $i = 1;
        foreach ($servers as $s) {
            $html .= '<tr>
                <td class="text-center">' . $i++ . '.</td>
                <td class="font-bold">' . h(strtoupper($s->name)) . '</td>
                <td>' . h($s->nickname) . '</td>
                <td style="font-size: 8px;">' . h($s->address) . '</td>
                <td class="text-center">' . ($s->dob ? date('F d, Y', strtotime($s->dob)) : '-') . '</td>
                <td>' . h($s->phone) . '</td>
                <td class="text-center">' . h($s->month_joined) . '</td>
                <td class="text-center">' . h($s->rank) . '</td>
                <td class="text-center">' . h($s->position) . '</td>
                <td class="text-center status-active">' . h($s->status) . '</td>
            </tr>';
        }

        $html .= '</tbody></table>

            <div class="footer">
                <table class="signatories">
                    <tr>
                        <td class="sig-box">
                            <div class="sig-line">BRO. BENAIKA LORENZO PARONABLE</div>
                            <div class="sig-label">Admin Officer, MAS-SHJP MBS</div>
                        </td>
                        <td width="10%"></td>
                        <td class="sig-box">
                            <div class="sig-line">BRO. KYLE VINCENT MADRIAGA</div>
                            <div class="sig-label">Coordinator, MAS-SHJP MBS</div>
                        </td>
                    </tr>
                </table>
                <div style="text-align: right; font-size: 7px; color: #94a3b8; margin-top: 15px;">
                    ASMS System Generated Document • Page 1 of 1
                </div>
            </div>
        </body></html>';

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', true);
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