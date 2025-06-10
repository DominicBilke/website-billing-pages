<?php
require_once __DIR__ . '/../inc/header.php';
require_once __DIR__ . '/../config/db.php';

$user_id = $_SESSION['user_id'];
$client_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM clients WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $client_id, $user_id);
if ($stmt->execute()) {
    header('Location: clients.php');
    exit;
} else {
    echo "Failed to delete client.";
}
?> 