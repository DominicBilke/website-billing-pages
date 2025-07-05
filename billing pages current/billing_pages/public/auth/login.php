<?php
require_once __DIR__ . '/../../inc/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$errors = [];
$email = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        // Attempt login
        $auth = new Auth();
        $result = $auth->login($email, $password, $remember);
        
        if ($result['success']) {
            setFlashMessage('success', 'Welcome back! You have been successfully logged in.');
            redirect('dashboard.php');
        } else {
            $errors = $result['errors'];
        }
    }
}

$pageTitle = 'Login - Billing Pages';
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
                    <p class="text-muted">Sign in to your account</p>
                </div>

                <!-- Login Card -->
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

                        <!-- Login Form -->
                        <form method="POST" action="" id="loginForm">
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
                                           placeholder="Enter your email">
                                </div>
                            </div>

                            <!-- Password Field -->
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
                                           autocomplete="current-password"
                                           placeholder="Enter your password">
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="remember" 
                                           name="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember me
                                    </label>
                                </div>
                                <a href="forgot-password.php" class="text-decoration-none">
                                    Forgot password?
                                </a>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Sign In
                                </button>
                            </div>
                        </form>

                        <!-- Divider -->
                        <div class="text-center my-4">
                            <span class="text-muted">or</span>
                        </div>

                        <!-- Social Login (Future Feature) -->
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary" disabled>
                                <i class="fab fa-google me-2"></i>
                                Sign in with Google
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sign Up Link -->
                <div class="text-center mt-4">
                    <p class="text-muted mb-0">
                        Don't have an account? 
                        <a href="register.php" class="text-decoration-none">Sign up</a>
                    </p>
                </div>

                <!-- Back to Home -->
                <div class="text-center mt-3">
                    <a href="<?= getBaseUrl() ?>" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to home
                    </a>
                </div>
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

        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing in...';
            submitBtn.disabled = true;
        });

        // Auto-focus email field
        document.getElementById('email').focus();
    </script>
</body>
</html> 