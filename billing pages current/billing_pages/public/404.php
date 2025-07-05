<?php
require_once __DIR__ . '/../inc/config.php';

$pageTitle = 'Page Not Found - Billing Pages';
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
                <!-- 404 Icon -->
                <div class="mb-4">
                    <i class="fas fa-exclamation-triangle fa-5x text-warning"></i>
                </div>
                
                <!-- Error Message -->
                <h1 class="display-1 text-muted mb-3">404</h1>
                <h2 class="h4 text-muted mb-4">Page Not Found</h2>
                <p class="text-muted mb-4">
                    The page you're looking for doesn't exist or has been moved. 
                    Please check the URL and try again.
                </p>
                
                <!-- Action Buttons -->
                <div class="d-grid gap-2 d-md-block">
                    <a href="<?= getBaseUrl() ?>" class="btn btn-primary me-md-2">
                        <i class="fas fa-home me-2"></i>
                        Go Home
                    </a>
                    <button onclick="history.back()" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Go Back
                    </button>
                </div>
                
                <!-- Search -->
                <div class="mt-5">
                    <p class="text-muted mb-3">Looking for something specific?</p>
                    <form action="<?= getBaseUrl() ?>" method="GET" class="d-flex justify-content-center">
                        <div class="input-group" style="max-width: 400px;">
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="Search our site...">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 