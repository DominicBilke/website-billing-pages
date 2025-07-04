<?php
/**
 * Billing Pages - Main Entry Point
 * 
 * This is the main entry point for the billing pages application.
 * It handles routing, authentication, and initializes the application.
 */

// Start session
session_start();

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Load configuration
$config = require __DIR__ . '/../config/app.php';
$dbConfig = require __DIR__ . '/../config/database.php';

// Set timezone
date_default_timezone_set($config['timezone']);

// Initialize error handling
if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Initialize logging
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

$log = new Logger('billing_pages');
$log->pushHandler(new RotatingFileHandler(
    $config['logging']['path'] . 'app.log',
    $config['logging']['max_files'],
    $config['logging']['level']
));

// Initialize database connection
try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['connections']['main']['host']};dbname={$dbConfig['connections']['main']['database']};charset=utf8mb4",
        $dbConfig['connections']['main']['username'],
        $dbConfig['connections']['main']['password'],
        $dbConfig['connections']['main']['options']
    );
} catch (PDOException $e) {
    $log->error('Database connection failed: ' . $e->getMessage());
    die('Database connection failed. Please check your configuration.');
}

// Handle routing
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove query string from request
$request = parse_url($request, PHP_URL_PATH);

// Remove base path if exists
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/') {
    $request = substr($request, strlen($basePath));
}

// Default route
if ($request === '' || $request === '/') {
    $request = '/login';
}

// Route handling
switch ($request) {
    case '/login':
        require __DIR__ . '/../src/Controllers/AuthController.php';
        $controller = new BillingPages\Controllers\AuthController($pdo, $config);
        $controller->login();
        break;
        
    case '/logout':
        require __DIR__ . '/../src/Controllers/AuthController.php';
        $controller = new BillingPages\Controllers\AuthController($pdo, $config);
        $controller->logout();
        break;
        
    case '/dashboard':
        require __DIR__ . '/../src/Controllers/DashboardController.php';
        $controller = new BillingPages\Controllers\DashboardController($pdo, $config);
        $controller->index();
        break;
        
    case '/companies':
        require __DIR__ . '/../src/Controllers/CompaniesController.php';
        $controller = new BillingPages\Controllers\CompaniesController($pdo, $config);
        $controller->index();
        break;
        
    case '/tours':
        require __DIR__ . '/../src/Controllers/ToursController.php';
        $controller = new BillingPages\Controllers\ToursController($pdo, $config);
        $controller->index();
        break;
        
    case '/work':
        require __DIR__ . '/../src/Controllers/WorkController.php';
        $controller = new BillingPages\Controllers\WorkController($pdo, $config);
        $controller->index();
        break;
        
    case '/tasks':
        require __DIR__ . '/../src/Controllers/TasksController.php';
        $controller = new BillingPages\Controllers\TasksController($pdo, $config);
        $controller->index();
        break;
        
    case '/money':
        require __DIR__ . '/../src/Controllers/MoneyController.php';
        $controller = new BillingPages\Controllers\MoneyController($pdo, $config);
        $controller->index();
        break;
        
    case '/api/login':
        require __DIR__ . '/../src/Controllers/ApiController.php';
        $controller = new BillingPages\Controllers\ApiController($pdo, $config);
        $controller->login();
        break;
        
    default:
        // Check if it's a static asset
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/', $request)) {
            $filePath = __DIR__ . $request;
            if (file_exists($filePath)) {
                $mimeTypes = [
                    'css' => 'text/css',
                    'js' => 'application/javascript',
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'ico' => 'image/x-icon',
                    'svg' => 'image/svg+xml',
                    'woff' => 'font/woff',
                    'woff2' => 'font/woff2',
                    'ttf' => 'font/ttf',
                    'eot' => 'application/vnd.ms-fontobject'
                ];
                
                $extension = pathinfo($request, PATHINFO_EXTENSION);
                if (isset($mimeTypes[$extension])) {
                    header('Content-Type: ' . $mimeTypes[$extension]);
                    readfile($filePath);
                    exit;
                }
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        require __DIR__ . '/../src/Views/404.php';
        break;
} 