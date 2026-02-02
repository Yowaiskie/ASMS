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
            $id = $_POST['id'] ?? '';
            $data = [
                'name' => trim($_POST['name']),
                'nickname' => trim($_POST['nickname'] ?? ''),
                'dob' => $_POST['dob'] ?? null,
                'age' => $_POST['age'] ?? null,
                'address' => trim($_POST['address'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'rank' => trim($_POST['rank']),
                'team' => trim($_POST['team']),
                'status' => trim($_POST['status']),
                'month_joined' => trim($_POST['month_joined'] ?? ''),
                'investiture_date' => $_POST['investiture_date'] ?? null,
                'order_name' => trim($_POST['order_name'] ?? ''),
                'position' => trim($_POST['position'] ?? '')
            ];

            if (!empty($id)) {
                if ($this->serverRepo->update($id, $data)) {
                    setFlash('msg_success', 'Server profile updated.');
                } else {
                    setFlash('msg_error', 'Update failed.');
                }
            } else {
                if ($this->serverRepo->create($data)) {
                    setFlash('msg_success', 'Server registered successfully!');
                } else {
                    setFlash('msg_error', 'Registration failed.');
                }
            }
            redirect('servers');
        }
    }

    public function download_pdf() {
        // Load DomPDF via Composer Autoloader
        $autoloadPath = __DIR__ . '/../../vendor/autoload.php';
        if (file_exists($autoloadPath)) {
            require_once $autoloadPath;
        } else {
            die("Please run 'composer require dompdf/dompdf'.");
        }

        $servers = $this->serverRepo->getAll();

        // Prepare Logo 1
        $logoPath = __DIR__ . '/../../public/images/logo.png';
        $logoData = '';
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $dataImg = file_get_contents($logoPath);
            $logoData = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
        }

        // Prepare Logo 2 (Parish Logo)
        $parishLogoPath = __DIR__ . '/../../public/images/parish-logo.png';
        $parishLogoData = '';
        if (file_exists($parishLogoPath)) {
            $type = pathinfo($parishLogoPath, PATHINFO_EXTENSION);
            $dataImg = file_get_contents($parishLogoPath);
            $parishLogoData = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
        }

        // HTML for Master List (Landscape)
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
                            <div style="display: inline-block;">
                                ' . ($logoData ? '<img src="' . $logoData . '" style="width: 55px; margin-right: 10px; vertical-align: middle;">' : '') . '
                                ' . ($parishLogoData ? '<img src="' . $parishLogoData . '" style="width: 55px; vertical-align: middle;">' : '') . '
                            </div>
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

        $html .= '
                </tbody>
            </table>
            
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
        </body>
        </html>';

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
                
                $firstRow = true;
                while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                    if ($firstRow) { $firstRow = false; continue; } // Skip Header

                    if (count($column) < 1) continue;
                    
                    $data = [
                        'name' => trim($column[0] ?? ''),
                        'nickname' => trim($column[1] ?? ''),
                        'address' => trim($column[2] ?? ''),
                        'dob' => !empty($column[3]) ? date('Y-m-d', strtotime($column[3])) : null,
                        'phone' => trim($column[4] ?? ''),
                        'month_joined' => trim($column[5] ?? ''),
                        'investiture_date' => !empty($column[6]) ? date('Y-m-d', strtotime($column[6])) : null,
                        'order_name' => trim($column[7] ?? ''),
                        'position' => trim($column[8] ?? ''),
                        'rank' => trim($column[9] ?? 'Server'),
                        'team' => trim($column[10] ?? 'Unassigned'),
                        'status' => trim($column[11] ?? 'Active'),
                        'email' => trim($column[12] ?? '')
                    ];

                    if (empty($data['name'])) continue;

                    if ($this->serverRepo->create($data)) {
                        $count++;
                    }
                }
                
                fclose($file);
                logAction('Create', 'Servers', "Imported $count servers via CSV.");
                setFlash('msg_success', "Imported $count servers successfully.");
            } else {
                setFlash('msg_error', 'Empty file uploaded.');
            }
        } else {
            setFlash('msg_error', 'Invalid file upload.');
        }
        redirect('servers');
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