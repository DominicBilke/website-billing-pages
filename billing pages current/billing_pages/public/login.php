<?php
require_once __DIR__ . '/../inc/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    if (login($email, $password)) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Billing Portal</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <?php if (isset($error)) echo "<div class='alert alert-error'>" . htmlspecialchars($error) . "</div>"; ?>
        <form method="POST" class="form">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
            <div class="form-links">
                <a href="forgot_password.php">Forgot Password?</a>
                <span class="separator">|</span>
                <a href="register.php">Register here</a>
            </div>
        </form>
    </div>
</body>
</html> 