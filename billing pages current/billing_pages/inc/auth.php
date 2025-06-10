<?php
session_start();
require_once __DIR__ . '/../config/db.php';

function register($name, $email, $password) {
    global $conn;
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password_hash);
    return $stmt->execute();
}

function login($email, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, name, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            return true;
        }
    }
    return false;
}

function logout() {
    session_destroy();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function generateResetToken($email) {
    global $conn;
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }
    
    // Generate token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Store token in database
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $token, $expires);
    
    if ($stmt->execute()) {
        return $token;
    }
    
    return false;
}

function verifyResetToken($token) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW() AND used = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['email'];
    }
    
    return false;
}

function resetPassword($token, $new_password) {
    global $conn;
    
    $email = verifyResetToken($token);
    if (!$email) {
        return false;
    }
    
    // Update password
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
    $stmt->bind_param("ss", $password_hash, $email);
    
    if ($stmt->execute()) {
        // Mark token as used
        $stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        return true;
    }
    
    return false;
}

function sendResetEmail($email, $token) {
    $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
    $subject = "Password Reset Request";
    $message = "Hello,\n\n";
    $message .= "You have requested to reset your password. Click the link below to reset your password:\n\n";
    $message .= $reset_link . "\n\n";
    $message .= "This link will expire in 1 hour.\n\n";
    $message .= "If you did not request this password reset, please ignore this email.\n\n";
    $message .= "Best regards,\nBilling Portal Team";
    
    $headers = "From: noreply@billingportal.com\r\n";
    $headers .= "Reply-To: noreply@billingportal.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    return mail($email, $subject, $message, $headers);
}
?> 