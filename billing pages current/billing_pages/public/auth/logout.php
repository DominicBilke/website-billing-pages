<?php
require_once __DIR__ . '/../../inc/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get user data before logout for logging
$user = null;
if (isset($_SESSION['user_id'])) {
    $db = Database::getInstance();
    $user = $db->fetch("SELECT id, username, email FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

// Log the logout activity
if ($user) {
    logActivity('user_logout', 'User logged out: ' . $user['username'], $user['id']);
}

// Clear all session data
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Clear remember me cookie if exists
if (isset($_COOKIE['remember_token'])) {
    $db = Database::getInstance();
    
    // Get the token hash
    $tokenHash = hash('sha256', $_COOKIE['remember_token']);
    
    // Delete the remember token from database
    $db->delete('remember_tokens', 'token_hash = ?', [$tokenHash]);
    
    // Delete the cookie
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
}

// Set flash message
setFlashMessage('success', 'You have been successfully logged out.');

// Redirect to login page
redirect('login.php');
?> 