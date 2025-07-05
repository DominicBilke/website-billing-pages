<?php
require_once __DIR__ . '/../../inc/config.php';

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
    'company_name' => '',
    'contact_name' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'state' => '',
    'postal_code' => '',
    'country' => '',
    'website' => '',
    'notes' => '',
    'status' => 'active'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $formData = [
            'company_name' => sanitizeInput($_POST['company_name'] ?? ''),
            'contact_name' => sanitizeInput($_POST['contact_name'] ?? ''),
            'email' => sanitizeInput($_POST['email'] ?? ''),
            'phone' => sanitizeInput($_POST['phone'] ?? ''),
            'address' => sanitizeInput($_POST['address'] ?? ''),
            'city' => sanitizeInput($_POST['city'] ?? ''),
            'state' => sanitizeInput($_POST['state'] ?? ''),
            'postal_code' => sanitizeInput($_POST['postal_code'] ?? ''),
            'country' => sanitizeInput($_POST['country'] ?? ''),
            'website' => sanitizeInput($_POST['website'] ?? ''),
            'notes' => sanitizeInput($_POST['notes'] ?? ''),
            'status' => sanitizeInput($_POST['status'] ?? 'active')
        ];

        // Validation
        if (empty($formData['company_name'])) {
            $errors[] = 'Company name is required.';
        }

        if (empty($formData['contact_name'])) {
            $errors[] = 'Contact name is required.';
        }

        if (empty($formData['email'])) {
            $errors[] = 'Email address is required.';
        } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (!empty($formData['phone']) && !preg_match('/^[\+]?[1-9][\d]{0,15}$/', preg_replace('/[^0-9+]/', '', $formData['phone']))) {
            $errors[] = 'Please enter a valid phone number.';
        }

        if (!empty($formData['website']) && !filter_var($formData['website'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Please enter a valid website URL.';
        }

        // Check if email already exists for this user
        $db = Database::getInstance();
        $existingClient = $db->fetch(
            "SELECT id FROM clients WHERE email = ? AND user_id = ?", 
            [$formData['email'], $user['id']]
        );
        
        if ($existingClient) {
            $errors[] = 'A client with this email address already exists.';
        }

        // If no errors, create the client
        if (empty($errors)) {
            $clientData = array_merge($formData, [
                'user_id' => $user['id'],
                'client_id' => generateClientId(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $clientId = $db->insert('clients', $clientData);
            
            if ($clientId) {
                logActivity('client_created', 'Client created: ' . $formData['company_name'], $user['id']);
                setFlashMessage('success', 'Client created successfully!');
                redirect('clients.php');
            } else {
                $errors[] = 'Failed to create client. Please try again.';
            }
        }
    }
}

$pageTitle = 'Add Client - Billing Pages';
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
                        <a class="nav-link" href="../dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../invoices.php">
                            <i class="fas fa-file-invoice me-1"></i>
                            Invoices
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="../clients.php">
                            <i class="fas fa-users me-1"></i>
                            Clients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../reports.php">
                            <i class="fas fa-chart-bar me-1"></i>
                            Reports
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?= h($user['first_name'] ?: $user['username']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../profile.php">
                                <i class="fas fa-user me-2"></i>Profile
                            </a></li>
                            <li><a class="dropdown-item" href="../settings.php">
                                <i class="fas fa-cog me-2"></i>Settings
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../auth/logout.php">
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
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="../clients.php">Clients</a></li>
                <li class="breadcrumb-item active">Add Client</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Add New Client</h1>
                <p class="text-muted mb-0">Create a new client record</p>
            </div>
            <a href="../clients.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Clients
            </a>
        </div>

        <!-- Client Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-plus me-2"></i>
                            Client Information
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

                        <!-- Client Form -->
                        <form method="POST" action="" id="clientForm">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            
                            <!-- Company Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-building me-2"></i>
                                        Company Information
                                    </h6>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="company_name" class="form-label">
                                        Company Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="company_name" 
                                           name="company_name" 
                                           value="<?= h($formData['company_name']) ?>"
                                           required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" 
                                           class="form-control" 
                                           id="website" 
                                           name="website" 
                                           value="<?= h($formData['website']) ?>"
                                           placeholder="https://example.com">
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-address-card me-2"></i>
                                        Contact Information
                                    </h6>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="contact_name" class="form-label">
                                        Contact Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="contact_name" 
                                           name="contact_name" 
                                           value="<?= h($formData['contact_name']) ?>"
                                           required>
                                </div>

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

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="phone" 
                                           name="phone" 
                                           value="<?= h($formData['phone']) ?>"
                                           placeholder="+1 (555) 123-4567">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" <?= $formData['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= $formData['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        Address Information
                                    </h6>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="address" class="form-label">Street Address</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="address" 
                                           name="address" 
                                           value="<?= h($formData['address']) ?>"
                                           placeholder="123 Main Street">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="city" 
                                           name="city" 
                                           value="<?= h($formData['city']) ?>">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="state" class="form-label">State/Province</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="state" 
                                           name="state" 
                                           value="<?= h($formData['state']) ?>">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="postal_code" class="form-label">Postal Code</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="postal_code" 
                                           name="postal_code" 
                                           value="<?= h($formData['postal_code']) ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <select class="form-select" id="country" name="country">
                                        <option value="">Select Country</option>
                                        <option value="US" <?= $formData['country'] === 'US' ? 'selected' : '' ?>>United States</option>
                                        <option value="CA" <?= $formData['country'] === 'CA' ? 'selected' : '' ?>>Canada</option>
                                        <option value="GB" <?= $formData['country'] === 'GB' ? 'selected' : '' ?>>United Kingdom</option>
                                        <option value="AU" <?= $formData['country'] === 'AU' ? 'selected' : '' ?>>Australia</option>
                                        <option value="DE" <?= $formData['country'] === 'DE' ? 'selected' : '' ?>>Germany</option>
                                        <option value="FR" <?= $formData['country'] === 'FR' ? 'selected' : '' ?>>France</option>
                                        <option value="Other" <?= $formData['country'] === 'Other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-sticky-note me-2"></i>
                                        Additional Information
                                    </h6>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" 
                                              id="notes" 
                                              name="notes" 
                                              rows="4"
                                              placeholder="Any additional notes about this client..."><?= h($formData['notes']) ?></textarea>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between">
                                <a href="../clients.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Create Client
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.getElementById('clientForm').addEventListener('submit', function(e) {
            const requiredFields = ['company_name', 'contact_name', 'email'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const element = document.getElementById(field);
                if (!element.value.trim()) {
                    element.classList.add('is-invalid');
                    isValid = false;
                } else {
                    element.classList.remove('is-invalid');
                }
            });
            
            // Email validation
            const email = document.getElementById('email').value;
            if (email && !isValidEmail(email)) {
                document.getElementById('email').classList.add('is-invalid');
                isValid = false;
            }
            
            // Website validation
            const website = document.getElementById('website').value;
            if (website && !isValidUrl(website)) {
                document.getElementById('website').classList.add('is-invalid');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields correctly.');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
            submitBtn.disabled = true;
        });

        // Email validation function
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // URL validation function
        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }

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

        // Auto-focus company name field
        document.getElementById('company_name').focus();
    </script>
</body>
</html> 