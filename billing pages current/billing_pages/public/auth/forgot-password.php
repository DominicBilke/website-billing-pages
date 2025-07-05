<?php
require_once __DIR__ . '/../../inc/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$errors = [];
$success = false;
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        // Validate email
        if (empty($email)) {
            $errors[] = 'Please enter your email address.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } else {
            $db = Database::getInstance();
            
            // Check if user exists
            $user = $db->fetch("SELECT id, username, email, first_name FROM users WHERE email = ?", [$email]);
            
            if ($user) {
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $token);
                $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store reset token
                $resetData = [
                    'user_id' => $user['id'],
                    'token_hash' => $tokenHash,
                    'expires_at' => $expiresAt,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $db->insert('password_resets', $resetData);
                
                // Send reset email
                $resetLink = getBaseUrl() . 'auth/reset-password.php?token=' . $token;
                
                $to = $user['email'];
                $subject = 'Password Reset Request - Billing Pages';
                $message = "
                <html>
                <body>
                    <h2>Password Reset Request</h2>
                    <p>Hello " . h($user['first_name'] ?: $user['username']) . ",</p>
                    <p>We received a request to reset your password for your Billing Pages account.</p>
                    <p>Click the link below to reset your password:</p>
                    <p><a href='{$resetLink}' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
                    <p>This link will expire in 1 hour.</p>
                    <p>If you didn't request this password reset, please ignore this email.</p>
                    <p>Best regards,<br>Billing Pages Team</p>
                </body>
                </html>
                ";
                
                $headers = [
                    'MIME-Version: 1.0',
                    'Content-type: text/html; charset=UTF-8',
                    'From: Billing Pages <noreply@billing-pages.com>',
                    'Reply-To: support@billing-pages.com',
                    'X-Mailer: PHP/' . phpversion()
                ];
                
                if (mail($to, $subject, $message, implode("\r\n", $headers))) {
                    $success = true;
                    logActivity('password_reset_requested', 'Password reset requested for: ' . $email, $user['id']);
                } else {
                    $errors[] = 'Failed to send reset email. Please try again.';
                }
            } else {
                // Don't reveal if email exists or not for security
                $success = true;
            }
        }
    }
}

$pageTitle = 'Forgot Password - Billing Pages';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?></title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= getAssetUrl('css/style.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <!-- Logo -->
                <div class="text-center mb-4">
                    <a href="<?= getBaseUrl() ?>" class="text-decoration-none">
                        <h2 class="text-primary fw-bold">
                            <i class="fas fa-file-invoice me-2"></i>
                            Billing Pages
                        </h2>
                    </a>
                    <p class="text-muted">Reset your password</p>
                </div>

                <?php if ($success): ?>
                    <!-- Success Message -->
                    <div class="card shadow-sm">
                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <i class="fas fa-check-circle text-success fa-3x"></i>
                            </div>
                            <h4 class="text-success mb-3">Check Your Email</h4>
                            <p class="text-muted mb-4">
                                We've sent password reset instructions to <strong><?= h($email) ?></strong>. 
                                Please check your inbox and follow the link to reset your password.
                            </p>
                            <div class="d-grid gap-2">
                                <a href="login.php" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Back to Login
                                </a>
                                <a href="<?= getBaseUrl() ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-home me-2"></i>
                                    Go Home
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Forgot Password Form -->
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <!-- Error Messages -->
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?= h($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <p class="text-muted mb-4">
                                Enter your email address and we'll send you a link to reset your password.
                            </p>

                            <!-- Forgot Password Form -->
                            <form method="POST" action="" id="forgotPasswordForm">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                
                                <!-- Email Field -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email" 
                                               value="<?= h($email) ?>"
                                               required 
                                               autocomplete="email"
                                               placeholder="Enter your email address">
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Send Reset Link
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Links -->
                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">
                            Remember your password? 
                            <a href="login.php" class="text-decoration-none">Sign in</a>
                        </p>
                    </div>

                    <!-- Back to Home -->
                    <div class="text-center mt-3">
                        <a href="<?= getBaseUrl() ?>" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to home
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            
            if (!email) {
                e.preventDefault();
                alert('Please enter your email address.');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
            submitBtn.disabled = true;
        });

        // Auto-focus email field
        document.getElementById('email').focus();
    </script>
</body>
</html> 