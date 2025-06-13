<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'billing_root');
define('DB_PASS', '');
define('DB_NAME', 'billing_portal');

try {
    // Create PDO instance
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Log error and display user-friendly message
    error_log("Database Connection Error: " . $e->getMessage());
    die("We're experiencing technical difficulties. Please try again later.");
}

// Helper function to get a setting value
function getSetting($key, $default = '', $user_id = null) {
    global $pdo;
    try {
        if ($user_id === null) {
            $user_id = $_SESSION['user_id'] ?? null;
        }
        
        if (!$user_id) {
            return $default;
        }
        
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ? AND user_id = ?");
        $stmt->execute([$key, $user_id]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    } catch (PDOException $e) {
        error_log("Error getting setting: " . $e->getMessage());
        return $default;
    }
}

// Helper function to update a setting value
function updateSetting($key, $value, $user_id = null) {
    global $pdo;
    try {
        if ($user_id === null) {
            $user_id = $_SESSION['user_id'] ?? null;
        }
        
        if (!$user_id) {
            return false;
        }
        
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, user_id, setting_value) 
                              VALUES (?, ?, ?) 
                              ON DUPLICATE KEY UPDATE setting_value = ?");
        return $stmt->execute([$key, $user_id, $value, $value]);
    } catch (PDOException $e) {
        error_log("Error updating setting: " . $e->getMessage());
        return false;
    }
}

// Helper function to format currency
function formatCurrency($amount) {
    $currency = getSetting('currency', 'EUR');
    $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    return $formatter->formatCurrency($amount, $currency);
}

// Helper function to format date
function formatDate($date) {
    return date('Y-m-d', strtotime($date));
}

// Helper function to calculate due date
function calculateDueDate($invoiceDate) {
    $paymentTerms = (int)getSetting('payment_terms', 30);
    return date('Y-m-d', strtotime($invoiceDate . " +{$paymentTerms} days"));
}

// Helper function to check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Helper function to require admin access
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /dashboard.php');
        exit;
    }
}

// Helper function to get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Helper function to get current user role
function getCurrentUserRole() {
    return $_SESSION['user_role'] ?? null;
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /auth/login.php');
        exit;
    }
} 