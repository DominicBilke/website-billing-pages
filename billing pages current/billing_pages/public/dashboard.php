<?php
require_once __DIR__ . '/../inc/header.php';
require_once __DIR__ . '/../inc/db.php';

// Get statistics
$stats = [
    'total_invoices' => $pdo->query("SELECT COUNT(*) FROM invoices")->fetchColumn(),
    'total_clients' => $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn(),
    'total_revenue' => $pdo->query("SELECT SUM(total_amount) FROM invoices")->fetchColumn() ?: 0,
    'pending_payments' => $pdo->query("SELECT COUNT(*) FROM invoices")->fetchColumn()
];

// Get recent invoices
$stmt = $pdo->query("
    SELECT i.*, c.name as client_name 
    FROM invoices i 
    JOIN clients c ON i.client_id = c.id 
    ORDER BY i.created_at DESC 
    LIMIT 5
");
$recent_invoices = $stmt->fetchAll();

// Get upcoming payments
$stmt = $pdo->query("
    SELECT i.*, c.name as client_name 
    FROM invoices i 
    JOIN clients c ON i.client_id = c.id 
    WHERE i.due_date <= DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)
    ORDER BY i.due_date ASC 
    LIMIT 5
");
$upcoming_payments = $stmt->fetchAll();
?>

<div class="container">
    <div class="page-header">
        <h1>Dashboard</h1>
        <div class="header-actions">
            <a href="invoices/create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Invoice
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo number_format($stats['total_invoices']); ?></h3>
                <p>Total Invoices</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo number_format($stats['total_clients']); ?></h3>
                <p>Total Clients</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-euro-sign"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo number_format($stats['total_revenue'], 2); ?> </h3>
                <p>Total Revenue</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo number_format($stats['pending_payments']); ?></h3>
                <p>Pending Payments</p>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Recent Invoices -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Recent Invoices</h2>
                    <a href="invoices/" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_invoices)): ?>
                        <p class="text-muted">No recent invoices found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Client</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_invoices as $invoice): ?>
                                        <tr>
                                            <td>
                                                <a href="invoices/view.php?id=<?php echo $invoice['id']; ?>">
                                                    <?php echo htmlspecialchars($invoice['invoice_number']); ?>
                                                </a>
                                            </td>
                                            <td><?php echo htmlspecialchars($invoice['client_name']); ?></td>
                                            <td><?php echo number_format($invoice['total_amount'], 2); ?> </td>
                                            <td>
                                                <span class="status-badge status-<?php echo $invoice['status']; ?>">
                                                    <?php echo ucfirst($invoice['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Upcoming Payments -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Upcoming Payments</h2>
                    <a href="invoices/?filter=upcoming" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($upcoming_payments)): ?>
                        <p class="text-muted">No upcoming payments found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Due Date</th>
                                        <th>Client</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcoming_payments as $payment): ?>
                                        <tr>
                                            <td><?php echo date('Y-m-d', strtotime($payment['due_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($payment['client_name']); ?></td>
                                            <td><?php echo number_format($payment['total_amount'], 2); ?> </td>
                                            <td>
                                                <span class="status-badge status-<?php echo $payment['status']; ?>">
                                                    <?php echo ucfirst($payment['status']); ?>
                                                </span>
                                            </td>
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

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2>Quick Actions</h2>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="invoices/create.php" class="quick-action-card">
                            <i class="fas fa-file-invoice"></i>
                            <span>Create Invoice</span>
                        </a>
                        <a href="clients/" class="quick-action-card">
                            <i class="fas fa-users"></i>
                            <span>Manage Clients</span>
                        </a>
                        <a href="invoices/" class="quick-action-card">
                            <i class="fas fa-list"></i>
                            <span>View Invoices</span>
                        </a>
                        <a href="reports/" class="quick-action-card">
                            <i class="fas fa-chart-bar"></i>
                            <span>View Reports</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../inc/footer.php'; ?> 