<?php

namespace App\Controllers\Api;

use App\Repositories\ServerRepository;
use App\Repositories\UserRepository;
use App\Repositories\SystemSettingRepository;

class ServerController extends ApiController {
    private $serverRepo;

    public function __construct() {
        $this->requireAnyRoleApi(['Admin', 'Superadmin']);
        $this->serverRepo = new ServerRepository();
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
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
        $ranks = (new SystemSettingRepository())->getRanks();

        $this->ok([
            'servers' => $servers,
            'ranks' => $ranks,
            'filters' => $filters,
            'pagination' => [
                'page' => $page,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords
            ]
        ]);
    }

    public function store() {
        $this->verifyCsrfApi();
        $data = $this->getRequestData();
        $id = $data['id'] ?? '';

        $payload = [
            'first_name' => trim($data['first_name'] ?? ''),
            'middle_name' => trim($data['middle_name'] ?? ''),
            'last_name' => trim($data['last_name'] ?? ''),
            'nickname' => trim($data['nickname'] ?? ''),
            'dob' => $data['dob'] ?? null,
            'age' => trim($data['age'] ?? ''),
            'address' => trim($data['address'] ?? ''),
            'phone' => trim($data['phone'] ?? ''),
            'email' => trim($data['email'] ?? ''),
            'rank' => trim($data['rank'] ?? ''),
            'team' => trim($data['team'] ?? 'Unassigned'),
            'status' => trim($data['status'] ?? ''),
            'month_joined' => trim($data['month_joined'] ?? ''),
            'investiture_date' => $data['investiture_date'] ?? null,
            'order_name' => trim($data['order_name'] ?? ''),
            'position' => trim($data['position'] ?? '')
        ];

        if (!empty($id)) {
            if ($this->serverRepo->update($id, $payload)) {
                logAction('Update', 'Servers', 'Updated server profile: ' . $payload['first_name'] . ' ' . $payload['last_name']);
                $this->ok(['message' => 'Server profile updated.']);
            }
            $this->error('Update failed.', 500);
        }

        if ($this->serverRepo->create($payload)) {
            $db = \App\Core\Database::getInstance();
            $serverId = $db->lastInsertId();

            $userRepo = new UserRepository();
            
            // Username Generation: Dynamic (Lastname + Firstname letters)
            $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $payload['last_name']));
            $firstNameClean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $payload['first_name']));
            $username = $baseUsername;
            
            $charCount = 0;
            $numSuffix = 2;
            
            while ($userRepo->isUsernameTaken($username)) {
                if ($charCount < strlen($firstNameClean)) {
                    // Try adding letters from first name one by one
                    $charCount++;
                    $username = $baseUsername . substr($firstNameClean, 0, $charCount);
                } else {
                    // If all letters used, start adding numbers
                    $username = $baseUsername . $firstNameClean . $numSuffix;
                    $numSuffix++;
                }
            }

            $userData = [
                'username' => $username,
                'password' => password_hash(DEFAULT_USER_PASSWORD, PASSWORD_DEFAULT),
                'role' => 'User',
                'server_id' => $serverId
            ];

            $userRepo->create($userData);
            logAction('Create', 'Servers', 'Registered new server and user account: ' . $payload['first_name'] . ' ' . $payload['last_name']);
            $this->ok([
                'message' => 'Server registered and user account created.',
                'server_id' => $serverId,
                'username' => $username
            ]);
        }

        $this->error('Registration failed.', 500);
    }

    public function updateStatus() {
        $this->verifyCsrfApi();
        $data = $this->getRequestData();
        $id = $data['id'] ?? null;
        $action = $data['action'] ?? null;

        if (!$id || !$action) {
            $this->error('ID and action are required.', 422);
        }

        $server = $this->serverRepo->getById($id);
        $name = $server ? $server->name : "ID: $id";

        if ($action === 'suspend') {
            $until = date('Y-m-d', strtotime('+30 days'));
            if ($this->serverRepo->suspendServer($id, $until)) {
                logAction('Update', 'Servers', "Manually suspended server: $name until $until");
                $this->ok(['message' => "Server suspended until " . date('M d, Y', strtotime($until))]);
            }
        } elseif ($action === 'unsuspend') {
            $db = \App\Core\Database::getInstance();
            $db->query("UPDATE servers SET status = 'Active', suspension_until = NULL WHERE id = :id");
            $db->bind(':id', $id);
            if ($db->execute()) {
                logAction('Update', 'Servers', "Manually unsuspended server: $name");
                $this->ok(['message' => 'Server unsuspended successfully.']);
            }
        }

        $this->error('Failed to update status.', 500);
    }

    public function downloadPdf() {
        $autoloadPath = __DIR__ . '/../../../vendor/autoload.php';
        if (file_exists($autoloadPath)) {
            require_once $autoloadPath;
        } else {
            $this->error("Please run 'composer require dompdf/dompdf'.", 500);
        }

        $servers = $this->serverRepo->getAll();
        $logoPath = __DIR__ . '/../../../public/images/logo.png';
        $parishLogoPath = __DIR__ . '/../../../public/images/parish-logo.png';

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
        $this->verifyCsrfApi();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
            $fileName = $_FILES['csv_file']['tmp_name'];

            if ($_FILES['csv_file']['size'] > 0) {
                $file = fopen($fileName, "r");
                $count = 0;
                $skipped = 0;
                $userRepo = new UserRepository();
                $db = \App\Core\Database::getInstance();

                $firstRow = true;
                while (($line = fgetcsv($file, 10000, ",")) !== false) {
                    $column = $line;
                    
                    // Delimiter Detection: If only 1 column found, try semicolon or comma
                    if (count($column) == 1) {
                        if (strpos($column[0], ';') !== false) {
                            $column = str_getcsv($column[0], ';');
                        } elseif (strpos($column[0], ',') !== false) {
                            $column = str_getcsv($column[0], ',');
                        }
                    }

                    if ($firstRow) { $firstRow = false; continue; }

                    $colCount = count($column);
                    if ($colCount < 1) { $skipped++; continue; }

                    $column[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $column[0]);

                    if ($colCount <= 14) {
                        $fullName = trim($column[0]);
                        if (empty($fullName)) { $skipped++; continue; }

                        $parts = explode(' ', $fullName);
                        $lastName = (count($parts) > 1) ? array_pop($parts) : 'Server';
                        $firstName = (count($parts) >= 1) ? array_shift($parts) : $fullName;
                        $middleName = implode(' ', $parts);

                        $payload = [
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
                        $payload = [
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

                    if (empty($payload['first_name'])) { $skipped++; continue; }

                    if ($this->serverRepo->create($payload)) {
                        $serverId = $db->lastInsertId();
                        $count++;

                        // Username Generation: Dynamic (Lastname + Firstname letters)
                        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $payload['last_name']));
                        $firstNameClean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $payload['first_name']));
                        $username = $baseUsername;
                        
                        $charCount = 0;
                        $numSuffix = 2;
                        
                        while ($userRepo->isUsernameTaken($username)) {
                            if ($charCount < strlen($firstNameClean)) {
                                // Try adding letters from first name one by one
                                $charCount++;
                                $username = $baseUsername . substr($firstNameClean, 0, $charCount);
                            } else {
                                // If all letters used, start adding numbers
                                $username = $baseUsername . $firstNameClean . $numSuffix;
                                $numSuffix++;
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
                $this->ok(['message' => "Imported $count servers and accounts successfully.", 'count' => $count, 'skipped' => $skipped]);
            }
            $this->error('Empty file.', 422);
        }
        $this->error('Invalid upload.', 422);
    }

    public function delete() {
        $this->verifyCsrfApi();
        $data = $this->getRequestData();
        $id = $data['id'] ?? null;
        $server = $this->serverRepo->getById($id);
        $name = $server ? $server->name : "ID: $id";

        if ($id && $this->serverRepo->delete($id)) {
            logAction('Delete', 'Servers', "Removed server: $name");
            $this->ok(['message' => 'Server removed successfully.']);
        }
        $this->error('Failed to remove server.', 500);
    }

    public function bulkDelete() {
        $this->verifyCsrfApi();
        $data = $this->getRequestData();
        $ids = $data['ids'] ?? [];

        if (empty($ids)) {
            $this->error('No servers selected.', 422);
        }

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
        $this->ok(['message' => "Deleted $count servers successfully."]);
    }
}
