<?php
require_once __DIR__ . '/../inc/config.php';

// Require authentication
requireAuth();

// Get user data
$user = getUserData();
if (!$user) {
    redirect('auth/login.php');
}

$db = Database::getInstance();

// Handle search and filtering
$search = sanitizeInput($_GET['search'] ?? '');
$status = sanitizeInput($_GET['status'] ?? '');
$dateFrom = sanitizeInput($_GET['date_from'] ?? '');
$dateTo = sanitizeInput($_GET['date_to'] ?? '');
$page = getCurrentPage();
$perPage = 20;
$offset = getOffset($page, $perPage);

// Build query
$whereConditions = ['i.user_id = ?'];
$params = [$user['id']];

if (!empty($search)) {
    $whereConditions[] = '(i.invoice_number LIKE ? OR c.company_name LIKE ? OR c.contact_name LIKE ?)';
    $searchParam = '%' . $search . '%';
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
}

if (!empty($status)) {
    $whereConditions[] = 'i.status = ?';
    $params[] = $status;
}

if (!empty($dateFrom)) {
    $whereConditions[] = 'i.issue_date >= ?';
    $params[] = $dateFrom;
}

if (!empty($dateTo)) {
    $whereConditions[] = 'i.issue_date <= ?';
    $params[] = $dateTo;
}

$whereClause = implode(' AND ', $whereConditions);

// Get total count for pagination
$totalInvoices = $db->count('invoices i', $whereClause, $params);

// Get invoices with client information
$invoices = $db->fetchAll(
    "SELECT i.*, c.company_name, c.contact_name, c.email as client_email 
     FROM invoices i 
     LEFT JOIN clients c ON i.client_id = c.id 
     WHERE {$whereClause} 
     ORDER BY i.created_at DESC 
     LIMIT {$perPage} OFFSET {$offset}",
    $params
);

// Handle invoice deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_invoice'])) {
    $invoiceId = (int) $_POST['invoice_id'];
    
    // Verify ownership
    $invoice = $db->fetch("SELECT * FROM invoices WHERE id = ? AND user_id = ?", [$invoiceId, $user['id']]);
    
    if ($invoice) {
        if ($invoice['status'] === 'paid') {
            setFlashMessage('error', 'Cannot delete a paid invoice.');
        } else {
            $db->delete('invoice_items', 'invoice_id = ?', [$invoiceId]);
            $db->delete('invoices', 'id = ?', [$invoiceId]);
            logActivity('invoice_deleted', 'Invoice deleted: ' . $invoice['invoice_number'], $user['id']);
            setFlashMessage('success', 'Invoice deleted successfully.');
            redirect('invoices.php');
        }
    }
}

// Get statistics
$stats = [
    'total' => $db->count('invoices', 'user_id = ?', [$user['id']]),
    'paid' => $db->count('invoices', 'user_id = ? AND status = "paid"', [$user['id']]),
    'pending' => $db->count('invoices', 'user_id = ? AND status IN ("sent", "draft")', [$user['id']]),
    'overdue' => $db->count('invoices', 'user_id = ? AND status = "overdue"', [$user['id']])
];

