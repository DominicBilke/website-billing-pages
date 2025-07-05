<?php
require_once __DIR__ . '/../inc/config.php';

// Require authentication
requireAuth();

// Get user data
$user = getUserData();
if (!$user) {
    redirect('auth/login.php');
}

$errors = [];
$success = false;
$formData = [
    'username' => $user['username'],
    'email' => $user['email'],
    'first_name' => $user['first_name'] ?? '',
    'last_name' => $user['last_name'] ?? '',
    'phone' => $user['phone'] ?? '',
    'company_name' => $user['company_name'] ?? '',
    'timezone' => $user['timezone'] ?? 'UTC',
    'currency' => $user['currency'] ?? 'USD'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $formData = [
            'username' => sanitizeInput($_POST['username'] ?? ''),
            'email' => sanitizeInput($_POST['email'] ?? ''),
            'first_name' => sanitizeInput($_POST['first_name'] ?? ''),
            'last_name' => sanitizeInput($_POST['last_name'] ?? ''),
            'phone' => sanitizeInput($_POST['phone'] ?? ''),
            'company_name' => sanitizeInput($_POST['company_name'] ?? ''),
            'timezone' => sanitizeInput($_POST['timezone'] ?? 'UTC'),
            'currency' => sanitizeInput($_POST['currency'] ?? 'USD')
        ];

        // Validation
        if (empty($formData['username'])) {
            $errors[] = 'Username is required.';
        }

        if (empty($formData['email'])) {
            $errors[] = 'Email address is required.';
        } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (!empty($formData['phone']) && !preg_match('/^[\+]?[1-9][\d]{0,15}$/', preg_replace('/[^0-9+]/', '', $formData['phone']))) {
            $errors[] = 'Please enter a valid phone number.';
        }

        // Check if username or email already exists (excluding current user)
        $db = Database::getInstance();
        $existingUser = $db->fetch(
            "SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?", 
            [$formData['username'], $formData['email'], $user['id']]
        );
        
        if ($existingUser) {
            $errors[] = 'Username or email address is already in use.';
        }

        // If no errors, update profile
        if (empty($errors)) {
            $updateData = array_merge($formData, [
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $result = $db->update('users', $updateData, 'id = ?', [$user['id']]);
            
            if ($result) {
                $success = true;
                logActivity('profile_updated', 'Profile updated', $user['id']);
                setFlashMessage('success', 'Profile updated successfully!');
                
                // Update session data
                $_SESSION['user_data'] = array_merge($user, $formData);
            } else {
                $errors[] = 'Failed to update profile. Please try again.';
            }
        }
    }
}

$pageTitle = 'Profile - Billing Pages';
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
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="<?= getBaseUrl() ?>">
                <i class="fas fa-file-invoice me-2"></i>
                Billing Pages
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="invoices.php">
                            <i class="fas fa-file-invoice me-1"></i>
                            Invoices
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="clients.php">
                            <i class="fas fa-users me-1"></i>
                            Clients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-chart-bar me-1"></i>
                            Reports
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?= h($user['first_name'] ?: $user['username']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item active" href="profile.php">
                                <i class="fas fa-user me-2"></i>Profile
                            </a></li>
                            <li><a class="dropdown-item" href="settings.php">
                                <i class="fas fa-cog me-2"></i>Settings
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="auth/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <!-- Flash Messages -->
        <?php $flashMessage = getFlashMessage(); ?>
        <?php if ($flashMessage): ?>
            <div class="alert alert-<?= $flashMessage['type'] ?> alert-dismissible fade show" role="alert">
                <?= h($flashMessage['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Profile Settings</h1>
                <p class="text-muted mb-0">Manage your account information</p>
            </div>
        </div>

        <div class="row">
            <!-- Profile Form -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>
                            Personal Information
                        </h5>
                    </div>
                    <div class="card-body">
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

                        <!-- Success Message -->
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                Profile updated successfully!
                            </div>
                        <?php endif; ?>

                        <!-- Profile Form -->
                        <form method="POST" action="" id="profileForm">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            
                            <div class="row">
                                <!-- Username -->
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">
                                        Username <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="username" 
                                           name="username" 
                                           value="<?= h($formData['username']) ?>"
                                           required>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        Email Address <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           value="<?= h($formData['email']) ?>"
                                           required>
                                </div>

                                <!-- First Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="first_name" 
                                           name="first_name" 
                                           value="<?= h($formData['first_name']) ?>">
                                </div>

                                <!-- Last Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="<?= h($formData['last_name']) ?>">
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="phone" 
                                           name="phone" 
                                           value="<?= h($formData['phone']) ?>"
                                           placeholder="+1 (555) 123-4567">
                                </div>

                                <!-- Company Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="company_name" 
                                           name="company_name" 
                                           value="<?= h($formData['company_name']) ?>">
                                </div>

                                <!-- Timezone -->
                                <div class="col-md-6 mb-3">
                                    <label for="timezone" class="form-label">Timezone</label>
                                    <select class="form-select" id="timezone" name="timezone">
                                        <option value="UTC" <?= $formData['timezone'] === 'UTC' ? 'selected' : '' ?>>UTC</option>
                                        <option value="America/New_York" <?= $formData['timezone'] === 'America/New_York' ? 'selected' : '' ?>>Eastern Time</option>
                                        <option value="America/Chicago" <?= $formData['timezone'] === 'America/Chicago' ? 'selected' : '' ?>>Central Time</option>
                                        <option value="America/Denver" <?= $formData['timezone'] === 'America/Denver' ? 'selected' : '' ?>>Mountain Time</option>
                                        <option value="America/Los_Angeles" <?= $formData['timezone'] === 'America/Los_Angeles' ? 'selected' : '' ?>>Pacific Time</option>
                                        <option value="Europe/London" <?= $formData['timezone'] === 'Europe/London' ? 'selected' : '' ?>>London</option>
                                        <option value="Europe/Paris" <?= $formData['timezone'] === 'Europe/Paris' ? 'selected' : '' ?>>Paris</option>
                                        <option value="Asia/Tokyo" <?= $formData['timezone'] === 'Asia/Tokyo' ? 'selected' : '' ?>>Tokyo</option>
                                    </select>
                                </div>

                                <!-- Currency -->
                                <div class="col-md-6 mb-3">
                                    <label for="currency" class="form-label">Default Currency</label>
                                    <select class="form-select" id="currency" name="currency">
                                        <option value="USD" <?= $formData['currency'] === 'USD' ? 'selected' : '' ?>>USD ($)</option>
                                        <option value="EUR" <?= $formData['currency'] === 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                                        <option value="GBP" <?= $formData['currency'] === 'GBP' ? 'selected' : '' ?>>GBP (£)</option>
                                        <option value="CAD" <?= $formData['currency'] === 'CAD' ? 'selected' : '' ?>>CAD (C$)</option>
                                        <option value="AUD" <?= $formData['currency'] === 'AUD' ? 'selected' : '' ?>>AUD (A$)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Account Info Sidebar -->
            <div class="col-lg-4">
                <!-- Account Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Account Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Member Since</label>
                            <div class="fw-bold"><?= formatDate($user['created_at']) ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Last Login</label>
                            <div class="fw-bold"><?= formatDateTime($user['last_login'] ?? $user['created_at']) ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Account Status</label>
                            <div>
                                <span class="badge bg-success">Active</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog me-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="auth/change-password.php" class="btn btn-outline-primary">
                                <i class="fas fa-key me-2"></i>
                                Change Password
                            </a>
                            <a href="settings.php" class="btn btn-outline-secondary">
                                <i class="fas fa-cog me-2"></i>
                                Account Settings
                            </a>
                            <a href="reports.php" class="btn btn-outline-info">
                                <i class="fas fa-chart-bar me-2"></i>
                                View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value.length <= 3) {
                    value = `(${value}`;
                } else if (value.length <= 6) {
                    value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
                } else {
                    value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 10)}`;
                }
            }
            e.target.value = value;
        });

        // Form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            
            if (!username || !email) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html> 