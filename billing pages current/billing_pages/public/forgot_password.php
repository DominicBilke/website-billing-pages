<?php
require_once __DIR__ . '/../inc/auth.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $token = generateResetToken($email);
        
        if ($token) {
            if (sendResetEmail($email, $token)) {
                $message = "Password reset instructions have been sent to your email address.";
            } else {
                $error = "Failed to send reset email. Please try again later.";
            }
        } else {
            $error = "If an account exists with this email, you will receive password reset instructions.";
            // We don't reveal if the email exists or not for security reasons
        }
    } else {
        $error = "Please enter a valid email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Billing Portal</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Forgot Password</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" class="form">
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </div>
            
            <div class="form-links">
                <a href="login.php">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html> 