<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'billing_pages');

// Application settings
define('SITE_NAME', 'Billing Pages');
define('SITE_URL', 'http://localhost/billing-pages');
define('ADMIN_EMAIL', 'admin@example.com');

// File paths
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('TEMP_DIR', __DIR__ . '/temp');

// Session settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_NAME', 'billing_pages_session');

// Security settings
define('HASH_COST', 12); // For password hashing
define('TOKEN_LIFETIME', 1800); // 30 minutes

// Pagination settings
define('RECORDS_PER_PAGE', 10);

// Date and time settings
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');

// Currency settings
define('CURRENCY_SYMBOL', '$');
define('CURRENCY_DECIMALS', 2);
define('CURRENCY_DECIMAL_SEPARATOR', '.');
define('CURRENCY_THOUSANDS_SEPARATOR', ',');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Create required directories if they don't exist
$directories = [
    UPLOAD_DIR,
    TEMP_DIR,
    __DIR__ . '/logs'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
} 