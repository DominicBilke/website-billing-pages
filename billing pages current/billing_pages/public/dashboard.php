<?php
require_once __DIR__ . '/../inc/config.php';

// Require authentication
requireAuth();

// Get user data
$user = getUserData();
if (!$user) {
    redirect('auth/login.php');
}

// Get dashboard statistics
$db = Database::getInstance();

// Total invoices
$totalInvoices = $db->count('invoices', 'user_id = ?', [$user['id']]);

// Paid invoices
$paidInvoices = $db->count('invoices', 'user_id = ? AND status = "paid"', [$user['id']]);

// Pending invoices
$pendingInvoices = $db->count('invoices', 'user_id = ? AND status IN ("sent", "draft")', [$user['id']]);

// Total revenue
$totalRevenue = $db->fetch(
    "SELECT COALESCE(SUM(total_amount), 0) as total FROM invoices WHERE user_id = ? AND status = 'paid'",
    [$user['id']]
)['total'];

// Recent invoices
$recentInvoices = $db->fetchAll(
    "SELECT i.*, c.company_name, c.contact_name 
     FROM invoices i 
     JOIN clients c ON i.client_id = c.id 
     WHERE i.user_id = ? 
     ORDER BY i.created_at DESC 
     LIMIT 5",
    [$user['id']]
);

// Recent clients
$recentClients = $db->fetchAll(
    "SELECT * FROM clients WHERE user_id = ? ORDER BY created_at DESC LIMIT 5",
    [$user['id']]
);

// Monthly revenue chart data
$monthlyRevenue = $db->fetchAll(
    "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
            SUM(total_amount) as revenue 
     FROM invoices 
     WHERE user_id = ? AND status = 'paid' 
     AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
     GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
     ORDER BY month DESC",
    [$user['id']]
);

$pageTitle = 'Dashboard - Billing Pages';
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
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
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
                        <a class="nav-link active" href="dashboard.php">
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

        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-0">
                            Welcome back, <?= h($user['first_name'] ?: $user['username']) ?>!
                        </h4>
                        <p class="text-muted mb-0">Here's what's happening with your business today.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?= number_format($totalInvoices) ?></div>
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
                            <div class="stat-number"><?= number_format($paidInvoices) ?></div>
                            <div class="stat-label">Paid Invoices</div>
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
                            <div class="stat-number"><?= number_format($pendingInvoices) ?></div>
                            <div class="stat-label">Pending Invoices</div>
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
                            <div class="stat-number">$<?= number_format($totalRevenue, 2) ?></div>
                            <div class="stat-label">Total Revenue</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Recent Activity -->
        <div class="row g-4">
            <!-- Revenue Chart -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Monthly Revenue
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="invoices/create.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Create Invoice
                            </a>
                            <a href="clients/create.php" class="btn btn-outline-primary">
                                <i class="fas fa-user-plus me-2"></i>
                                Add Client
                            </a>
                            <a href="reports.php" class="btn btn-outline-primary">
                                <i class="fas fa-chart-bar me-2"></i>
                                View Reports
                            </a>
                            <a href="settings.php" class="btn btn-outline-secondary">
                                <i class="fas fa-cog me-2"></i>
                                Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Invoices and Clients -->
        <div class="row g-4 mt-4">
            <!-- Recent Invoices -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-invoice me-2"></i>
                            Recent Invoices
                        </h5>
                        <a href="invoices.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentInvoices)): ?>
                            <p class="text-muted text-center py-3">No invoices yet. <a href="invoices/create.php">Create your first invoice</a></p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Invoice</th>
                                            <th>Client</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentInvoices as $invoice): ?>
                                            <tr>
                                                <td>
                                                    <a href="invoices/view.php?id=<?= $invoice['id'] ?>" class="text-decoration-none">
                                                        <?= h($invoice['invoice_number']) ?>
                                                    </a>
                                                </td>
                                                <td><?= h($invoice['company_name']) ?></td>
                                                <td>$<?= number_format($invoice['total_amount'], 2) ?></td>
                                                <td>
                                                    <span class="badge badge-<?= $invoice['status'] === 'paid' ? 'success' : ($invoice['status'] === 'sent' ? 'warning' : 'secondary') ?>">
                                                        <?= ucfirst($invoice['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= formatDate($invoice['created_at']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Clients -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users me-2"></i>
                            Recent Clients
                        </h5>
                        <a href="clients.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentClients)): ?>
                            <p class="text-muted text-center py-3">No clients yet. <a href="clients/create.php">Add your first client</a></p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Contact</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Added</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentClients as $client): ?>
                                            <tr>
                                                <td>
                                                    <a href="clients/view.php?id=<?= $client['id'] ?>" class="text-decoration-none">
                                                        <?= h($client['company_name']) ?>
                                                    </a>
                                                </td>
                                                <td><?= h($client['contact_name']) ?></td>
                                                <td><?= h($client['email']) ?></td>
                                                <td>
                                                    <span class="badge badge-<?= $client['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                        <?= ucfirst($client['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= formatDate($client['created_at']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueData = <?= json_encode($monthlyRevenue) ?>;
        
        const labels = revenueData.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        }).reverse();
        
        const data = revenueData.map(item => parseFloat(item.revenue)).reverse();
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue',
                    data: data,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 