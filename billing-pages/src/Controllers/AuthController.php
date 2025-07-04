<?php

namespace BillingPages\Controllers;

/**
 * Authentication Controller
 * Handles user login, logout, and authentication
 */
class AuthController
{
    private $pdo;
    private $config;
    
    public function __construct($pdo, $config)
    {
        $this->pdo = $pdo;
        $this->config = $config;
    }
    
    /**
     * Display login form
     */
    public function login()
    {
        // If user is already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
        
        // Display login form
        require __DIR__ . '/../Views/login.php';
    }
    
    /**
     * Handle login form submission
     */
    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }
        
        $domain = $_POST['domain'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate input
        if (empty($domain) || empty($username) || empty($password)) {
            $_SESSION['error'] = 'Please fill in all required fields.';
            header('Location: /login');
            exit;
        }
        
        try {
            // Get domain information
            $stmt = $this->pdo->prepare("SELECT * FROM domains WHERE domain = ? AND status = 'active'");
            $stmt->execute([$domain]);
            $domainInfo = $stmt->fetch();
            
            if (!$domainInfo) {
                $_SESSION['error'] = 'Invalid domain or domain is inactive.';
                header('Location: /login');
                exit;
            }
            
            // Get user information
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE domain_id = ? AND username = ? AND status = 'active'");
            $stmt->execute([$domainInfo['id'], $username]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($password, $user['password_hash'])) {
                $_SESSION['error'] = 'Invalid username or password.';
                header('Location: /login');
                exit;
            }
            
            // Check if account is locked
            if ($user['locked_until'] && $user['locked_until'] > date('Y-m-d H:i:s')) {
                $_SESSION['error'] = 'Account is temporarily locked. Please try again later.';
                header('Location: /login');
                exit;
            }
            
            // Reset login attempts on successful login
            $stmt = $this->pdo->prepare("UPDATE users SET login_attempts = 0, last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['domain_id'] = $user['domain_id'];
            $_SESSION['domain'] = $domain;
            $_SESSION['domain_type'] = $domainInfo['type'];
            
            // Set domain-specific database connection
            $this->setDomainDatabase($domainInfo);
            
            $_SESSION['message'] = 'Welcome back, ' . $user['username'] . '!';
            header('Location: /dashboard');
            exit;
            
        } catch (\Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            $_SESSION['error'] = 'An error occurred during login. Please try again.';
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Handle logout
     */
    public function logout()
    {
        // Clear session
        session_destroy();
        
        // Redirect to login
        header('Location: /login');
        exit;
    }
    
    /**
     * Set domain-specific database connection
     */
    private function setDomainDatabase($domainInfo)
    {
        $dbConfig = require __DIR__ . '/../../config/database.php';
        $connectionKey = $domainInfo['type'];
        
        if (isset($dbConfig['connections'][$connectionKey])) {
            $config = $dbConfig['connections'][$connectionKey];
            
            $_SESSION['db_host'] = $config['host'];
            $_SESSION['db_name'] = $config['database'];
            $_SESSION['db_user'] = $config['username'];
            $_SESSION['db_pass'] = $config['password'];
        }
    }
    
    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated()
    {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check if user has specific role
     */
    public static function hasRole($role)
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
    
    /**
     * Require authentication
     */
    public static function requireAuth()
    {
        if (!self::isAuthenticated()) {
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Require specific role
     */
    public static function requireRole($role)
    {
        self::requireAuth();
        
        if (!self::hasRole($role)) {
            $_SESSION['error'] = 'You do not have permission to access this page.';
            header('Location: /dashboard');
            exit;
        }
    }
} 