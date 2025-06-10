<?php
require_once 'script/config.php';
require_once 'script/auth.php';
require_once 'script/template.php';
require_once 'script/database.php';

// Check if user is already logged in
if ($auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Get token from URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: login.php');
    exit;
}

// Verify token
$db = Database::getInstance();
$result = $db->query("
    SELECT pr.*, u.email 
    FROM password_resets pr 
    JOIN users u ON pr.user_id = u.id 
    WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used = 0
", [$token], 's');

if (!$result || $result->num_rows === 0) {
    $template->addMessage('Invalid or expired password reset link.', 'danger');
    header('Location: login.php');
    exit;
}

$reset = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($password) || empty($confirmPassword)) {
        $template->addMessage('Please enter both password fields.', 'danger');
    } elseif ($password !== $confirmPassword) {
        $template->addMessage('Passwords do not match.', 'danger');
    } elseif (strlen($password) < 8) {
        $template->addMessage('Password must be at least 8 characters long.', 'danger');
    } else {
        // Update password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT, ['cost' => PASSWORD_HASH_COST]);
        
        $db->beginTransaction();
        
        try {
            // Update user's password
            $db->query("UPDATE users SET password = ? WHERE id = ?", [$hashedPassword, $reset['user_id']], 'si');
            
            // Mark reset token as used
            $db->query("UPDATE password_resets SET used = 1 WHERE token = ?", [$token], 's');
            
            $db->commit();
            
            $template->addMessage('Your password has been reset successfully. You can now login with your new password.', 'success');
            header('Location: login.php');
            exit;
        } catch (Exception $e) {
            $db->rollback();
            $template->addMessage('An error occurred while resetting your password. Please try again.', 'danger');
        }
    }
}

// Set page title
$template->setPageTitle('Reset Password');

// Add breadcrumbs
$template->addBreadcrumb('Reset Password');

// Start output buffering
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-0">Reset Password</h4>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    Please enter your new password below.
                </p>
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?token=' . $token); ?>" id="resetPasswordForm">
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">
                            Password must be at least 8 characters long.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key me-2"></i> Reset Password
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