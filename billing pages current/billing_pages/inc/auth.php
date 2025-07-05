<?php
/**
 * Authentication System
 * Handles user authentication, authorization, and security
 */

class Auth {
    private $db;
    private $maxLoginAttempts = 5;
    private $lockoutTime = 900; // 15 minutes
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Register a new user
     */
    public function register($data) {
        $errors = [];
        
        // Validate required fields
        $required = ['username', 'email', 'password', 'confirm_password'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        // Validate email
        if (!empty($data['email']) && !isValidEmail($data['email'])) {
            $errors[] = 'Invalid email address';
        }
        
        // Check if email already exists
        if (!empty($data['email']) && $this->db->exists('users', 'email = ?', [$data['email']])) {
            $errors[] = 'Email address already registered';
        }
        
        // Check if username already exists
        if (!empty($data['username']) && $this->db->exists('users', 'username = ?', [$data['username']])) {
            $errors[] = 'Username already taken';
        }
        
        // Validate password strength
        if (!empty($data['password'])) {
            $passwordErrors = validatePasswordStrength($data['password']);
            $errors = array_merge($errors, $passwordErrors);
        }
        
        // Confirm password
        if (!empty($data['password']) && $data['password'] !== $data['confirm_password']) {
            $errors[] = 'Passwords do not match';
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Create user
        try {
            $userData = [
                'username' => sanitizeInput($data['username']),
                'email' => sanitizeInput($data['email']),
                'password' => hashPassword($data['password']),
                'role' => 'user',
                'status' => 'active',
                'email_verified' => 0,
                'verification_token' => generateRandomString(32),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $userId = $this->db->insert('users', $userData);
            
            // Send verification email
            $this->sendVerificationEmail($userData['email'], $userData['verification_token']);
            
            logActivity('user_registered', 'New user registered: ' . $userData['email'], $userId);
            
            return ['success' => true, 'user_id' => $userId];
            
        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['Registration failed. Please try again.']];
        }
    }
    
    /**
     * Login user
     */
    public function login($email, $password, $remember = false) {
        // Check for too many login attempts
        if ($this->isAccountLocked($email)) {
            return ['success' => false, 'errors' => ['Account temporarily locked. Please try again later.']];
        }
        
        // Validate input
        if (empty($email) || empty($password)) {
            return ['success' => false, 'errors' => ['Email and password are required']];
        }
        
        // Get user
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email = ? AND status = 'active'",
            [sanitizeInput($email)]
        );
        
        if (!$user) {
            $this->recordLoginAttempt($email, false);
            return ['success' => false, 'errors' => ['Invalid email or password']];
        }
        
        // Verify password
        if (!verifyPassword($password, $user['password'])) {
            $this->recordLoginAttempt($email, false);
            return ['success' => false, 'errors' => ['Invalid email or password']];
        }
        
        // Check if email is verified
        if (!$user['email_verified']) {
            return ['success' => false, 'errors' => ['Please verify your email address before logging in']];
        }
        
        // Record successful login
        $this->recordLoginAttempt($email, true);
        
        // Create session
        $this->createSession($user, $remember);
        
        // Update last login
        $this->db->update('users', 
            ['last_login' => date('Y-m-d H:i:s')], 
            'id = ?', 
            [$user['id']]
        );
        
        logActivity('user_login', 'User logged in successfully', $user['id']);
        
        return ['success' => true, 'user' => $user];
    }
    
    /**
     * Logout user
     */
    public function logout() {
        if (isLoggedIn()) {
            logActivity('user_logout', 'User logged out', $_SESSION['user_id']);
        }
        
        // Destroy session
        session_destroy();
        
        // Clear remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
            
            // Remove token from database
            $this->db->delete('remember_tokens', 'token = ?', [$_COOKIE['remember_token']]);
        }
    }
    
    /**
     * Create user session
     */
    private function createSession($user, $remember = false) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Set remember me token
        if ($remember) {
            $token = generateRandomString(64);
            $expires = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 days
            
            $this->db->insert('remember_tokens', [
                'user_id' => $user['id'],
                'token' => $token,
                'expires_at' => $expires,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
        }
    }
    
    /**
     * Check remember me token
     */
    public function checkRememberToken() {
        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            
            $rememberToken = $this->db->fetch(
                "SELECT rt.*, u.* FROM remember_tokens rt 
                 JOIN users u ON rt.user_id = u.id 
                 WHERE rt.token = ? AND rt.expires_at > NOW() AND u.status = 'active'",
                [$token]
            );
            
            if ($rememberToken) {
                $this->createSession($rememberToken, false);
                return true;
            } else {
                // Remove invalid token
                setcookie('remember_token', '', time() - 3600, '/');
                $this->db->delete('remember_tokens', 'token = ?', [$token]);
            }
        }
        
        return false;
    }
    
    /**
     * Send verification email
     */
    private function sendVerificationEmail($email, $token) {
        $verificationUrl = APP_URL . '/auth/verify.php?token=' . $token;
        
        $subject = 'Verify your email address - ' . APP_NAME;
        $message = "
        <html>
        <body>
            <h2>Welcome to " . APP_NAME . "!</h2>
            <p>Please click the link below to verify your email address:</p>
            <p><a href='{$verificationUrl}'>{$verificationUrl}</a></p>
            <p>If you didn't create an account, you can safely ignore this email.</p>
        </body>
        </html>
        ";
        
        return sendEmail($email, $subject, $message);
    }
    
