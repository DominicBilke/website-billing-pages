<?php
require_once __DIR__ . '/../../inc/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$errors = [];
$success = false;
$token = sanitizeInput($_GET['token'] ?? '');
$tokenValid = false;
$user = null;

// Validate token
if (!empty($token)) {
    $db = Database::getInstance();
    $tokenHash = hash('sha256', $token);
    
    // Get reset record
    $resetRecord = $db->fetch(
        "SELECT * FROM password_resets WHERE token_hash = ? AND expires_at > NOW() AND used = 0",
        [$tokenHash]
    );
    
    if ($resetRecord) {
        $tokenValid = true;
        $user = $db->fetch("SELECT id, username, email, first_name FROM users WHERE id = ?", [$resetRecord['user_id']]);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValid) {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        // Validate password
        if (empty($password)) {
            $errors[] = 'Please enter a new password.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }
        
        // If no errors, update password
        if (empty($errors)) {
            try {
                $db->beginTransaction();
                
                // Update user password
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $db->update('users', 
                    ['password_hash' => $passwordHash, 'updated_at' => date('Y-m-d H:i:s')], 
                    'id = ?', 
                    [$user['id']]
                );
                
                // Mark reset token as used
                $db->update('password_resets', 
                    ['used' => 1, 'used_at' => date('Y-m-d H:i:s')], 
                    'token_hash = ?', 
                    [$tokenHash]
                );
                
                // Clear any existing sessions for this user
                $db->delete('sessions', 'user_id = ?', [$user['id']]);
                
                $db->commit();
                
                $success = true;
                logActivity('password_reset_completed', 'Password reset completed for: ' . $user['email'], $user['id']);
                
            } catch (Exception $e) {
                $db->rollback();
                $errors[] = 'Failed to reset password. Please try again.';
            }
        }
    }
}

$pageTitle = 'Reset Password - Billing Pages';
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
                            <h4 class="text-success mb-3">Password Reset Successful!</h4>
                            <p class="text-muted mb-4">
                                Your password has been successfully reset. You can now log in with your new password.
                            </p>
                            <div class="d-grid">
                                <a href="login.php" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Go to Login
                                </a>
                            </div>
                        </div>
                    </div>
                <?php elseif (!$tokenValid): ?>
                    <!-- Invalid Token -->
                    <div class="card shadow-sm">
                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <i class="fas fa-exclamation-triangle text-warning fa-3x"></i>
                            </div>
                            <h4 class="text-warning mb-3">Invalid or Expired Link</h4>
                            <p class="text-muted mb-4">
                                This password reset link is invalid or has expired. Please request a new password reset.
                            </p>
                            <div class="d-grid gap-2">
                                <a href="forgot-password.php" class="btn btn-primary">
                                    <i class="fas fa-key me-2"></i>
                                    Request New Reset
                                </a>
                                <a href="login.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Back to Login
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Reset Password Form -->
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
                                Hello <strong><?= h($user['first_name'] ?: $user['username']) ?></strong>, 
                                please enter your new password below.
                            </p>

                            <!-- Reset Password Form -->
                            <form method="POST" action="" id="resetPasswordForm">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                
                                <!-- New Password Field -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password" 
                                               name="password" 
                                               required 
                                               autocomplete="new-password"
                                               placeholder="Enter new password">
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        Password must be at least 8 characters with uppercase, lowercase, number, and special character.
                                    </div>
                                </div>

                                <!-- Confirm Password Field -->
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" 
                                               class="form-control" 
                                               id="confirm_password" 
                                               name="confirm_password" 
                                               required 
                                               autocomplete="new-password"
                                               placeholder="Confirm new password">
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-key me-2"></i>
                                        Reset Password
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
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            updatePasswordStrengthIndicator(strength);
        });

        // Confirm password validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        // Form validation
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (!password || !confirmPassword) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match.');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Resetting...';
            submitBtn.disabled = true;
        });

        // Password strength checker
        function checkPasswordStrength(password) {
            let score = 0;
            
            if (password.length >= 8) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;
            
            if (score < 3) return 'weak';
            if (score < 5) return 'medium';
            return 'strong';
        }

        function updatePasswordStrengthIndicator(strength) {
            const passwordField = document.getElementById('password');
            const existingIndicator = document.getElementById('password-strength');
            
            if (existingIndicator) {
                existingIndicator.remove();
            }
            
            const indicator = document.createElement('div');
            indicator.id = 'password-strength';
            indicator.className = 'mt-1';
            
            let color, text;
            switch (strength) {
                case 'weak':
                    color = 'danger';
                    text = 'Weak password';
                    break;
                case 'medium':
                    color = 'warning';
                    text = 'Medium strength password';
                    break;
                case 'strong':
                    color = 'success';
                    text = 'Strong password';
                    break;
            }
            
            indicator.innerHTML = `<small class="text-${color}"><i class="fas fa-shield-alt me-1"></i>${text}</small>`;
            passwordField.parentNode.appendChild(indicator);
        }

        // Auto-focus password field
        document.getElementById('password').focus();
    </script>
</body>
</html> 