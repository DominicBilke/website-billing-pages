<?php
require_once __DIR__ . '/../../inc/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$errors = [];
$success = false;
$formData = [
    'username' => '',
    'email' => '',
    'first_name' => '',
    'last_name' => ''
];

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'username' => sanitizeInput($_POST['username'] ?? ''),
        'email' => sanitizeInput($_POST['email'] ?? ''),
        'first_name' => sanitizeInput($_POST['first_name'] ?? ''),
        'last_name' => sanitizeInput($_POST['last_name'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? ''
    ];
    
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        // Attempt registration
        $auth = new Auth();
        $result = $auth->register($formData);
        
        if ($result['success']) {
            $success = true;
            setFlashMessage('success', 'Registration successful! Please check your email to verify your account.');
        } else {
            $errors = $result['errors'];
        }
    }
}

$pageTitle = 'Register - Billing Pages';
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
        <div class="row justify-content-center align-items-center min-vh-100 py-4">
            <div class="col-md-8 col-lg-6">
                <!-- Logo -->
                <div class="text-center mb-4">
                    <a href="<?= getBaseUrl() ?>" class="text-decoration-none">
                        <h2 class="text-primary fw-bold">
                            <i class="fas fa-file-invoice me-2"></i>
                            Billing Pages
                        </h2>
                    </a>
                    <p class="text-muted">Create your account to get started</p>
                </div>

                <!-- Success Message -->
                <?php if ($success): ?>
                    <div class="card shadow-sm">
                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <i class="fas fa-check-circle text-success fa-3x"></i>
                            </div>
                            <h4 class="text-success mb-3">Registration Successful!</h4>
                            <p class="text-muted mb-4">
                                We've sent a verification email to <strong><?= h($formData['email']) ?></strong>. 
                                Please check your inbox and click the verification link to activate your account.
                            </p>
                            <div class="d-grid gap-2">
                                <a href="login.php" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Go to Login
                                </a>
                                <a href="<?= getBaseUrl() ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-home me-2"></i>
                                    Back to Home
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Registration Card -->
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

                            <!-- Registration Form -->
                            <form method="POST" action="" id="registerForm">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                
                                <div class="row">
                                    <!-- First Name -->
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="first_name" 
                                               name="first_name" 
                                               value="<?= h($formData['first_name']) ?>"
                                               required 
                                               autocomplete="given-name"
                                               placeholder="Enter your first name">
                                    </div>

                                    <!-- Last Name -->
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="last_name" 
                                               name="last_name" 
                                               value="<?= h($formData['last_name']) ?>"
                                               required 
                                               autocomplete="family-name"
                                               placeholder="Enter your last name">
                                    </div>
                                </div>

                                <!-- Username -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               id="username" 
                                               name="username" 
                                               value="<?= h($formData['username']) ?>"
                                               required 
                                               autocomplete="username"
                                               placeholder="Choose a username">
                                    </div>
                                    <div class="form-text">
                                        Username must be 3-20 characters long and contain only letters, numbers, and underscores.
                                    </div>
                                </div>

                                <!-- Email -->
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
                                               value="<?= h($formData['email']) ?>"
                                               required 
                                               autocomplete="email"
                                               placeholder="Enter your email address">
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
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
                                               placeholder="Create a strong password">
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        Password must be at least 8 characters long and contain uppercase, lowercase, number, and special character.
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
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
                                               placeholder="Confirm your password">
                                    </div>
                                </div>

                                <!-- Terms and Conditions -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="terms" 
                                               name="terms" 
                                               required>
                                        <label class="form-check-label" for="terms">
                                            I agree to the 
                                            <a href="<?= getBaseUrl() ?>/terms.php" target="_blank">Terms of Service</a> 
                                            and 
                                            <a href="<?= getBaseUrl() ?>/privacy.php" target="_blank">Privacy Policy</a>
                                        </label>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-user-plus me-2"></i>
                                        Create Account
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Sign In Link -->
                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">
                            Already have an account? 
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
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const requiredFields = ['username', 'email', 'first_name', 'last_name', 'password', 'confirm_password'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const element = document.getElementById(field);
                if (!element.value.trim()) {
                    element.classList.add('is-invalid');
                    isValid = false;
                } else {
                    element.classList.remove('is-invalid');
                }
            });
            
            if (!document.getElementById('terms').checked) {
                alert('Please agree to the Terms of Service and Privacy Policy.');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating account...';
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

        // Auto-focus first name field
        document.getElementById('first_name').focus();
    </script>
</body>
</html> 