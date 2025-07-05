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

// Get current settings
$db = Database::getInstance();
$settings = $db->fetchAll("SELECT * FROM user_settings WHERE user_id = ?", [$user['id']]);
$settingsMap = [];
foreach ($settings as $setting) {
    $settingsMap[$setting['setting_key']] = $setting['setting_value'];
}

$formData = [
    'email_notifications' => $settingsMap['email_notifications'] ?? '1',
    'invoice_reminders' => $settingsMap['invoice_reminders'] ?? '1',
    'payment_notifications' => $settingsMap['payment_notifications'] ?? '1',
    'default_invoice_terms' => $settingsMap['default_invoice_terms'] ?? '',
    'default_payment_terms' => $settingsMap['default_payment_terms'] ?? '30',
    'auto_number_invoices' => $settingsMap['auto_number_invoices'] ?? '1',
    'invoice_prefix' => $settingsMap['invoice_prefix'] ?? 'INV-',
    'tax_rate' => $settingsMap['tax_rate'] ?? '0',
    'currency_symbol' => $settingsMap['currency_symbol'] ?? '$',
    'date_format' => $settingsMap['date_format'] ?? 'Y-m-d',
    'time_format' => $settingsMap['time_format'] ?? 'H:i',
    'language' => $settingsMap['language'] ?? 'en',
    'theme' => $settingsMap['theme'] ?? 'light'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $formData = [
            'email_notifications' => sanitizeInput($_POST['email_notifications'] ?? '0'),
            'invoice_reminders' => sanitizeInput($_POST['invoice_reminders'] ?? '0'),
            'payment_notifications' => sanitizeInput($_POST['payment_notifications'] ?? '0'),
            'default_invoice_terms' => sanitizeInput($_POST['default_invoice_terms'] ?? ''),
            'default_payment_terms' => (int) ($_POST['default_payment_terms'] ?? 30),
            'auto_number_invoices' => sanitizeInput($_POST['auto_number_invoices'] ?? '0'),
            'invoice_prefix' => sanitizeInput($_POST['invoice_prefix'] ?? 'INV-'),
            'tax_rate' => (float) ($_POST['tax_rate'] ?? 0),
            'currency_symbol' => sanitizeInput($_POST['currency_symbol'] ?? '$'),
            'date_format' => sanitizeInput($_POST['date_format'] ?? 'Y-m-d'),
            'time_format' => sanitizeInput($_POST['time_format'] ?? 'H:i'),
            'language' => sanitizeInput($_POST['language'] ?? 'en'),
            'theme' => sanitizeInput($_POST['theme'] ?? 'light')
        ];

        // Validation
        if ($formData['default_payment_terms'] < 0 || $formData['default_payment_terms'] > 365) {
            $errors[] = 'Default payment terms must be between 0 and 365 days.';
        }

        if ($formData['tax_rate'] < 0 || $formData['tax_rate'] > 100) {
            $errors[] = 'Tax rate must be between 0 and 100 percent.';
        }

        // If no errors, update settings
        if (empty($errors)) {
            try {
                $db->beginTransaction();
                
                foreach ($formData as $key => $value) {
                    $existingSetting = $db->fetch(
                        "SELECT id FROM user_settings WHERE user_id = ? AND setting_key = ?",
                        [$user['id'], $key]
                    );
                    
                    if ($existingSetting) {
                        $db->update('user_settings', 
                            ['setting_value' => $value, 'updated_at' => date('Y-m-d H:i:s')], 
                            'user_id = ? AND setting_key = ?', 
                            [$user['id'], $key]
                        );
                    } else {
                        $db->insert('user_settings', [
                            'user_id' => $user['id'],
                            'setting_key' => $key,
                            'setting_value' => $value,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
                
                $db->commit();
                $success = true;
                logActivity('settings_updated', 'Settings updated', $user['id']);
                setFlashMessage('success', 'Settings updated successfully!');
                
            } catch (Exception $e) {
                $db->rollback();
                $errors[] = 'Failed to update settings. Please try again.';
            }
        }
    }
}

$pageTitle = 'Settings - Billing Pages';
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
                            <li><a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user me-2"></i>Profile
                            </a></li>
                            <li><a class="dropdown-item active" href="settings.php">
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
                <h1 class="h3 mb-0">Account Settings</h1>
                <p class="text-muted mb-0">Customize your billing experience</p>
            </div>
        </div>

        <form method="POST" action="" id="settingsForm">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            
            <div class="row">
                <!-- Notification Settings -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-bell me-2"></i>
                                Notification Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="email_notifications" 
                                           name="email_notifications" 
                                           value="1"
                                           <?= $formData['email_notifications'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="email_notifications">
                                        Email Notifications
                                    </label>
                                    <div class="form-text">Receive email notifications for important events</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="invoice_reminders" 
                                           name="invoice_reminders" 
                                           value="1"
                                           <?= $formData['invoice_reminders'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="invoice_reminders">
                                        Invoice Reminders
                                    </label>
                                    <div class="form-text">Get reminded about overdue invoices</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="payment_notifications" 
                                           name="payment_notifications" 
                                           value="1"
                                           <?= $formData['payment_notifications'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="payment_notifications">
                                        Payment Notifications
                                    </label>
                                    <div class="form-text">Notify when payments are received</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Settings -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-invoice me-2"></i>
                                Invoice Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="auto_number_invoices" 
                                           name="auto_number_invoices" 
                                           value="1"
                                           <?= $formData['auto_number_invoices'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="auto_number_invoices">
                                        Auto-number Invoices
                                    </label>
                                    <div class="form-text">Automatically generate invoice numbers</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="invoice_prefix" class="form-label">Invoice Number Prefix</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="invoice_prefix" 
                                       name="invoice_prefix" 
                                       value="<?= h($formData['invoice_prefix']) ?>"
                                       placeholder="INV-">
                            </div>
                            
                            <div class="mb-3">
                                <label for="default_payment_terms" class="form-label">Default Payment Terms (days)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="default_payment_terms" 
                                       name="default_payment_terms" 
                                       value="<?= h($formData['default_payment_terms']) ?>"
                                       min="0" 
                                       max="365">
                            </div>
                            
                            <div class="mb-3">
                                <label for="default_invoice_terms" class="form-label">Default Invoice Terms</label>
                                <textarea class="form-control" 
                                          id="default_invoice_terms" 
                                          name="default_invoice_terms" 
                                          rows="3"
                                          placeholder="Enter default terms and conditions..."><?= h($formData['default_invoice_terms']) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Settings -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-dollar-sign me-2"></i>
                                Financial Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="tax_rate" class="form-label">Default Tax Rate (%)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="tax_rate" 
                                       name="tax_rate" 
                                       value="<?= h($formData['tax_rate']) ?>"
                                       min="0" 
                                       max="100" 
                                       step="0.01">
                            </div>
                            
                            <div class="mb-3">
                                <label for="currency_symbol" class="form-label">Currency Symbol</label>
                                <select class="form-select" id="currency_symbol" name="currency_symbol">
                                    <option value="$" <?= $formData['currency_symbol'] === '$' ? 'selected' : '' ?>>$ (USD)</option>
                                    <option value="€" <?= $formData['currency_symbol'] === '€' ? 'selected' : '' ?>>€ (EUR)</option>
                                    <option value="£" <?= $formData['currency_symbol'] === '£' ? 'selected' : '' ?>>£ (GBP)</option>
                                    <option value="C$" <?= $formData['currency_symbol'] === 'C$' ? 'selected' : '' ?>>C$ (CAD)</option>
                                    <option value="A$" <?= $formData['currency_symbol'] === 'A$' ? 'selected' : '' ?>>A$ (AUD)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Display Settings -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-palette me-2"></i>
                                Display Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="date_format" class="form-label">Date Format</label>
                                <select class="form-select" id="date_format" name="date_format">
                                    <option value="Y-m-d" <?= $formData['date_format'] === 'Y-m-d' ? 'selected' : '' ?>>YYYY-MM-DD</option>
                                    <option value="m/d/Y" <?= $formData['date_format'] === 'm/d/Y' ? 'selected' : '' ?>>MM/DD/YYYY</option>
                                    <option value="d/m/Y" <?= $formData['date_format'] === 'd/m/Y' ? 'selected' : '' ?>>DD/MM/YYYY</option>
                                    <option value="M j, Y" <?= $formData['date_format'] === 'M j, Y' ? 'selected' : '' ?>>Jan 1, 2024</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="time_format" class="form-label">Time Format</label>
                                <select class="form-select" id="time_format" name="time_format">
                                    <option value="H:i" <?= $formData['time_format'] === 'H:i' ? 'selected' : '' ?>>24-hour (13:30)</option>
                                    <option value="g:i A" <?= $formData['time_format'] === 'g:i A' ? 'selected' : '' ?>>12-hour (1:30 PM)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="language" class="form-label">Language</label>
                                <select class="form-select" id="language" name="language">
                                    <option value="en" <?= $formData['language'] === 'en' ? 'selected' : '' ?>>English</option>
                                    <option value="es" <?= $formData['language'] === 'es' ? 'selected' : '' ?>>Español</option>
                                    <option value="fr" <?= $formData['language'] === 'fr' ? 'selected' : '' ?>>Français</option>
                                    <option value="de" <?= $formData['language'] === 'de' ? 'selected' : '' ?>>Deutsch</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="theme" class="form-label">Theme</label>
                                <select class="form-select" id="theme" name="theme">
                                    <option value="light" <?= $formData['theme'] === 'light' ? 'selected' : '' ?>>Light</option>
                                    <option value="dark" <?= $formData['theme'] === 'dark' ? 'selected' : '' ?>>Dark</option>
                                    <option value="auto" <?= $formData['theme'] === 'auto' ? 'selected' : '' ?>>Auto</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            const paymentTerms = document.getElementById('default_payment_terms').value;
            const taxRate = document.getElementById('tax_rate').value;
            
            if (paymentTerms < 0 || paymentTerms > 365) {
                e.preventDefault();
                alert('Default payment terms must be between 0 and 365 days.');
                return false;
            }
            
            if (taxRate < 0 || taxRate > 100) {
                e.preventDefault();
                alert('Tax rate must be between 0 and 100 percent.');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
            submitBtn.disabled = true;
        });

        // Theme preview
        document.getElementById('theme').addEventListener('change', function() {
            const theme = this.value;
            // This would typically update the theme immediately
            console.log('Theme changed to:', theme);
        });
    </script>
</body>
</html> 