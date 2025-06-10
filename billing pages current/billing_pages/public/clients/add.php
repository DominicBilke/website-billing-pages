<?php
require_once __DIR__ . '/../inc/header.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO clients (user_id, name, address, email, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $name, $address, $email, $phone);
    if ($stmt->execute()) {
        header('Location: clients.php');
        exit;
    } else {
        $error = "Failed to add client.";
    }
}
?>

<h1>Add New Client</h1>
<?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
<form method="POST">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required><br>
    <label for="address">Address:</label>
    <textarea id="address" name="address"></textarea><br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email"><br>
    <label for="phone">Phone:</label>
    <input type="text" id="phone" name="phone"><br>
    <button type="submit">Add Client</button>
</form>
<a href="clients.php">Back to Clients</a>

<?php require_once __DIR__ . '/../inc/footer.php'; ?> 