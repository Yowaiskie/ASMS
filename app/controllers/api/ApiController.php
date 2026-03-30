<?php

namespace App\Controllers\Api;

use App\Core\Controller;

class ApiController extends Controller {
    protected ?array $requestData = null;

    protected function getRequestData(): array {
        if ($this->requestData !== null) {
            return $this->requestData;
        }

        $data = $_POST;
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (stripos($contentType, 'application/json') !== false) {
            $raw = trim(file_get_contents('php://input'));
            if ($raw !== '') {
                $json = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                    $data = $json;
                }
            }
        }

        $this->requestData = $data;
        return $data;
    }

    protected function json(array $payload, int $status = 200): void {
        if (ob_get_length()) {
            ob_clean();
        }
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    protected function ok($data = null, array $meta = []): void {
        $payload = ['success' => true, 'data' => $data];
        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }
        $this->json($payload);
    }

    protected function error(string $message, int $status = 400, array $errors = []): void {
        $payload = ['success' => false, 'message' => $message];
        if (!empty($errors)) {
            $payload['errors'] = $errors;
        }
        $this->json($payload, $status);
    }

    protected function requireLoginApi(): void {
        if (!isset($_SESSION['user_id'])) {
            $this->error('Unauthorized', 401);
        }
        $this->checkMaintenanceModeApi();
        $this->checkVerificationApi();
    }

    protected function requireRoleApi(string $role): void {
        $this->requireLoginApi();
        if (($_SESSION['role'] ?? '') !== $role) {
            $this->error('Forbidden', 403);
        }
    }

    protected function requireAnyRoleApi(array $roles): void {
        $this->requireLoginApi();
        if (!in_array($_SESSION['role'] ?? '', $roles, true)) {
            $this->error('Forbidden', 403);
        }
    }

    protected function verifyCsrfApi(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        $data = $this->getRequestData();
        $token = $data['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        if (!$token || !hash_equals($sessionToken, $token)) {
            $this->error('CSRF Validation Failed: Invalid Request', 419);
        }
    }

    protected function normalizePath(string $path): string {
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = str_replace('\\', '/', $scriptDir);
        if ($scriptDir !== '/' && substr($scriptDir, -1) === '/') {
            $scriptDir = rtrim($scriptDir, '/');
        }
        if ($scriptDir !== '' && $scriptDir !== '/' && strpos($path, $scriptDir) === 0) {
            $path = substr($path, strlen($scriptDir));
        }
        if ($path === '') {
            $path = '/';
        }
        return $path;
    }

    protected function checkMaintenanceModeApi(): void {
        $mode = \App\Models\SystemSetting::get('maintenance_mode', 'off');
        $role = $_SESSION['role'] ?? 'User';
        if ($mode !== 'on' || $role === 'Superadmin') {
            return;
        }

        $path = $this->normalizePath(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
        $allowed = [
            '/maintenance',
            '/login',
            '/logout',
            '/auth/login',
            '/auth/logout',
            '/api/auth/login',
            '/api/auth/logout'
        ];

        if (!in_array($path, $allowed, true)) {
            $this->error('Maintenance mode enabled', 503);
        }
    }

    protected function checkVerificationApi(): void {
        $role = $_SESSION['role'] ?? '';
        if (!in_array($role, ['User', 'Admin'], true)) {
            return;
        }
        $isVerified = $_SESSION['is_verified'] ?? 0;
        if ($isVerified) {
            return;
        }

        $path = $this->normalizePath(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
        if (strpos($path, '/api/settings') === 0 || in_array($path, ['/settings', '/settings/store', '/logout', '/maintenance', '/api/auth/logout'], true)) {
            return;
        }

        $this->error('Verification required to access this resource.', 403);
    }
}
