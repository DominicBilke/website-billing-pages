<?php
require_once __DIR__ . '/../inc/header.php';
require_once __DIR__ . '/../config/db.php';

$user_id = $_SESSION['user_id'];

// Fetch clients
$stmt = $conn->prepare("SELECT * FROM clients WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$clients = $result->fetch_all(MYSQLI_ASSOC);
?>

<h1>Clients</h1>
<a href="client_add.php">Add New Client</a>
<table border="1">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($clients as $client): ?>
    <tr>
        <td><?php echo htmlspecialchars($client['name']); ?></td>
        <td><?php echo htmlspecialchars($client['email']); ?></td>
        <td><?php echo htmlspecialchars($client['phone']); ?></td>
        <td>
            <a href="client_edit.php?id=<?php echo $client['id']; ?>">Edit</a>
            <a href="client_delete.php?id=<?php echo $client['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require_once __DIR__ . '/../inc/footer.php'; ?> 