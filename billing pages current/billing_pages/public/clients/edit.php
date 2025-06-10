<?php
require_once __DIR__ . '/../../inc/header.php';
require_once __DIR__ . '/../../config/db.php';

$user_id = $_SESSION['user_id'];
$client_id = $_GET['id'];

// Fetch client details
$stmt = $conn->prepare("SELECT * FROM clients WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $client_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc();

if (!$client) {
    header('Location: clients.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE clients SET name = ?, address = ?, email = ?, phone = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssssii", $name, $address, $email, $phone, $client_id, $user_id);
    if ($stmt->execute()) {
        header('Location: /clients');
        exit;
    } else {
        $error = "Failed to update client.";
    }
}
?>

<h1>Edit Client</h1>
<?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
<form method="POST">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($client['name']); ?>" required><br>
    <label for="address">Address:</label>
    <textarea id="address" name="address"><?php echo htmlspecialchars($client['address']); ?></textarea><br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($client['email']); ?>"><br>
    <label for="phone">Phone:</label>
    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($client['phone']); ?>"><br>
    <button type="submit">Update Client</button>
</form>
<a href="/clients">Back to Clients</a>

<?php require_once __DIR__ . '/../../inc/footer.php'; ?> 