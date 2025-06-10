<?php
require_once __DIR__ . '/../../inc/header.php';
require_once __DIR__ . '/../../config/db.php';

// Get date range from request or default to current month
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Get revenue statistics
$stmt = $conn->prepare("
    SELECT 
        COUNT(id) as total_invoices,
        SUM(total_amount) as total_revenue,
        AVG(total_amount) as average_invoice,
        SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END) as paid_amount,
        SUM(CASE WHEN status = 'unpaid' THEN total_amount ELSE 0 END) as unpaid_amount,
        SUM(CASE WHEN status = 'spent' THEN total_amount ELSE 0 END) as overdue_amount
    FROM invoices 
    WHERE date BETWEEN ? AND ?
");
$stmt->execute([$start_date, $end_date]);
$stmt->bind_result($total_invoices, $total_revenue, $average_invoice, $paid_amount, $unpaid_amount, $overdue_amount);
$revenue_stats = $stmt->fetch();
$stmt->close();

// Get monthly revenue data for chart
$stmt = $conn->prepare("
    SELECT 
        DATE_FORMAT(date, '%Y-%m') as month,
        SUM(total_amount) as revenue
    FROM invoices 
    WHERE date BETWEEN ? AND ?
    GROUP BY DATE_FORMAT(date, '%Y-%m')
    ORDER BY month
");
$stmt->execute([$start_date, $end_date]);
$stmt->bind_result($revenue, $month);
$monthly_revenue = $stmt->fetch();
$stmt->close();

// Get top clients
$stmt = $conn->prepare("
    SELECT 
        c.name,
        COUNT(i.id) as invoice_count,
        SUM(i.total_amount) as total_amount
    FROM clients c
    LEFT JOIN invoices i ON c.id = i.client_id
    WHERE i.date BETWEEN ? AND ?
    GROUP BY c.id
    ORDER BY total_amount DESC
    LIMIT 5
");
$stmt->execute([$start_date, $end_date]);
$stmt->bind_result($name, $invoice_count, $total_amount);
$top_clients = $stmt->fetch();
$stmt->close();

// Get payment status distribution
$stmt = $conn->prepare("
    SELECT 
        status,
        COUNT(*) as count,
        SUM(total_amount) as amount
    FROM invoices 
    WHERE date BETWEEN ? AND ?
    GROUP BY status
");
$stmt->execute([$start_date, $end_date]);
$stmt->bind_result($status, $count, $amount);
$payment_stats = $stmt->fetch();
$stmt->close();
?>

<div class="container">
    <div class="page-header">
        <h1>Reports & Analytics</h1>
        <div class="header-actions">
            <form class="date-filter" method="GET">
                <div class="input-group">
                    <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                    <span class="input-group-text">to</span>
                    <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <h3><?php echo number_format($total_invoices); ?></h3>
                <p>Total Invoices</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-icon">
                    <i class="fas fa-euro-sign"></i>
                </div>
                <h3><?php echo number_format($total_revenue, 2); ?> €</h3>
                <p>Total Revenue</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3><?php echo number_format($average_invoice, 2); ?> €</h3>
                <p>Average Invoice</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3><?php echo number_format($unpaid_amount + $overdue_amount, 2); ?> €</h3>
                <p>Outstanding</p>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Revenue Trend</h3>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Status</h3>
                </div>
                <div class="card-body">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top Clients</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Invoices</th>
                                    <th>Total Amount</th>
                                    <th>Average Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(is_array($name)) 
					foreach ($name as $i=>$n): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($name[$i]); ?></td>
                                    <td><?php echo number_format($count[$i]); ?></td>
                                    <td><?php echo number_format($amount[$i], 2); ?> €</td>
                                    <td><?php echo number_format($amount[$i] / $count[$i], 2); ?> €</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($month); ?>,
            datasets: [{
                label: 'Revenue',
                data: <?php echo json_encode($revenue); ?>,
                borderColor: '#1a237e',
                backgroundColor: 'rgba(26, 35, 126, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
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
                            return value + ' €';
                        }
                    }
                }
            }
        }
    });

    // Payment Status Chart
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Unpaid', 'Spent'],
            datasets: [{
                data: [
                    <?php echo $paid_amount; ?>,
                    <?php echo $unpaid_amount; ?>,
                    <?php echo $overdue_amount; ?>
                ],
                backgroundColor: [
                    '#2e7d32',
                    '#f57c00',
                    '#c62828'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../../inc/footer.php'; ?> 