<?php
require_once __DIR__ . '/../../inc/header.php';
require_once __DIR__ . '/../../config/db.php';

$user_id = $_SESSION['user_id'];
$invoice_id = $_GET['id'];

// Fetch invoice details
$stmt = $conn->prepare("SELECT * FROM invoices WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $invoice_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();

if (!$invoice) {
    header('Location: /invoices');
    exit;
}

// Fetch clients for dropdown
$stmt = $conn->prepare("SELECT id, name FROM clients WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$clients = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_id = $_POST['client_id'];
    $date = $_POST['date'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];
    $total = $_POST['total'];

    $stmt = $conn->prepare("UPDATE invoices SET client_id = ?, date = ?, due_date = ?, status = ?, total_amount = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("isssdii", $client_id, $date, $due_date, $status, $total, $invoice_id, $user_id);
    if ($stmt->execute()) {
        header('Location: /invoices');
        exit;
    } else {
        $error = "Failed to update invoice.";
    }
}
?>

<h1>Edit Invoice</h1>
<?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
<form method="POST">
    <label for="client_id">Client:</label>
    <select id="client_id" name="client_id" required>
        <?php foreach ($clients as $client): ?>
        <option value="<?php echo $client['id']; ?>" <?php echo ($client['id'] == $invoice['client_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($client['name']); ?></option>
        <?php endforeach; ?>
    </select><br>
    <label for="date">Date:</label>
    <input type="date" id="date" name="date" value="<?php echo $invoice['date']; ?>" required><br>
    <label for="due_date">Due Date:</label>
    <input type="date" id="due_date" name="due_date" value="<?php echo $invoice['due_date']; ?>" required><br>
    <label for="status">Status:</label>
    <select id="status" name="status" required>
        <option value="draft" <?php echo ($invoice['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
        <option value="sent" <?php echo ($invoice['status'] == 'sent') ? 'selected' : ''; ?>>Sent</option>
        <option value="paid" <?php echo ($invoice['status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
    </select><br>
    <label for="total">Total:</label>
    <input type="number" id="total" name="total" step="0.01" value="<?php echo $invoice['total_amount']; ?>" required><br>
    <button type="submit">Update Invoice</button>
</form>
<a href="../invoices.php">Back to Invoices</a>

<?php require_once __DIR__ . '/../../inc/footer.php'; ?> 