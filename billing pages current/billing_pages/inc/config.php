<?php
/**
 * Billing Pages Configuration
 * Modern configuration system with environment variables
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load environment variables
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, '"\'');
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
    return true;
}

// Load .env file
$envFile = __DIR__ . '/../.env';
if (!loadEnv($envFile)) {
    // Fallback to env.example if .env doesn't exist
    loadEnv(__DIR__ . '/../env.example');
}

// Helper function to get environment variable
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

// Application configuration
define('APP_NAME', env('APP_NAME', 'Billing Pages'));
define('APP_ENV', env('APP_ENV', 'production'));
define('APP_DEBUG', env('APP_DEBUG', 'false') === 'true');
define('APP_URL', env('APP_URL', 'https://billing-pages.com'));
define('APP_TIMEZONE', env('APP_TIMEZONE', 'UTC'));
define('APP_ROOT', dirname(__DIR__));

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Error reporting based on environment
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Logging configuration
ini_set('log_errors', 1);
$logDir = APP_ROOT . '/logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0755, true);
}
ini_set('error_log', $logDir . '/error.log');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', env('SESSION_SECURE_COOKIE', 'true') === 'true');
ini_set('session.cookie_samesite', env('SESSION_SAME_SITE', 'strict'));

// Security headers
if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' \'unsafe-eval\' https://cdn.jsdelivr.net https://js.stripe.com; style-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net; img-src \'self\' data: https:; font-src \'self\' https://cdn.jsdelivr.net; connect-src \'self\' https://api.stripe.com;');
}

// Database configuration
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_DATABASE', env('DB_DATABASE', 'billing_portal'));
define('DB_USERNAME', env('DB_USERNAME', 'root'));
define('DB_PASSWORD', env('DB_PASSWORD', ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

// Mail configuration
define('MAIL_MAILER', env('MAIL_MAILER', 'smtp'));
define('MAIL_HOST', env('MAIL_HOST', 'smtp.gmail.com'));
define('MAIL_PORT', env('MAIL_PORT', '587'));
define('MAIL_USERNAME', env('MAIL_USERNAME', ''));
define('MAIL_PASSWORD', env('MAIL_PASSWORD', ''));
define('MAIL_ENCRYPTION', env('MAIL_ENCRYPTION', 'tls'));
define('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', 'noreply@billing-pages.com'));
define('MAIL_FROM_NAME', env('MAIL_FROM_NAME', APP_NAME));

// Payment configuration
define('STRIPE_PUBLISHABLE_KEY', env('STRIPE_PUBLISHABLE_KEY', ''));
define('STRIPE_SECRET_KEY', env('STRIPE_SECRET_KEY', ''));
define('STRIPE_WEBHOOK_SECRET', env('STRIPE_WEBHOOK_SECRET', ''));

// File upload configuration
define('UPLOAD_MAX_SIZE', (int) env('UPLOAD_MAX_SIZE', '5242880')); // 5MB
define('ALLOWED_FILE_TYPES', explode(',', env('ALLOWED_FILE_TYPES', 'jpg,jpeg,png,pdf,doc,docx')));

// Include required files
require_once APP_ROOT . '/inc/db.php';
require_once APP_ROOT . '/inc/helpers.php';

// Initialize application
initializeApp();

/**
 * Initialize the application
 */
function initializeApp() {
    // Set up error handling
    if (APP_DEBUG) {
        set_error_handler('errorHandler');
        set_exception_handler('exceptionHandler');
    }
    
    // Load Composer autoloader
    $autoloader = APP_ROOT . '/vendor/autoload.php';
    if (file_exists($autoloader)) {
        require_once $autoloader;
    }
}

/**
 * Custom error handler
 */
function errorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $error = [
        'type' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline,
        'time' => date('Y-m-d H:i:s')
    ];
    
    error_log(json_encode($error) . PHP_EOL, 3, APP_ROOT . '/logs/error.log');
    
    if (APP_DEBUG) {
        echo "<h1>Error</h1>";
        echo "<p><strong>Type:</strong> " . $error['type'] . "</p>";
        echo "<p><strong>Message:</strong> " . $error['message'] . "</p>";
        echo "<p><strong>File:</strong> " . $error['file'] . "</p>";
        echo "<p><strong>Line:</strong> " . $error['line'] . "</p>";
    }
    
    return true;
}

/**
 * Custom exception handler
 */
function exceptionHandler($exception) {
    $error = [
        'type' => get_class($exception),
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString(),
        'time' => date('Y-m-d H:i:s')
    ];
    
    error_log(json_encode($error) . PHP_EOL, 3, APP_ROOT . '/logs/error.log');
    
    if (APP_DEBUG) {
        echo "<h1>Exception</h1>";
        echo "<p><strong>Type:</strong> " . $error['type'] . "</p>";
        echo "<p><strong>Message:</strong> " . $error['message'] . "</p>";
        echo "<p><strong>File:</strong> " . $error['file'] . "</p>";
        echo "<p><strong>Line:</strong> " . $error['line'] . "</p>";
        echo "<h2>Stack Trace</h2>";
        echo "<pre>" . $error['trace'] . "</pre>";
    } else {
        http_response_code(500);
        echo "<h1>Internal Server Error</h1>";
        echo "<p>Something went wrong. Please try again later.</p>";
    }
}

// Helper function to get base URL
function getBaseUrl() {
    return APP_URL;
}

// Helper function to get asset URL
function getAssetUrl($path) {
    return getBaseUrl() . '/public/' . ltrim($path, '/');
}

// Helper function to get current page URL
function getCurrentUrl() {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
           "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

// Helper function to redirect
function redirect($path) {
    header('Location: ' . getBaseUrl() . '/' . ltrim($path, '/'));
    exit;
}

// Helper function to set flash message
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Helper function to get flash message
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Helper function to sanitize output
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Helper function to generate CSRF token
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Helper function to validate CSRF token
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Helper function to check if request is AJAX
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Helper function to send JSON response
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Helper function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Helper function to validate date
function isValidDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// Helper function to format phone number
function formatPhoneNumber($phone) {
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    return $phone;
}

// Helper function to generate random string
function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length));
}

// Helper function to get file extension
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// Helper function to check if file is allowed
function isAllowedFile($filename, $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf']) {
    return in_array(getFileExtension($filename), $allowedExtensions);
}

// Helper function to get file size in human readable format
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Helper function to get mime type
function getMimeType($filename) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filename);
    finfo_close($finfo);
    return $mimeType;
}

// Helper function to check if mime type is allowed
function isAllowedMimeType($filename, $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf']) {
    return in_array(getMimeType($filename), $allowedMimeTypes);
}

// Helper function to upload file
function uploadFile($file, $destination, $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf']) {
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new RuntimeException('Invalid parameters.');
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }

    if ($file['size'] > 5242880) { // 5MB
        throw new RuntimeException('Exceeded filesize limit.');
    }

    if (!isAllowedFile($file['name'], $allowedExtensions)) {
        throw new RuntimeException('Invalid file format.');
    }

    if (!isAllowedMimeType($file['tmp_name'])) {
        throw new RuntimeException('Invalid file format.');
    }

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

    return true;
} 