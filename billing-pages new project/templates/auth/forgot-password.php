<?php
$this->setPageTitle('Forgot Password');
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="/assets/img/logo.png" alt="Logo" class="mb-4" style="max-width: 150px;">
                        <h4 class="mb-0">Forgot Password</h4>
                        <p class="text-muted">Enter your email to reset your password</p>
                    </div>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="/forgot-password.php" method="POST" class="needs-validation" novalidate>
                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                                       required 
                                       autofocus>
                            </div>
                            <div class="invalid-feedback">
                                Please enter a valid email address.
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Send Reset Link
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <a href="/login.php" class="text-muted">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer Links -->
            <div class="text-center mt-4">
                <p class="text-muted">
                    Don't have an account? 
                    <a href="/register.php">Contact your administrator</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function() {
    'use strict';
    
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
})();
</script> 