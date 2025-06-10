<?php
require_once __DIR__ . '/../inc/header.php';
require_once __DIR__ . '/../config/db.php';

$user_id = $_SESSION['user_id'];

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

    $stmt = $conn->prepare("INSERT INTO invoices (user_id, client_id, date, due_date, status, total) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssd", $user_id, $client_id, $date, $due_date, $status, $total);
    if ($stmt->execute()) {
        header('Location: invoices.php');
        exit;
    } else {
        $error = "Failed to add invoice.";
    }
}
?>

<h1>Add New Invoice</h1>
<?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
<form method="POST">
    <label for="client_id">Client:</label>
    <select id="client_id" name="client_id" required>
        <?php foreach ($clients as $client): ?>
        <option value="<?php echo $client['id']; ?>"><?php echo htmlspecialchars($client['name']); ?></option>
        <?php endforeach; ?>
    </select><br>
    <label for="date">Date:</label>
    <input type="date" id="date" name="date" required><br>
    <label for="due_date">Due Date:</label>
    <input type="date" id="due_date" name="due_date" required><br>
    <label for="status">Status:</label>
    <select id="status" name="status" required>
        <option value="draft">Draft</option>
        <option value="sent">Sent</option>
        <option value="paid">Paid</option>
    </select><br>
    <label for="total">Total:</label>
    <input type="number" id="total" name="total" step="0.01" required><br>
    <button type="submit">Add Invoice</button>
</form>
<a href="invoices.php">Back to Invoices</a>

<?php require_once __DIR__ . '/../inc/footer.php'; ?> 