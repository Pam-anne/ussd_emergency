<?php


// Handle static files for built-in server
if (php_sapi_name() === 'cli-server') {
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false; // Let built-in server handle static files
    }
}

// Get the current URL path
$request = $_SERVER['REQUEST_URI'];

// Remove query string
$path = parse_url($request, PHP_URL_PATH);

// Remove leading slash
$path = ltrim($path, '/');

// Load routes
$routes = require 'routes.php';

// Check if route exists
if (array_key_exists($path, $routes)) {
    $file = $routes[$path];
    
    // Check if file exists
    if (file_exists($file)) {
        // Include database connection
        if (file_exists('db_connect.php')) {
            require_once 'db_connect.php';
        }
        
        // Include config
        if (file_exists('config.php')) {
            require_once 'config.php';
        }
        
        // Load the controller
        require $file;
    } else {
        // File not found
        http_response_code(404);
        require 'views/partials/404.php';
    }
} else {
    // Route not found
    http_response_code(404);
    if (file_exists('views/partials/404.php')) {
        require 'views/partials/404.php';
    } else {
        echo "<h1>404 - Page Not Found</h1>";
    }
}
