<?php

declare(strict_types=1);

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Start session with secure settings
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
ini_set('session.use_strict_mode', '1');
session_start();

// Set error reporting based on environment
if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Get request URI and method
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Simple router
$routes = [
    // Client Portal Routes
    'GET' => [
        '/^\/portal\/([a-f0-9]{64})$/' => ['App\Controllers\ClientController', 'portal'],
        '/^\/portal\/([a-f0-9]{64})\/campaign\/(\d+)$/' => ['App\Controllers\ClientController', 'campaign'],
        '/^\/portal\/([a-f0-9]{64})\/history$/' => ['App\Controllers\ClientController', 'history'],
        
        // Admin Routes
        '/^\/admin$/' => ['App\Controllers\AdminController', 'dashboard'],
        '/^\/admin\/login$/' => ['App\Controllers\AdminController', 'loginForm'],
        '/^\/admin\/logout$/' => ['App\Controllers\AdminController', 'logout'],
        '/^\/admin\/clients$/' => ['App\Controllers\AdminController', 'clients'],
        '/^\/admin\/clients\/(\d+)$/' => ['App\Controllers\AdminController', 'clientDetail'],
        '/^\/admin\/clients\/(\d+)\/campaigns$/' => ['App\Controllers\AdminController', 'campaigns'],
        '/^\/admin\/campaigns\/(\d+)$/' => ['App\Controllers\AdminController', 'campaignDetail'],
        '/^\/admin\/campaigns\/(\d+)\/subjects$/' => ['App\Controllers\AdminController', 'subjects'],
        '/^\/admin\/campaigns\/(\d+)\/emails$/' => ['App\Controllers\AdminController', 'emails'],
        
        // Home redirect
        '/^\/$/' => ['App\Controllers\AdminController', 'home'],
    ],
    'POST' => [
        // Client Portal Actions
        '/^\/api\/approve$/' => ['App\Controllers\ApiController', 'approve'],
        '/^\/api\/deny$/' => ['App\Controllers\ApiController', 'deny'],
        
        // Admin Actions
        '/^\/admin\/login$/' => ['App\Controllers\AdminController', 'login'],
        '/^\/admin\/clients\/create$/' => ['App\Controllers\AdminController', 'createClient'],
        '/^\/admin\/clients\/(\d+)\/update$/' => ['App\Controllers\AdminController', 'updateClient'],
        '/^\/admin\/clients\/(\d+)\/regenerate-token$/' => ['App\Controllers\AdminController', 'regenerateToken'],
        '/^\/admin\/clients\/(\d+)\/send-link$/' => ['App\Controllers\AdminController', 'sendLink'],
        '/^\/admin\/clients\/(\d+)\/delete$/' => ['App\Controllers\AdminController', 'deleteClient'],
        '/^\/admin\/campaigns\/create$/' => ['App\Controllers\AdminController', 'createCampaign'],
        '/^\/admin\/campaigns\/(\d+)\/update$/' => ['App\Controllers\AdminController', 'updateCampaign'],
        '/^\/admin\/subjects\/create$/' => ['App\Controllers\AdminController', 'createSubject'],
        '/^\/admin\/subjects\/(\d+)\/update$/' => ['App\Controllers\AdminController', 'updateSubject'],
        '/^\/admin\/subjects\/(\d+)\/delete$/' => ['App\Controllers\AdminController', 'deleteSubject'],
        '/^\/admin\/emails\/create$/' => ['App\Controllers\AdminController', 'createEmail'],
        '/^\/admin\/emails\/(\d+)\/update$/' => ['App\Controllers\AdminController', 'updateEmail'],
        '/^\/admin\/emails\/(\d+)\/delete$/' => ['App\Controllers\AdminController', 'deleteEmail'],
        '/^\/admin\/emails\/(\d+)\/resubmit$/' => ['App\Controllers\AdminController', 'resubmitEmail'],
        '/^\/admin\/subjects\/(\d+)\/resubmit$/' => ['App\Controllers\AdminController', 'resubmitSubject'],
    ],
];

// Match route
$matched = false;
if (isset($routes[$requestMethod])) {
    foreach ($routes[$requestMethod] as $pattern => $handler) {
        if (preg_match($pattern, $requestUri, $matches)) {
            array_shift($matches); // Remove full match
            [$controllerClass, $method] = $handler;
            
            $controller = new $controllerClass();
            call_user_func_array([$controller, $method], $matches);
            $matched = true;
            break;
        }
    }
}

if (!$matched) {
    http_response_code(404);
    include __DIR__ . '/../src/views/404.php';
}

