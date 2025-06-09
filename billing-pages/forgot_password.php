<?php
require_once 'script/config.php';
require_once 'script/auth.php';
require_once 'script/template.php';

// Check if user is already logged in
if ($auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (empty($email)) {
        $template->addMessage('Please enter your email address.', 'danger');
    } else {
        if ($auth->resetPassword($email)) {
            $template->addMessage('Password reset instructions have been sent to your email.', 'success');
        } else {
            $template->addMessage('No account found with that email address.', 'danger');
        }
    }
}

// Set page title
$template->setPageTitle('Forgot Password');

// Add breadcrumbs
$template->addBreadcrumb('Forgot Password');

// Start output buffering
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-0">Forgot Password</h4>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    Enter your email address and we'll send you instructions to reset your password.
                </p>
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="forgotPasswordForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control" id="email" name="email" required autofocus>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i> Send Reset Link
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <a href="login.php" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
</div>

<?php
// Get the content and render the template
$content = ob_get_clean();
$template->setContent($content);
echo $template->render(); 