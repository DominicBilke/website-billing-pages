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

// Get date range for reports
$dateRange = sanitizeInput($_GET['range'] ?? '30');
$startDate = sanitizeInput($_GET['start_date'] ?? '');
$endDate = sanitizeInput($_GET['end_date'] ?? '');

// Calculate date range
if (empty($startDate) || empty($endDate)) {
    $endDate = date('Y-m-d');
    $startDate = date('Y-m-d', strtotime("-{$dateRange} days"));
}

// Get revenue statistics
$revenueStats = $db->fetch(
    "SELECT 
        COUNT(*) as total_invoices,
        SUM(total_amount) as total_revenue,
        SUM(paid_amount) as total_paid,
        AVG(total_amount) as avg_invoice_amount,
        COUNT(CASE WHEN status = 'paid' THEN 1 END) as paid_invoices,
        COUNT(CASE WHEN status = 'overdue' THEN 1 END) as overdue_invoices
     FROM invoices 
     WHERE user_id = ? AND issue_date BETWEEN ? AND ?",
    [$user['id'], $startDate, $endDate]
);

// Get monthly revenue data for chart
$monthlyRevenue = $db->fetchAll(
    "SELECT 
        DATE_FORMAT(issue_date, '%Y-%m') as month,
        SUM(total_amount) as revenue,
        SUM(paid_amount) as paid_revenue
     FROM invoices 
     WHERE user_id = ? AND issue_date BETWEEN ? AND ?
     GROUP BY DATE_FORMAT(issue_date, '%Y-%m')
     ORDER BY month DESC
     LIMIT 12",
    [$user['id'], $startDate, $endDate]
);

// Get top clients by revenue
$topClients = $db->fetchAll(
    "SELECT 
        c.company_name,
        c.contact_name,
        COUNT(i.id) as invoice_count,
        SUM(i.total_amount) as total_revenue,
        SUM(i.paid_amount) as paid_revenue
     FROM clients c
     LEFT JOIN invoices i ON c.id = i.client_id 
     WHERE c.user_id = ? AND (i.issue_date BETWEEN ? AND ? OR i.issue_date IS NULL)
     GROUP BY c.id
     ORDER BY total_revenue DESC
     LIMIT 10",
    [$user['id'], $startDate, $endDate]
);

// Get invoice status distribution
$statusDistribution = $db->fetchAll(
    "SELECT 
        status,
        COUNT(*) as count,
        SUM(total_amount) as total_amount
     FROM invoices 
     WHERE user_id = ? AND issue_date BETWEEN ? AND ?
     GROUP BY status",
    [$user['id'], $startDate, $endDate]
);

// Get recent activity
$recentActivity = $db->fetchAll(
    "SELECT 
        action_type,
        description,
        created_at,
        ip_address
     FROM activity_logs 
     WHERE user_id = ?
     ORDER BY created_at DESC
     LIMIT 20",
    [$user['id']]
);

$pageTitle = 'Reports - Billing Pages';
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
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        <a class="nav-link active" href="reports.php">
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
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Reports & Analytics</h1>
                <p class="text-muted mb-0">Track your business performance and insights</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="exportReport()">
                    <i class="fas fa-download me-2"></i>
                    Export Report
                </button>
                <button class="btn btn-outline-secondary" onclick="printReport()">
                    <i class="fas fa-print me-2"></i>
                    Print
                </button>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="range" class="form-label">Quick Range</label>
                        <select class="form-select" id="range" name="range" onchange="updateDateRange()">
                            <option value="7" <?= $dateRange === '7' ? 'selected' : '' ?>>Last 7 days</option>
                            <option value="30" <?= $dateRange === '30' ? 'selected' : '' ?>>Last 30 days</option>
                            <option value="90" <?= $dateRange === '90' ? 'selected' : '' ?>>Last 90 days</option>
                            <option value="365" <?= $dateRange === '365' ? 'selected' : '' ?>>Last year</option>
                            <option value="custom" <?= $dateRange === 'custom' ? 'selected' : '' ?>>Custom range</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" 
                               class="form-control" 
                               id="start_date" 
                               name="start_date" 
                               value="<?= h($startDate) ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" 
                               class="form-control" 
                               id="end_date" 
                               name="end_date" 
                               value="<?= h($endDate) ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-grid gap-2 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>
                                Apply Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Revenue Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?= formatCurrency($revenueStats['total_revenue'] ?? 0) ?></div>
                            <div class="stat-label">Total Revenue</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?= formatCurrency($revenueStats['total_paid'] ?? 0) ?></div>
                            <div class="stat-label">Total Paid</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-check-circle fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?= number_format($revenueStats['total_invoices'] ?? 0) ?></div>
                            <div class="stat-label">Total Invoices</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-file-invoice fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?= formatCurrency($revenueStats['avg_invoice_amount'] ?? 0) ?></div>
                            <div class="stat-label">Average Invoice</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chart-line fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <!-- Monthly Revenue Chart -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Monthly Revenue Trend
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Status Distribution Chart -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Invoice Status Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Clients and Recent Activity -->
        <div class="row g-4">
            <!-- Top Clients -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-trophy me-2"></i>
                            Top Clients by Revenue
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($topClients)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-2x text-muted mb-3"></i>
                                <p class="text-muted">No client data available for the selected period.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Invoices</th>
                                            <th>Revenue</th>
                                            <th>Paid</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($topClients as $client): ?>
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong><?= h($client['company_name']) ?></strong>
                                                        <br><small class="text-muted"><?= h($client['contact_name']) ?></small>
                                                    </div>
                                                </td>
                                                <td><?= number_format($client['invoice_count']) ?></td>
                                                <td><?= formatCurrency($client['total_revenue']) ?></td>
                                                <td><?= formatCurrency($client['paid_revenue']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>
                            Recent Activity
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentActivity)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-history fa-2x text-muted mb-3"></i>
                                <p class="text-muted">No recent activity found.</p>
                            </div>
                        <?php else: ?>
                            <div class="activity-timeline">
                                <?php foreach ($recentActivity as $activity): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-circle text-primary"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title"><?= h($activity['description']) ?></div>
                                            <div class="activity-meta">
                                                <small class="text-muted">
                                                    <?= formatDateTime($activity['created_at']) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column(array_reverse($monthlyRevenue), 'month')) ?>,
                datasets: [{
                    label: 'Total Revenue',
                    data: <?= json_encode(array_column(array_reverse($monthlyRevenue), 'revenue')) ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Paid Revenue',
                    data: <?= json_encode(array_column(array_reverse($monthlyRevenue), 'paid_revenue')) ?>,
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
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

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($statusDistribution, 'status')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($statusDistribution, 'count')) ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Date range functions
        function updateDateRange() {
            const range = document.getElementById('range').value;
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');
            
            if (range !== 'custom') {
                const end = new Date();
                const start = new Date();
                start.setDate(start.getDate() - parseInt(range));
                
                endDate.value = end.toISOString().split('T')[0];
                startDate.value = start.toISOString().split('T')[0];
            }
        }

        // Export report
        function exportReport() {
            const params = new URLSearchParams(window.location.search);
            window.open('reports/export.php?' + params.toString(), '_blank');
        }

        // Print report
        function printReport() {
            window.print();
        }

        // Auto-update date range on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateDateRange();
        });
    </script>
</body>
</html> 