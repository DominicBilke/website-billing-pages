<?php
require_once __DIR__ . '/../inc/header.php';
require_once __DIR__ . '/../config/db.php';

$user_id = $_SESSION['user_id'];
$invoice_id = $_GET['id'];

// Fetch invoice details
$stmt = $conn->prepare("SELECT i.*, c.name as client_name, c.address as client_address, c.email as client_email, c.phone as client_phone FROM invoices i JOIN clients c ON i.client_id = c.id WHERE i.id = ? AND i.user_id = ?");
$stmt->bind_param("ii", $invoice_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();

if (!$invoice) {
    header('Location: invoices.php');
    exit;
}
?>

<div class="invoice-container">
    <div class="invoice-header">
        <h1>Invoice #<?php echo $invoice['id']; ?></h1>
        <span class="status-badge status-<?php echo $invoice['status']; ?>"><?php echo ucfirst($invoice['status']); ?></span>
    </div>

    <div class="invoice-details">
        <div class="client-info">
            <h2>Client Information</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($invoice['client_name']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($invoice['client_address']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($invoice['client_email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($invoice['client_phone']); ?></p>
        </div>

        <div class="invoice-info">
            <h2>Invoice Information</h2>
            <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($invoice['date'])); ?></p>
            <p><strong>Due Date:</strong> <?php echo date('F j, Y', strtotime($invoice['due_date'])); ?></p>
            <p><strong>Total Amount:</strong> €<?php echo number_format($invoice['total'], 2); ?></p>
        </div>
    </div>

    <div class="action-buttons">
        <a href="invoice_pdf.php?id=<?php echo $invoice['id']; ?>" class="btn btn-view">Download PDF</a>
        <a href="invoice_edit.php?id=<?php echo $invoice['id']; ?>" class="btn btn-edit">Edit Invoice</a>
        <a href="invoices.php" class="btn btn-view">Back to Invoices</a>
    </div>
</div>

<?php require_once __DIR__ . '/../inc/footer.php'; ?> 