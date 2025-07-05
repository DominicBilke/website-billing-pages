<?php
require_once __DIR__ . '/../../inc/config.php';

// Require authentication
requireAuth();

// Get user data
$user = getUserData();
if (!$user) {
    redirect('auth/login.php');
}

$db = Database::getInstance();

// Get clients for dropdown
$clients = $db->fetchAll(
    "SELECT id, company_name, contact_name, email FROM clients WHERE user_id = ? AND status = 'active' ORDER BY company_name",
    [$user['id']]
);

$errors = [];
$formData = [
    'client_id' => '',
    'invoice_number' => '',
    'issue_date' => date('Y-m-d'),
    'due_date' => date('Y-m-d', strtotime('+30 days')),
    'currency' => 'USD',
    'tax_rate' => 0,
    'notes' => '',
    'terms' => '',
    'status' => 'draft'
];

$lineItems = [
    [
        'description' => '',
        'quantity' => 1,
        'unit_price' => 0,
        'tax_rate' => 0
    ]
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $formData = [
            'client_id' => (int) ($_POST['client_id'] ?? 0),
            'invoice_number' => sanitizeInput($_POST['invoice_number'] ?? ''),
            'issue_date' => sanitizeInput($_POST['issue_date'] ?? ''),
            'due_date' => sanitizeInput($_POST['due_date'] ?? ''),
            'currency' => sanitizeInput($_POST['currency'] ?? 'USD'),
            'tax_rate' => (float) ($_POST['tax_rate'] ?? 0),
            'notes' => sanitizeInput($_POST['notes'] ?? ''),
            'terms' => sanitizeInput($_POST['terms'] ?? ''),
            'status' => sanitizeInput($_POST['status'] ?? 'draft')
        ];

        // Process line items
        $lineItems = [];
        $itemCount = count($_POST['item_description'] ?? []);
        
        for ($i = 0; $i < $itemCount; $i++) {
            $description = sanitizeInput($_POST['item_description'][$i] ?? '');
            $quantity = (float) ($_POST['item_quantity'][$i] ?? 1);
            $unitPrice = (float) ($_POST['item_unit_price'][$i] ?? 0);
            $taxRate = (float) ($_POST['item_tax_rate'][$i] ?? 0);
            
            if (!empty($description)) {
                $lineItems[] = [
                    'description' => $description,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_rate' => $taxRate
                ];
            }
        }

        // Validation
        if (empty($formData['client_id'])) {
            $errors[] = 'Please select a client.';
        }

        if (empty($formData['invoice_number'])) {
            $errors[] = 'Invoice number is required.';
        }

        if (empty($formData['issue_date'])) {
            $errors[] = 'Issue date is required.';
        }

        if (empty($formData['due_date'])) {
            $errors[] = 'Due date is required.';
        }

        if (empty($lineItems)) {
            $errors[] = 'At least one line item is required.';
        }

        // Check if invoice number already exists
        $existingInvoice = $db->fetch(
            "SELECT id FROM invoices WHERE invoice_number = ? AND user_id = ?", 
            [$formData['invoice_number'], $user['id']]
        );
        
        if ($existingInvoice) {
            $errors[] = 'An invoice with this number already exists.';
        }

        // If no errors, create the invoice
        if (empty($errors)) {
            try {
                $db->beginTransaction();

                // Calculate totals
                $subtotal = 0;
                $totalTax = 0;
                
                foreach ($lineItems as $item) {
                    $lineTotal = $item['quantity'] * $item['unit_price'];
                    $lineTax = $lineTotal * ($item['tax_rate'] / 100);
                    $subtotal += $lineTotal;
                    $totalTax += $lineTax;
                }

                $invoiceTax = $subtotal * ($formData['tax_rate'] / 100);
                $totalAmount = $subtotal + $totalTax + $invoiceTax;

                // Create invoice
                $invoiceData = array_merge($formData, [
                    'user_id' => $user['id'],
                    'subtotal' => $subtotal,
                    'tax_amount' => $totalTax + $invoiceTax,
                    'total_amount' => $totalAmount,
                    'paid_amount' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                $invoiceId = $db->insert('invoices', $invoiceData);
                
                if ($invoiceId) {
                    // Create line items
                    foreach ($lineItems as $item) {
                        $lineTotal = $item['quantity'] * $item['unit_price'];
                        $lineTax = $lineTotal * ($item['tax_rate'] / 100);
                        
                        $itemData = [
                            'invoice_id' => $invoiceId,
                            'description' => $item['description'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'tax_rate' => $item['tax_rate'],
                            'line_total' => $lineTotal,
                            'tax_amount' => $lineTax
                        ];
                        
                        $db->insert('invoice_items', $itemData);
                    }

                    $db->commit();
                    
                    logActivity('invoice_created', 'Invoice created: ' . $formData['invoice_number'], $user['id']);
                    setFlashMessage('success', 'Invoice created successfully!');
                    redirect('invoices.php');
                } else {
                    throw new Exception('Failed to create invoice');
                }
            } catch (Exception $e) {
                $db->rollback();
                $errors[] = 'Failed to create invoice. Please try again.';
            }
        }
    }
}

// Generate invoice number if not provided
if (empty($formData['invoice_number'])) {
    $lastInvoice = $db->fetch(
        "SELECT invoice_number FROM invoices WHERE user_id = ? ORDER BY id DESC LIMIT 1",
        [$user['id']]
    );
    
    if ($lastInvoice) {
        $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastInvoice['invoice_number']);
        $formData['invoice_number'] = 'INV-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    } else {
        $formData['invoice_number'] = 'INV-000001';
    }
}

$pageTitle = 'Create Invoice - Billing Pages';
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
                        <a class="nav-link active" href="../invoices.php">
                            <i class="fas fa-file-invoice me-1"></i>
                            Invoices
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../clients.php">
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
                <li class="breadcrumb-item"><a href="../invoices.php">Invoices</a></li>
                <li class="breadcrumb-item active">Create Invoice</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Create New Invoice</h1>
                <p class="text-muted mb-0">Generate a new invoice for your client</p>
            </div>
            <a href="../invoices.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Invoices
            </a>
        </div>

        <!-- Invoice Form -->
        <form method="POST" action="" id="invoiceForm">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            
            <div class="row">
                <!-- Main Form -->
                <div class="col-lg-8">
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

                    <!-- Invoice Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Invoice Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="client_id" class="form-label">
                                        Client <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="client_id" name="client_id" required>
                                        <option value="">Select Client</option>
                                        <?php foreach ($clients as $client): ?>
                                            <option value="<?= $client['id'] ?>" <?= $formData['client_id'] == $client['id'] ? 'selected' : '' ?>>
                                                <?= h($client['company_name']) ?> - <?= h($client['contact_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="invoice_number" class="form-label">
                                        Invoice Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="invoice_number" 
                                           name="invoice_number" 
                                           value="<?= h($formData['invoice_number']) ?>"
                                           required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="issue_date" class="form-label">
                                        Issue Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="issue_date" 
                                           name="issue_date" 
                                           value="<?= h($formData['issue_date']) ?>"
                                           required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="due_date" class="form-label">
                                        Due Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="due_date" 
                                           name="due_date" 
                                           value="<?= h($formData['due_date']) ?>"
                                           required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="currency" class="form-label">Currency</label>
                                    <select class="form-select" id="currency" name="currency">
                                        <option value="USD" <?= $formData['currency'] === 'USD' ? 'selected' : '' ?>>USD ($)</option>
                                        <option value="EUR" <?= $formData['currency'] === 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                                        <option value="GBP" <?= $formData['currency'] === 'GBP' ? 'selected' : '' ?>>GBP (£)</option>
                                        <option value="CAD" <?= $formData['currency'] === 'CAD' ? 'selected' : '' ?>>CAD (C$)</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="draft" <?= $formData['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                        <option value="sent" <?= $formData['status'] === 'sent' ? 'selected' : '' ?>>Sent</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Line Items -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>
                                Line Items
                            </h5>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addLineItem()">
                                <i class="fas fa-plus me-1"></i>
                                Add Item
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="lineItems">
                                <?php foreach ($lineItems as $index => $item): ?>
                                    <div class="line-item row mb-3" data-index="<?= $index ?>">
                                        <div class="col-md-4">
                                            <label class="form-label">Description</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="item_description[]" 
                                                   value="<?= h($item['description']) ?>"
                                                   placeholder="Item description">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Quantity</label>
                                            <input type="number" 
                                                   class="form-control quantity" 
                                                   name="item_quantity[]" 
                                                   value="<?= h($item['quantity']) ?>"
                                                   min="0" 
                                                   step="0.01"
                                                   onchange="calculateLineTotal(<?= $index ?>)">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Unit Price</label>
                                            <input type="number" 
                                                   class="form-control unit-price" 
                                                   name="item_unit_price[]" 
                                                   value="<?= h($item['unit_price']) ?>"
                                                   min="0" 
                                                   step="0.01"
                                                   onchange="calculateLineTotal(<?= $index ?>)">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Tax Rate (%)</label>
                                            <input type="number" 
                                                   class="form-control tax-rate" 
                                                   name="item_tax_rate[]" 
                                                   value="<?= h($item['tax_rate']) ?>"
                                                   min="0" 
                                                   max="100" 
                                                   step="0.01"
                                                   onchange="calculateLineTotal(<?= $index ?>)">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Total</label>
                                            <input type="text" 
                                                   class="form-control line-total" 
                                                   readonly 
                                                   value="<?= formatCurrency($item['quantity'] * $item['unit_price']) ?>">
                                            <?php if ($index > 0): ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger mt-1" 
                                                        onclick="removeLineItem(<?= $index ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Notes and Terms -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-sticky-note me-2"></i>
                                Additional Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" 
                                              id="notes" 
                                              name="notes" 
                                              rows="4"
                                              placeholder="Additional notes for the client..."><?= h($formData['notes']) ?></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="terms" class="form-label">Terms & Conditions</label>
                                    <textarea class="form-control" 
                                              id="terms" 
                                              name="terms" 
                                              rows="4"
                                              placeholder="Payment terms and conditions..."><?= h($formData['terms']) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Sidebar -->
                <div class="col-lg-4">
                    <div class="card sticky-top" style="top: 2rem;">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-calculator me-2"></i>
                                Invoice Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="tax_rate" class="form-label">Invoice Tax Rate (%)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="tax_rate" 
                                       name="tax_rate" 
                                       value="<?= h($formData['tax_rate']) ?>"
                                       min="0" 
                                       max="100" 
                                       step="0.01"
                                       onchange="calculateTotals()">
                            </div>

                            <div class="summary-item d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="subtotal">$0.00</span>
                            </div>
                            <div class="summary-item d-flex justify-content-between mb-2">
                                <span>Item Tax:</span>
                                <span id="itemTax">$0.00</span>
                            </div>
                            <div class="summary-item d-flex justify-content-between mb-2">
                                <span>Invoice Tax:</span>
                                <span id="invoiceTax">$0.00</span>
                            </div>
                            <hr>
                            <div class="summary-item d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span id="total">$0.00</span>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>
                                    Create Invoice
                                </button>
                                <button type="submit" name="save_draft" value="1" class="btn btn-outline-secondary">
                                    <i class="fas fa-edit me-2"></i>
                                    Save as Draft
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let lineItemIndex = <?= count($lineItems) ?>;

        // Add line item
        function addLineItem() {
            const container = document.getElementById('lineItems');
            const newItem = document.createElement('div');
            newItem.className = 'line-item row mb-3';
            newItem.setAttribute('data-index', lineItemIndex);
            
            newItem.innerHTML = `
                <div class="col-md-4">
                    <label class="form-label">Description</label>
                    <input type="text" 
                           class="form-control" 
                           name="item_description[]" 
                           placeholder="Item description">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" 
                           class="form-control quantity" 
                           name="item_quantity[]" 
                           value="1"
                           min="0" 
                           step="0.01"
                           onchange="calculateLineTotal(${lineItemIndex})">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Unit Price</label>
                    <input type="number" 
                           class="form-control unit-price" 
                           name="item_unit_price[]" 
                           value="0"
                           min="0" 
                           step="0.01"
                           onchange="calculateLineTotal(${lineItemIndex})">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tax Rate (%)</label>
                    <input type="number" 
                           class="form-control tax-rate" 
                           name="item_tax_rate[]" 
                           value="0"
                           min="0" 
                           max="100" 
                           step="0.01"
                           onchange="calculateLineTotal(${lineItemIndex})">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Total</label>
                    <input type="text" 
                           class="form-control line-total" 
                           readonly 
                           value="$0.00">
                    <button type="button" 
                            class="btn btn-sm btn-outline-danger mt-1" 
                            onclick="removeLineItem(${lineItemIndex})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(newItem);
            lineItemIndex++;
        }

        // Remove line item
        function removeLineItem(index) {
            const item = document.querySelector(`[data-index="${index}"]`);
            if (item) {
                item.remove();
                calculateTotals();
            }
        }

        // Calculate line total
        function calculateLineTotal(index) {
            const item = document.querySelector(`[data-index="${index}"]`);
            const quantity = parseFloat(item.querySelector('.quantity').value) || 0;
            const unitPrice = parseFloat(item.querySelector('.unit-price').value) || 0;
            const taxRate = parseFloat(item.querySelector('.tax-rate').value) || 0;
            
            const lineTotal = quantity * unitPrice;
            const lineTax = lineTotal * (taxRate / 100);
            
            item.querySelector('.line-total').value = formatCurrency(lineTotal + lineTax);
            calculateTotals();
        }

        // Calculate totals
        function calculateTotals() {
            let subtotal = 0;
            let itemTax = 0;
            
            document.querySelectorAll('.line-item').forEach(item => {
                const quantity = parseFloat(item.querySelector('.quantity').value) || 0;
                const unitPrice = parseFloat(item.querySelector('.unit-price').value) || 0;
                const taxRate = parseFloat(item.querySelector('.tax-rate').value) || 0;
                
                const lineTotal = quantity * unitPrice;
                const lineTax = lineTotal * (taxRate / 100);
                
                subtotal += lineTotal;
                itemTax += lineTax;
            });
            
            const invoiceTaxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
            const invoiceTax = subtotal * (invoiceTaxRate / 100);
            const total = subtotal + itemTax + invoiceTax;
            
            document.getElementById('subtotal').textContent = formatCurrency(subtotal);
            document.getElementById('itemTax').textContent = formatCurrency(itemTax);
            document.getElementById('invoiceTax').textContent = formatCurrency(invoiceTax);
            document.getElementById('total').textContent = formatCurrency(total);
        }

        // Format currency
        function formatCurrency(amount) {
            return '$' + parseFloat(amount).toFixed(2);
        }

        // Form validation
        document.getElementById('invoiceForm').addEventListener('submit', function(e) {
            const clientId = document.getElementById('client_id').value;
            const invoiceNumber = document.getElementById('invoice_number').value;
            const issueDate = document.getElementById('issue_date').value;
            const dueDate = document.getElementById('due_date').value;
            
            if (!clientId || !invoiceNumber || !issueDate || !dueDate) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            // Check if at least one line item has description
            let hasItems = false;
            document.querySelectorAll('input[name="item_description[]"]').forEach(input => {
                if (input.value.trim()) {
                    hasItems = true;
                }
            });
            
            if (!hasItems) {
                e.preventDefault();
                alert('Please add at least one line item.');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
            submitBtn.disabled = true;
        });

        // Auto-calculate on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotals();
        });

        // Due date validation
        document.getElementById('due_date').addEventListener('change', function() {
            const issueDate = document.getElementById('issue_date').value;
            const dueDate = this.value;
            
            if (issueDate && dueDate && issueDate > dueDate) {
                alert('Due date must be after issue date.');
                this.value = issueDate;
            }
        });
    </script>
</body>
</html> 