    /**
     * Verify email address
     */
    public function verifyEmail($token) {
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE verification_token = ? AND email_verified = 0",
            [$token]
        );
        
        if (!$user) {
            return ['success' => false, 'errors' => ['Invalid or expired verification token']];
        }
        
        $this->db->update('users', 
            [
                'email_verified' => 1,
                'verification_token' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ], 
            'id = ?', 
            [$user['id']]
        );
        
        logActivity('email_verified', 'Email address verified', $user['id']);
        
        return ['success' => true, 'user' => $user];
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordReset($email) {
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email = ? AND status = 'active'",
            [sanitizeInput($email)]
        );
        
        if (!$user) {
            return ['success' => false, 'errors' => ['Email address not found']];
        }
        
        $token = generateRandomString(64);
        $expires = date('Y-m-d H:i:s', time() + (60 * 60)); // 1 hour
        
        $this->db->insert('password_resets', [
            'user_id' => $user['id'],
            'token' => $token,
            'expires_at' => $expires,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $resetUrl = APP_URL . '/auth/reset-password.php?token=' . $token;
        
        $subject = 'Reset your password - ' . APP_NAME;
        $message = "
        <html>
        <body>
            <h2>Password Reset Request</h2>
            <p>You requested to reset your password. Click the link below to proceed:</p>
            <p><a href='{$resetUrl}'>{$resetUrl}</a></p>
            <p>This link will expire in 1 hour.</p>
            <p>If you didn't request this, you can safely ignore this email.</p>
        </body>
        </html>
        ";
        
        if (sendEmail($email, $subject, $message)) {
            logActivity('password_reset_requested', 'Password reset requested', $user['id']);
            return ['success' => true];
        } else {
            return ['success' => false, 'errors' => ['Failed to send reset email']];
        }
    }
    
    /**
     * Reset password
     */
    public function resetPassword($token, $password) {
        $reset = $this->db->fetch(
            "SELECT pr.*, u.* FROM password_resets pr 
             JOIN users u ON pr.user_id = u.id 
             WHERE pr.token = ? AND pr.expires_at > NOW() AND u.status = 'active'",
            [$token]
        );
        
        if (!$reset) {
            return ['success' => false, 'errors' => ['Invalid or expired reset token']];
        }
        
        // Validate password strength
        $passwordErrors = validatePasswordStrength($password);
        if (!empty($passwordErrors)) {
            return ['success' => false, 'errors' => $passwordErrors];
        }
        
        // Update password
        $this->db->update('users', 
            [
                'password' => hashPassword($password),
                'updated_at' => date('Y-m-d H:i:s')
            ], 
            'id = ?', 
            [$reset['user_id']]
        );
        
        // Delete reset token
        $this->db->delete('password_resets', 'token = ?', [$token]);
        
        logActivity('password_reset', 'Password reset successfully', $reset['user_id']);
        
        return ['success' => true];
    }
    
    /**
     * Check if account is locked
     */
    private function isAccountLocked($email) {
        $attempts = $this->db->fetch(
            "SELECT COUNT(*) as count FROM login_attempts 
             WHERE email = ? AND success = 0 AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)",
            [$email, $this->lockoutTime]
        );
        
        return $attempts['count'] >= $this->maxLoginAttempts;
    }
    
    /**
     * Record login attempt
     */
    private function recordLoginAttempt($email, $success) {
        $this->db->insert('login_attempts', [
            'email' => sanitizeInput($email),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'success' => $success ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get current user
     */
    public function getCurrentUser() {
        if (!isLoggedIn()) {
            return null;
        }
        
        return $this->db->fetch(
            "SELECT * FROM users WHERE id = ? AND status = 'active'",
            [$_SESSION['user_id']]
        );
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($userId, $data) {
        $errors = [];
        
        // Validate email if changed
        if (!empty($data['email'])) {
            $existingUser = $this->db->fetch(
                "SELECT id FROM users WHERE email = ? AND id != ?",
                [$data['email'], $userId]
            );
            
            if ($existingUser) {
                $errors[] = 'Email address already in use';
            }
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $updateData = [
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if (!empty($data['first_name'])) $updateData['first_name'] = sanitizeInput($data['first_name']);
        if (!empty($data['last_name'])) $updateData['last_name'] = sanitizeInput($data['last_name']);
        if (!empty($data['email'])) $updateData['email'] = sanitizeInput($data['email']);
        if (!empty($data['phone'])) $updateData['phone'] = sanitizeInput($data['phone']);
        
        $this->db->update('users', $updateData, 'id = ?', [$userId]);
        
        logActivity('profile_updated', 'Profile updated', $userId);
        
        return ['success' => true];
    }
    
    /**
     * Change password
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->db->fetch("SELECT password FROM users WHERE id = ?", [$userId]);
        
        if (!verifyPassword($currentPassword, $user['password'])) {
            return ['success' => false, 'errors' => ['Current password is incorrect']];
        }
        
        $passwordErrors = validatePasswordStrength($newPassword);
        if (!empty($passwordErrors)) {
            return ['success' => false, 'errors' => $passwordErrors];
        }
        
        $this->db->update('users', 
            [
                'password' => hashPassword($newPassword),
                'updated_at' => date('Y-m-d H:i:s')
            ], 
            'id = ?', 
            [$userId]
        );
        
        logActivity('password_changed', 'Password changed', $userId);
        
        return ['success' => true];
    }
}

// Initialize authentication
$auth = new Auth();
?> 