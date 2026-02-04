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

        // If no direct match, check for dynamic routes (e.g., /path/:id)
        $params = [];
        if ($callback === false) {
            foreach ($this->routes[$method] as $routePath => $routeCallback) {
                // Replace :param with a regex group
                $pattern = preg_replace('/:[a-zA-Z0-9_]+/', '([a-zA-Z0-9_]+)', $routePath);
                $pattern = "@^" . $pattern . "$@";

                if (preg_match($pattern, $path, $matches)) {
                    array_shift($matches); // Remove the full match
                    $params = $matches;
                    $callback = $routeCallback;
                    break;
                }
            }
        }

        if ($callback === false) {
            http_response_code(404);
            echo "404 Not Found - Path: " . htmlspecialchars($path);
            return;
        }

        if (is_array($callback)) {
            $controller = new $callback[0]();
            $action = $callback[1];
            call_user_func_array([$controller, $action], $params);
        } else {
            call_user_func_array($callback, $params);
        }
    }
}