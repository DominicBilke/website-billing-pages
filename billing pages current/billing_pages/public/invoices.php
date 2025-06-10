<?php
require_once __DIR__ . '/../inc/header.php';
require_once __DIR__ . '/../config/db.php';

$user_id = $_SESSION['user_id'];

// Fetch invoices
$stmt = $conn->prepare("SELECT i.*, c.name as client_name FROM invoices i JOIN clients c ON i.client_id = c.id WHERE i.user_id = ? ORDER BY i.date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$invoices = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="page-header">
    <h1>Invoices</h1>
    <a href="/invoices/create.php" class="btn btn-view">Add New Invoice</a>
</div>

<?php if (empty($invoices)): ?>
    <div class="alert alert-info">No invoices found. <a href="invoice_add.php">Create your first invoice</a>.</div>
<?php else: ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $invoice): ?>
                <tr>
                    <td><?php echo $invoice['id']; ?></td>
                    <td><?php echo htmlspecialchars($invoice['client_name']); ?></td>
                    <td><?php echo date('M j, Y', strtotime($invoice['date'])); ?></td>
                    <td><?php echo date('M j, Y', strtotime($invoice['due_date'])); ?></td>
                    <td><span class="status-badge status-<?php echo $invoice['status']; ?>"><?php echo ucfirst($invoice['status']); ?></span></td>
                    <td>€<?php echo number_format($invoice['total_amount'], 2); ?></td>
                    <td class="action-buttons">
                        <a href="invoices/view.php?id=<?php echo $invoice['id']; ?>" class="btn btn-view">View</a>
                        <a href="invoices/edit.php?id=<?php echo $invoice['id']; ?>" class="btn btn-edit">Edit</a>
                        <a href="invoices/delete.php?id=<?php echo $invoice['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this invoice?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../inc/footer.php'; ?> 