$pageTitle = 'Invoices - Billing Pages';
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
                        <a class="nav-link active" href="invoices.php">
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
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?= h($user['first_name'] ?: $user['username']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">
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
                <h1 class="h3 mb-0">Invoices</h1>
                <p class="text-muted mb-0">Manage your invoices and payments</p>
            </div>
            <a href="invoices/create.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Create Invoice
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?= number_format($stats['total']) ?></div>
                            <div class="stat-label">Total Invoices</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-file-invoice fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?= number_format($stats['paid']) ?></div>
                            <div class="stat-label">Paid</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?= number_format($stats['pending']) ?></div>
                            <div class="stat-label">Pending</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?= number_format($stats['overdue']) ?></div>
                            <div class="stat-label">Overdue</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               value="<?= h($search) ?>"
                               placeholder="Search by invoice number, client, or contact">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="sent" <?= $status === 'sent' ? 'selected' : '' ?>>Sent</option>
                            <option value="paid" <?= $status === 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="overdue" <?= $status === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                            <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" 
                               class="form-control" 
                               id="date_from" 
                               name="date_from" 
                               value="<?= h($dateFrom) ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" 
                               class="form-control" 
                               id="date_to" 
                               name="date_to" 
                               value="<?= h($dateTo) ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-grid gap-2 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice me-2"></i>
                        Invoice List
                    </h5>
                    <span class="badge bg-primary"><?= number_format($totalInvoices) ?> invoices</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($invoices)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No invoices found</h5>
                        <p class="text-muted mb-3">
                            <?php if (!empty($search) || !empty($status) || !empty($dateFrom) || !empty($dateTo)): ?>
                                Try adjusting your search criteria.
                            <?php else: ?>
                                Get started by creating your first invoice.
                            <?php endif; ?>
                        </p>
                        <a href="invoices/create.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Create Your First Invoice
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Client</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoices as $invoice): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-3">
                                                    <i class="fas fa-file-invoice fa-2x text-primary"></i>
                                                </div>
                                                <div>
                                                    <strong><?= h($invoice['invoice_number']) ?></strong>
                                                    <br><small class="text-muted"><?= formatCurrency($invoice['total_amount'], $invoice['currency']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= h($invoice['company_name']) ?></strong>
                                                <br><small class="text-muted"><?= h($invoice['contact_name']) ?></small>
                                            </div>
                                        </td>
                                        <td><?= formatDate($invoice['issue_date']) ?></td>
                                        <td>
                                            <?php 
                                            $dueDate = new DateTime($invoice['due_date']);
                                            $today = new DateTime();
                                            $isOverdue = $dueDate < $today && $invoice['status'] !== 'paid';
                                            ?>
                                            <span class="<?= $isOverdue ? 'text-danger' : '' ?>">
                                                <?= formatDate($invoice['due_date']) ?>
                                                <?php if ($isOverdue): ?>
                                                    <i class="fas fa-exclamation-triangle text-danger ms-1"></i>
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?= formatCurrency($invoice['total_amount'], $invoice['currency']) ?></strong>
                                            <?php if ($invoice['paid_amount'] > 0): ?>
                                                <br><small class="text-success">Paid: <?= formatCurrency($invoice['paid_amount'], $invoice['currency']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusColors = [
                                                'draft' => 'secondary',
                                                'sent' => 'warning',
                                                'paid' => 'success',
                                                'overdue' => 'danger',
                                                'cancelled' => 'dark'
                                            ];
                                            $statusColor = $statusColors[$invoice['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge badge-<?= $statusColor ?>">
                                                <?= ucfirst($invoice['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="invoices/view.php?id=<?= $invoice['id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="invoices/edit.php?id=<?= $invoice['id'] ?>" 
                                                   class="btn btn-sm btn-outline-secondary" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="invoices/pdf.php?id=<?= $invoice['id'] ?>" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="Download PDF"
                                                   target="_blank">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <?php if ($invoice['status'] !== 'paid'): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Delete"
                                                            onclick="confirmDelete(<?= $invoice['id'] ?>, '<?= h($invoice['invoice_number']) ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalInvoices > $perPage): ?>
                        <div class="d-flex justify-content-center mt-4">
                            <?= generatePagination($totalInvoices, $perPage, $page, 'invoices.php?' . http_build_query(array_filter(['search' => $search, 'status' => $status, 'date_from' => $dateFrom, 'date_to' => $dateTo]))) ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the invoice "<strong id="invoiceNumber"></strong>"?</p>
                    <p class="text-danger mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="invoice_id" id="invoiceId">
                        <button type="submit" name="delete_invoice" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>
                            Delete Invoice
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Delete confirmation
        function confirmDelete(invoiceId, invoiceNumber) {
            document.getElementById('invoiceId').value = invoiceId;
            document.getElementById('invoiceNumber').textContent = invoiceNumber;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Auto-submit form on filter change
        document.getElementById('status').addEventListener('change', function() {
            this.form.submit();
        });

        // Date range validation
        document.getElementById('date_to').addEventListener('change', function() {
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = this.value;
            
            if (dateFrom && dateTo && dateFrom > dateTo) {
                alert('End date must be after start date.');
                this.value = '';
            }
        });
    </script>
</body>
</html> 