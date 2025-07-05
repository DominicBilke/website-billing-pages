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
$page = getCurrentPage();
$perPage = 20;
$offset = getOffset($page, $perPage);

// Build query
$whereConditions = ['user_id = ?'];
$params = [$user['id']];

if (!empty($search)) {
    $whereConditions[] = '(company_name LIKE ? OR contact_name LIKE ? OR email LIKE ?)';
    $searchParam = '%' . $search . '%';
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
}

if (!empty($status)) {
    $whereConditions[] = 'status = ?';
    $params[] = $status;
}

$whereClause = implode(' AND ', $whereConditions);

// Get total count for pagination
$totalClients = $db->count('clients', $whereClause, $params);

// Get clients
$clients = $db->fetchAll(
    "SELECT * FROM clients WHERE {$whereClause} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}",
    $params
);

// Handle client deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_client'])) {
    $clientId = (int) $_POST['client_id'];
    
    // Verify ownership
    $client = $db->fetch("SELECT * FROM clients WHERE id = ? AND user_id = ?", [$clientId, $user['id']]);
    
    if ($client) {
        // Check if client has invoices
        $invoiceCount = $db->count('invoices', 'client_id = ?', [$clientId]);
        
        if ($invoiceCount > 0) {
            setFlashMessage('error', 'Cannot delete client with existing invoices. Please delete or reassign invoices first.');
        } else {
            $db->delete('clients', 'id = ?', [$clientId]);
            logActivity('client_deleted', 'Client deleted: ' . $client['company_name'], $user['id']);
            setFlashMessage('success', 'Client deleted successfully.');
            redirect('clients.php');
        }
    }
}

$pageTitle = 'Clients - Billing Pages';
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
                        <a class="nav-link active" href="clients.php">
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
                <h1 class="h3 mb-0">Clients</h1>
                <p class="text-muted mb-0">Manage your client relationships</p>
            </div>
            <a href="clients/create.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add Client
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               value="<?= h($search) ?>"
                               placeholder="Search by company name, contact, or email">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
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

        <!-- Clients Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>
                        Client List
                    </h5>
                    <span class="badge bg-primary"><?= number_format($totalClients) ?> clients</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($clients)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No clients found</h5>
                        <p class="text-muted mb-3">
                            <?php if (!empty($search) || !empty($status)): ?>
                                Try adjusting your search criteria.
                            <?php else: ?>
                                Get started by adding your first client.
                            <?php endif; ?>
                        </p>
                        <a href="clients/create.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Add Your First Client
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clients as $client): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-3">
                                                    <i class="fas fa-building fa-2x text-primary"></i>
                                                </div>
                                                <div>
                                                    <strong><?= h($client['company_name']) ?></strong>
                                                    <?php if ($client['client_id']): ?>
                                                        <br><small class="text-muted">ID: <?= h($client['client_id']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= h($client['contact_name']) ?></td>
                                        <td>
                                            <a href="mailto:<?= h($client['email']) ?>" class="text-decoration-none">
                                                <?= h($client['email']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if ($client['phone']): ?>
                                                <a href="tel:<?= h($client['phone']) ?>" class="text-decoration-none">
                                                    <?= h($client['phone']) ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $client['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                <?= ucfirst($client['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= formatDate($client['created_at']) ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="clients/view.php?id=<?= $client['id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="clients/edit.php?id=<?= $client['id'] ?>" 
                                                   class="btn btn-sm btn-outline-secondary" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Delete"
                                                        onclick="confirmDelete(<?= $client['id'] ?>, '<?= h($client['company_name']) ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalClients > $perPage): ?>
                        <div class="d-flex justify-content-center mt-4">
                            <?= generatePagination($totalClients, $perPage, $page, 'clients.php?' . http_build_query(array_filter(['search' => $search, 'status' => $status]))) ?>
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
                    <p>Are you sure you want to delete the client "<strong id="clientName"></strong>"?</p>
                    <p class="text-danger mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="client_id" id="clientId">
                        <button type="submit" name="delete_client" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>
                            Delete Client
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
        function confirmDelete(clientId, clientName) {
            document.getElementById('clientId').value = clientId;
            document.getElementById('clientName').textContent = clientName;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Auto-submit form on filter change
        document.getElementById('status').addEventListener('change', function() {
            this.form.submit();
        });

        // Clear search
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            if (searchInput.value) {
                const clearButton = document.createElement('button');
                clearButton.type = 'button';
                clearButton.className = 'btn btn-outline-secondary btn-sm ms-2';
                clearButton.innerHTML = '<i class="fas fa-times"></i>';
                clearButton.onclick = function() {
                    searchInput.value = '';
                    searchInput.form.submit();
                };
                searchInput.parentNode.appendChild(clearButton);
            }
        });
    </script>
</body>
</html> 