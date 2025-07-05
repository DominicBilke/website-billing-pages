<?php
require_once __DIR__ . '/../inc/config.php';

$pageTitle = 'Server Error - Billing Pages';
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
            <div class="col-md-6 text-center">
                <!-- 500 Icon -->
                <div class="mb-4">
                    <i class="fas fa-server fa-5x text-danger"></i>
                </div>
                
                <!-- Error Message -->
                <h1 class="display-1 text-muted mb-3">500</h1>
                <h2 class="h4 text-muted mb-4">Internal Server Error</h2>
                <p class="text-muted mb-4">
                    Something went wrong on our end. We're working to fix the issue. 
                    Please try again in a few moments.
                </p>
                
                <!-- Action Buttons -->
                <div class="d-grid gap-2 d-md-block">
                    <a href="<?= getBaseUrl() ?>" class="btn btn-primary me-md-2">
                        <i class="fas fa-home me-2"></i>
                        Go Home
                    </a>
                    <button onclick="location.reload()" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2"></i>
                        Try Again
                    </button>
                </div>
                
                <!-- Contact Support -->
                <div class="mt-5">
                    <p class="text-muted mb-3">Still having issues?</p>
                    <a href="mailto:support@billing-pages.com" class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-2"></i>
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 