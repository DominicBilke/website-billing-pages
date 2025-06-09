<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $template->getPageTitle(); ?> - <?php echo APP_NAME; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo APP_URL; ?>/assets/img/favicon.ico">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    
    <!-- Additional styles -->
    <?php echo $template->getAdditionalCss(); ?>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-calculator me-2"></i>
                <?php echo APP_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <?php if ($auth->isLoggedIn()): ?>
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-building me-1"></i> Companies
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="company_insert.php">Add Company</a></li>
                                <li><a class="dropdown-item" href="company_overview.php">Overview</a></li>
                                <li><a class="dropdown-item" href="company_evaluation.php">Evaluation</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-route me-1"></i> Tours
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="tour_insert.php">Add Tour</a></li>
                                <li><a class="dropdown-item" href="tour_overview.php">Overview</a></li>
                                <li><a class="dropdown-item" href="tour_evaluation.php">Evaluation</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-tasks me-1"></i> Tasks
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="task_insert.php">Add Task</a></li>
                                <li><a class="dropdown-item" href="task_overview.php">Overview</a></li>
                                <li><a class="dropdown-item" href="task_evaluation.php">Evaluation</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-briefcase me-1"></i> Work
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="work_insert.php">Add Work</a></li>
                                <li><a class="dropdown-item" href="work_overview.php">Overview</a></li>
                                <li><a class="dropdown-item" href="work_evaluation.php">Evaluation</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-money-bill me-1"></i> Money
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="money_insert.php">Add Entry</a></li>
                                <li><a class="dropdown-item" href="money_overview.php">Overview</a></li>
                                <li><a class="dropdown-item" href="money_evaluation.php">Evaluation</a></li>
                            </ul>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i> <?php echo $auth->getUsername(); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                <?php else: ?>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Breadcrumbs -->
    <?php if ($template->hasBreadcrumbs()): ?>
    <nav aria-label="breadcrumb" class="bg-light py-2">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <?php foreach ($template->getBreadcrumbs() as $breadcrumb): ?>
                <li class="breadcrumb-item"><?php echo $breadcrumb; ?></li>
                <?php endforeach; ?>
            </ol>
        </div>
    </nav>
    <?php endif; ?>
    
    <!-- Messages -->
    <?php if ($template->hasMessages()): ?>
    <div class="container mt-3">
        <?php foreach ($template->getMessages() as $type => $messages): ?>
        <?php foreach ($messages as $message): ?>
        <div class="alert alert-<?php echo $type; ?> alert-dismissible fade show">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="flex-grow-1 py-4">
        <?php echo $template->getContent(); ?>
    </main>
    
    <!-- Footer -->
    <footer class="footer mt-auto py-4 bg-dark text-light">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="mb-3"><?php echo APP_NAME; ?></h5>
                    <p class="mb-0">Professional billing and accounting system for managing companies, tours, tasks, work entries, and money transactions.</p>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light">Dashboard</a></li>
                        <li><a href="help.php" class="text-light">Help & Support</a></li>
                        <li><a href="contact.php" class="text-light">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Legal</h5>
                    <ul class="list-unstyled">
                        <li><a href="imprint.php" class="text-light">Imprint</a></li>
                        <li><a href="privacy.php" class="text-light">Privacy Policy</a></li>
                        <li><a href="copyright.php" class="text-light">Copyright</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Version <?php echo APP_VERSION; ?></p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="assets/js/script.js"></script>
    
    <!-- Additional scripts -->
    <?php echo $template->getAdditionalJs(); ?>
</body>
</html> 