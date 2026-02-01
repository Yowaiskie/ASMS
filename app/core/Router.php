<?php

namespace App\Core;

class Router {
    protected $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function resolve() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Get the directory of the entry script (index.php)
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        
        // Normalize slashes to forward slashes for Windows compatibility
        $scriptDir = str_replace('\\', '/', $scriptDir);
        
        // Ensure scriptDir doesn't end with a slash unless it's just '/'
        if ($scriptDir !== '/' && substr($scriptDir, -1) === '/') {
            $scriptDir = rtrim($scriptDir, '/');
        }

        // Remove scriptDir from the start of path if it matches
        if (strpos($path, $scriptDir) === 0) {
            $path = substr($path, strlen($scriptDir));
        }
        
        // Default to / if empty
        if ($path === '' || $path === '/') {
            $path = '/';
        }

        $method = $_SERVER['REQUEST_METHOD'];
        $callback = $this->routes[$method][$path] ?? false;

        // Debugging (Uncomment if still having issues)
        // echo "Path: " . $path . "<br>";
        // echo "ScriptDir: " . $scriptDir . "<br>";
        // echo "Method: " . $method . "<br>";

        if ($callback === false) {
            http_response_code(404);
            echo "404 Not Found - Path: " . htmlspecialchars($path);
            return;
        }

        if (is_array($callback)) {
            $controller = new $callback[0]();
            $action = $callback[1];
            call_user_func([$controller, $action]);
        } else {
            call_user_func($callback);
        }
    }
}