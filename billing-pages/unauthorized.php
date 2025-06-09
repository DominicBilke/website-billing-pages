<?php
require_once 'script/config.php';
require_once 'script/auth.php';
require_once 'script/template.php';

// Set page title
$template->setPageTitle('Unauthorized');

// Add breadcrumbs
$template->addBreadcrumb('Unauthorized');

// Add error message
$template->addMessage('You do not have permission to access this page.', 'danger');

// Start output buffering
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <div class="card">
            <div class="card-body">
                <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                <h2 class="mb-4">Access Denied</h2>
                <p class="lead mb-4">You do not have the required permissions to access this page.</p>
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i> Return to Dashboard
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