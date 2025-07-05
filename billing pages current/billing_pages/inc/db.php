<?php
/**
 * Database Connection and Management
 * Handles database connections and provides database utilities
 */

class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    private function connect() {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_PORT,
                DB_DATABASE,
                DB_CHARSET
            );
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
            
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            if (APP_DEBUG) {
                throw new Exception('Database connection failed: ' . $e->getMessage());
            } else {
                throw new Exception('Database connection failed. Please check your configuration.');
            }
        }
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Database query failed: ' . $e->getMessage());
            if (APP_DEBUG) {
                throw new Exception('Database query failed: ' . $e->getMessage());
            } else {
                throw new Exception('Database operation failed.');
            }
        }
    }
    
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function insert($table, $data) {
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $fieldList = implode(', ', $fields);
        
        $sql = "INSERT INTO {$table} ({$fieldList}) VALUES ({$placeholders})";
        
        $this->query($sql, $data);
        return $this->pdo->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $fields = array_keys($data);
        $setClause = implode(' = ?, ', $fields) . ' = ?';
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        
        $params = array_values($data);
        $params = array_merge($params, $whereParams);
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function count($table, $where = '1', $params = []) {
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$where}";
        $result = $this->fetch($sql, $params);
        return (int) $result['count'];
    }
    
    public function exists($table, $where, $params = []) {
        return $this->count($table, $where, $params) > 0;
    }
    
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    public function commit() {
        return $this->pdo->commit();
    }
    
    public function rollback() {
        return $this->pdo->rollback();
    }
    
    public function inTransaction() {
        return $this->pdo->inTransaction();
    }
}

// Initialize database connection
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    if (APP_DEBUG) {
        die('Database Error: ' . $e->getMessage());
    } else {
        die('Database connection failed. Please check your configuration.');
    }
}

// Legacy compatibility - keep the old $pdo variable for existing code
if (!isset($pdo)) {
    $pdo = $db->getConnection();
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