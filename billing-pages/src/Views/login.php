<?php
$title = 'Login - Billing Pages';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Login
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/api/login" id="loginForm">
                    <div class="mb-3">
                        <label for="domain" class="form-label">
                            <i class="fas fa-globe me-1"></i>
                            Domain
                        </label>
                        <input type="text" class="form-control" id="domain" name="domain" 
                               value="<?= htmlspecialchars($_POST['domain'] ?? '') ?>" 
                               required autofocus>
                        <div class="form-text">Enter your domain name (e.g., mycompany.billing-pages.com)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user me-1"></i>
                            Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-1"></i>
                            Password
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Login
                        </button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <p class="text-muted mb-2">Don't have an account?</p>
                    <a href="/register" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-user-plus me-1"></i>
                        Register
                    </a>
                </div>
                
                <div class="text-center mt-3">
                    <a href="/forgot-password" class="text-muted text-decoration-none">
                        <i class="fas fa-key me-1"></i>
                        Forgot Password?
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Demo Information -->
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Demo Information
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">
                    For demonstration purposes, you can use these credentials:
                </p>
                <ul class="small text-muted">
                    <li><strong>Domain:</strong> demo.billing-pages.com</li>
                    <li><strong>Username:</strong> admin</li>
                    <li><strong>Password:</strong> password123</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    
    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
    
    // Form validation
    const loginForm = document.getElementById('loginForm');
    loginForm.addEventListener('submit', function(e) {
        const domain = document.getElementById('domain').value.trim();
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        
        if (!domain || !username || !password) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return false;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Logging in...';
        submitBtn.disabled = true;
        
        // Re-enable after 5 seconds if no response
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 5000);
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?> 