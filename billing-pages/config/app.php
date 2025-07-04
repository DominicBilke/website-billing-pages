<?php

return [
    'name' => 'Billing Pages',
    'version' => '1.0.0',
    'environment' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => $_ENV['APP_DEBUG'] ?? false,
    
    'url' => $_ENV['APP_URL'] ?? 'https://billing-pages.com',
    'timezone' => 'Europe/Berlin',
    'locale' => 'de',
    
    'upload' => [
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_types' => ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'csv', 'gpx'],
        'path' => __DIR__ . '/../public/uploads/',
        'temp_path' => __DIR__ . '/../temp/',
    ],
    
    'session' => [
        'name' => 'billing_pages_session',
        'lifetime' => 7200, // 2 hours
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax',
    ],
    
    'security' => [
        'password_min_length' => 8,
        'password_require_special' => true,
        'max_login_attempts' => 5,
        'lockout_duration' => 900, // 15 minutes
        'csrf_token_lifetime' => 3600, // 1 hour
    ],
    
    'mail' => [
        'host' => $_ENV['MAIL_HOST'] ?? 'localhost',
        'port' => $_ENV['MAIL_PORT'] ?? 587,
        'username' => $_ENV['MAIL_USERNAME'] ?? '',
        'password' => $_ENV['MAIL_PASSWORD'] ?? '',
        'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
        'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@billing-pages.com',
        'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Billing Pages',
    ],
    
    'logging' => [
        'path' => __DIR__ . '/../logs/',
        'level' => $_ENV['LOG_LEVEL'] ?? 'info',
        'max_files' => 30,
    ],
    
    'pdf' => [
        'template_path' => __DIR__ . '/../src/Views/pdf/',
        'output_path' => __DIR__ . '/../temp/pdf/',
        'font_path' => __DIR__ . '/../vendor/tecnickcom/tcpdf/fonts/',
    ],
    
    'maps' => [
        'api_key' => $_ENV['MAPS_API_KEY'] ?? '',
        'default_zoom' => 13,
        'default_center' => [52.5200, 13.4050], // Berlin
    ],
    
    'modules' => [
        'companies' => [
            'name' => 'Company Billing',
            'description' => 'Employee time tracking and company billing',
            'icon' => 'fas fa-building',
            'color' => '#007bff',
        ],
        'tours' => [
            'name' => 'Tour Billing',
            'description' => 'Route tracking and tour billing with GPS',
            'icon' => 'fas fa-route',
            'color' => '#28a745',
        ],
        'work' => [
            'name' => 'Work Billing',
            'description' => 'Work time tracking and project billing',
            'icon' => 'fas fa-briefcase',
            'color' => '#ffc107',
        ],
        'tasks' => [
            'name' => 'Task Billing',
            'description' => 'Task-based billing and project management',
            'icon' => 'fas fa-tasks',
            'color' => '#17a2b8',
        ],
        'money' => [
            'name' => 'Money Billing',
            'description' => 'Financial transactions and money billing',
            'icon' => 'fas fa-euro-sign',
            'color' => '#dc3545',
        ],
    ],
]; 