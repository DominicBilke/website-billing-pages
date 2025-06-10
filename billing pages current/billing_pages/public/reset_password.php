<?php
require_once __DIR__ . '/../inc/auth.php';

$message = '';
$error = '';
$valid_token = false;
$token = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $email = verifyResetToken($token);
    if ($email) {
        $valid_token = true;
    } else {
        $error = "Invalid or expired reset token. Please request a new password reset.";
    }
} else {
    $error = "No reset token provided.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $valid_token) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        if (resetPassword($token, $new_password)) {
            $message = "Your password has been reset successfully. You can now login with your new password.";
            $valid_token = false; // Prevent further use of the form
        } else {
            $error = "Failed to reset password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Billing Portal</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <div class="form-links">
                <a href="login.php">Go to Login</a>
            </div>
        <?php elseif ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php if (!$valid_token): ?>
                <div class="form-links">
                    <a href="forgot_password.php">Request New Reset Link</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($valid_token): ?>
            <form method="POST" class="form">
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required 
                           minlength="8" autocomplete="new-password">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required 
                           minlength="8" autocomplete="new-password">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html> 