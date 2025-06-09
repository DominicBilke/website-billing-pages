<?php
require_once 'script/config.php';
require_once 'script/auth.php';
require_once 'script/template.php';

// Get error code
$code = $_GET['code'] ?? '404';
$messages = [
    '400' => 'Bad Request',
    '401' => 'Unauthorized',
    '403' => 'Forbidden',
    '404' => 'Page Not Found',
    '500' => 'Internal Server Error'
];

$message = $messages[$code] ?? 'Unknown Error';

// Set page title
$template->setPageTitle("Error $code");

// Add breadcrumbs
$template->addBreadcrumb('Error');

// Start output buffering
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <div class="error-page">
            <h1 class="display-1 text-danger"><?php echo $code; ?></h1>
            <h2 class="mb-4"><?php echo $message; ?></h2>
            <p class="text-muted mb-4">
                <?php
                switch ($code) {
                    case '400':
                        echo 'The server could not understand your request.';
                        break;
                    case '401':
                        echo 'You need to be authenticated to access this page.';
                        break;
                    case '403':
                        echo 'You do not have permission to access this page.';
                        break;
                    case '404':
                        echo 'The page you are looking for could not be found.';
                        break;
                    case '500':
                        echo 'An internal server error occurred. Please try again later.';
                        break;
                    default:
                        echo 'An unexpected error occurred.';
                }
                ?>
            </p>
            <div class="mt-4">
                <a href="javascript:history.back()" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>Go Back
                </a>
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>Go to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.error-page {
    padding: 60px 0;
}
.error-page h1 {
    font-size: 120px;
    font-weight: 700;
    margin-bottom: 20px;
}
.error-page h2 {
    font-size: 32px;
    font-weight: 600;
}
</style>

<?php
// Get the content and render the template
$content = ob_get_clean();
$template->setContent($content);
echo $template->render(); 