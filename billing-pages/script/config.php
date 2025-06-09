<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'billing');

// Application settings
define('APP_NAME', 'Billing System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/billing-pages');
define('APP_ROOT', dirname(__DIR__));

// Session settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_NAME', 'billing_session');
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', '');
define('SESSION_SECURE', true);
define('SESSION_HTTPONLY', true);

// Security settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_LENGTH', 32);
define('PASSWORD_HASH_COST', 12);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes

// File upload settings
define('UPLOAD_MAX_SIZE', 5242880); // 5MB
define('UPLOAD_ALLOWED_TYPES', [
    'image/jpeg',
    'image/png',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]);
define('UPLOAD_PATH', APP_ROOT . '/uploads');

// Date and time settings
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', DATE_FORMAT . ' ' . TIME_FORMAT);
define('TIMEZONE', 'Europe/Berlin');

// Currency settings
define('CURRENCY_CODE', 'EUR');
define('CURRENCY_SYMBOL', '€');
define('CURRENCY_DECIMALS', 2);
define('CURRENCY_DECIMAL_SEPARATOR', ',');
define('CURRENCY_THOUSANDS_SEPARATOR', '.');

// Pagination settings
define('ITEMS_PER_PAGE', 10);
define('PAGINATION_LINKS', 5);

// Logging settings
define('LOG_PATH', APP_ROOT . '/logs');
define('LOG_LEVEL', 'ERROR'); // DEBUG, INFO, WARNING, ERROR, CRITICAL
define('LOG_FORMAT', '[%datetime%] %level%: %message% %context%');

// Email settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_FROM_EMAIL', '');
define('SMTP_FROM_NAME', APP_NAME);

// API settings
define('API_KEY', '');
define('API_SECRET', '');
define('API_VERSION', 'v1');
define('API_RATE_LIMIT', 100); // requests per minute

// Cache settings
define('CACHE_ENABLED', true);
define('CACHE_PATH', APP_ROOT . '/cache');
define('CACHE_LIFETIME', 3600); // 1 hour

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', LOG_PATH . '/php_errors.log');

// Set timezone
date_default_timezone_set(TIMEZONE);

// Create required directories if they don't exist
$directories = [
    UPLOAD_PATH,
    LOG_PATH,
    CACHE_PATH
];

foreach ($directories as $directory) {
    if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
    }
} 