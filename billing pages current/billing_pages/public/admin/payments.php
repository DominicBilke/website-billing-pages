<?php
require_once __DIR__ . '/../../inc/header.php';
require_once __DIR__ . '/../../inc/db.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isAdmin($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Handle payment status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id'], $_POST['status'])) {
    $payment_id = (int)$_POST['payment_id'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE payments SET status = ? WHERE id = ?");
    $stmt->execute([$status, $payment_id]);
    
    // Update invoice payment status
    $stmt = $pdo->prepare("
        UPDATE invoices i 
        SET payment_status = CASE
            WHEN (SELECT SUM(amount) FROM payments WHERE invoice_id = i.id AND status = 'completed') >= i.total_amount THEN 'paid'
            WHEN (SELECT SUM(amount) FROM payments WHERE invoice_id = i.id AND status = 'completed') > 0 THEN 'partially_paid'
            ELSE 'unpaid'
        END
        WHERE i.id = (SELECT invoice_id FROM payments WHERE id = ?)
    ");
    $stmt->execute([$payment_id]);
    
    header('Location: payments.php?success=1');
    exit();
}

// Get all payments with invoice and client details
$stmt = $pdo->query("
    SELECT p.*, i.invoice_number, i.total_amount, c.name as client_name
    FROM payments p
    JOIN invoices i ON p.invoice_id = i.id
    JOIN clients c ON i.client_id = c.id
    ORDER BY p.payment_date DESC
");
$payments = $stmt->fetchAll();
?>

<div class="container">
    <div class="page-header">
        <h1>Payment Management</h1>
        <div class="header-actions">
            <a href="payment_settings.php" class="btn btn-secondary">Payment Settings</a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Payment status updated successfully.</div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invoice</th>
                            <th>Client</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?php echo date('Y-m-d', strtotime($payment['payment_date'])); ?></td>
                                <td>
                                    <a href="../invoice_view.php?id=<?php echo $payment['invoice_id']; ?>">
                                        <?php echo htmlspecialchars($payment['invoice_number']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($payment['client_name']); ?></td>
                                <td><?php echo number_format($payment['amount'], 2); ?> €</td>
                                <td><?php echo ucwords(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $payment['status']; ?>">
                                        <?php echo ucfirst($payment['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            Update Status
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="dropdown-item">Mark as Completed</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                                    <input type="hidden" name="status" value="failed">
                                                    <button type="submit" class="dropdown-item">Mark as Failed</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                                    <input type="hidden" name="status" value="refunded">
                                                    <button type="submit" class="dropdown-item">Mark as Refunded</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../inc/footer.php'; ?> 