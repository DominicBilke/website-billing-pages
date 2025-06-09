<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'billing_system');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Application configuration
define('APP_NAME', 'Billing System');
define('APP_URL', 'http://localhost/billing-pages');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // development, production

// Session configuration
define('SESSION_NAME', 'billing_session');
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', '');
define('SESSION_SECURE', false);
define('SESSION_HTTPONLY', true);

// Security configuration
define('PASSWORD_HASH_COST', 12);
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_LENGTH', 32);
define('CSRF_TOKEN_LIFETIME', 3600); // 1 hour

// File upload configuration
define('UPLOAD_MAX_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_FILE_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
]);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// Email configuration
define('MAIL_HOST', 'smtp.example.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your_email@example.com');
define('MAIL_PASSWORD', 'your_email_password');
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM_ADDRESS', 'noreply@example.com');
define('MAIL_FROM_NAME', 'Billing System');

// Logging configuration
define('LOG_PATH', __DIR__ . '/../logs/');
define('LOG_LEVEL', 'debug'); // debug, info, warning, error, critical

// Cache configuration
define('CACHE_PATH', __DIR__ . '/../cache/');
define('CACHE_LIFETIME', 3600); // 1 hour

// API configuration
define('API_KEY', 'your_api_key');
define('API_SECRET', 'your_api_secret');

// Error reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Time zone
date_default_timezone_set('UTC');

// Character encoding
mb_internal_encoding('UTF-8');

// Load environment variables from .env file if it exists
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Override configuration with environment variables if they exist
$env_vars = [
    'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS',
    'APP_URL', 'APP_ENV',
    'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD',
    'API_KEY', 'API_SECRET'
];

foreach ($env_vars as $var) {
    $env_value = getenv($var);
    if ($env_value !== false) {
        define($var, $env_value);
    }
} 