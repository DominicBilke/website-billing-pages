<?php
require_once 'config.php';
require_once 'database.php';
require_once 'functions.php';

class Auth {
    private $db;
    private static $instance = null;
    
    private function __construct() {
        $this->db = Database::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function login($username, $password) {
        try {
            $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
            $result = $this->db->query($sql, [$username], 's');
            
            if (!$result || $result->num_rows === 0) {
                return false;
            }
            
            $user = $result->fetch_assoc();
            
            if (!password_verify($password, $user['password'])) {
                $this->logFailedAttempt($username);
                return false;
            }
            
            $this->startSession($user);
            $this->logActivity($user['id'], 'login', 'User logged in successfully');
            
            return true;
        } catch (Exception $e) {
            log_error($e->getMessage());
            return false;
        }
    }
    
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->logActivity($_SESSION['user_id'], 'logout', 'User logged out');
        }
        
        session_destroy();
        return true;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
    
    public function requireRole($role) {
        $this->requireLogin();
        
        if ($_SESSION['user_role'] !== $role) {
            header('Location: unauthorized.php');
            exit;
        }
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $sql = "SELECT id, username, email, role FROM users WHERE id = ?";
        $result = $this->db->query($sql, [$_SESSION['user_id']], 'i');
        
        if (!$result || $result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            $sql = "SELECT password FROM users WHERE id = ?";
            $result = $this->db->query($sql, [$userId], 'i');
            
            if (!$result || $result->num_rows === 0) {
                return false;
            }
            
            $user = $result->fetch_assoc();
            
            if (!password_verify($currentPassword, $user['password'])) {
                return false;
            }
            
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => PASSWORD_HASH_COST]);
            
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $result = $this->db->query($sql, [$hashedPassword, $userId], 'si');
            
            if ($result) {
                $this->logActivity($userId, 'password_change', 'User changed password');
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            log_error($e->getMessage());
            return false;
        }
    }
    
    public function resetPassword($email) {
        try {
            $sql = "SELECT id FROM users WHERE email = ?";
            $result = $this->db->query($sql, [$email], 's');
            
            if (!$result || $result->num_rows === 0) {
                return false;
            }
            
            $user = $result->fetch_assoc();
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $sql = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)";
            $result = $this->db->query($sql, [$user['id'], $token, $expires], 'iss');
            
            if ($result) {
                $this->sendPasswordResetEmail($email, $token);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            log_error($e->getMessage());
            return false;
        }
    }
    
    private function startSession($user) {
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['last_activity'] = time();
        
        $this->regenerateSession();
    }
    
    private function regenerateSession() {
        if (isset($_SESSION['last_regeneration'])) {
            $timeout = SESSION_LIFETIME;
            if (time() - $_SESSION['last_regeneration'] > $timeout) {
                session_regenerate_id(true);
                $_SESSION['last_regeneration'] = time();
            }
        } else {
            $_SESSION['last_regeneration'] = time();
        }
    }
    
    private function logFailedAttempt($username) {
        $sql = "INSERT INTO login_attempts (username, ip_address, created_at) VALUES (?, ?, NOW())";
        $this->db->query($sql, [$username, $_SERVER['REMOTE_ADDR']], 'ss');
    }
    
    private function isAccountLocked($username) {
        $sql = "SELECT COUNT(*) as attempts FROM login_attempts 
                WHERE username = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)";
        $result = $this->db->query($sql, [$username, LOGIN_TIMEOUT], 'si');
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['attempts'] >= LOGIN_MAX_ATTEMPTS;
        }
        
        return false;
    }
    
    private function sendPasswordResetEmail($email, $token) {
        $resetLink = APP_URL . "/reset_password.php?token=" . $token;
        $subject = "Password Reset Request";
        $message = "Please click the following link to reset your password: " . $resetLink;
        
        $headers = "From: " . SMTP_FROM_EMAIL . "\r\n";
        $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        return mail($email, $subject, $message, $headers);
    }
    
    private function logActivity($userId, $action, $details = '') {
        $sql = "INSERT INTO activity_log (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())";
        return $this->db->query($sql, [$userId, $action, $details], 'iss');
    }
}

// Initialize authentication
$auth = Auth::getInstance(); 