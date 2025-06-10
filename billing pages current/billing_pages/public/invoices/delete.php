<?php
require_once __DIR__ . '/../../inc/header.php';
require_once __DIR__ . '/../../config/db.php';

$user_id = $_SESSION['user_id'];
$invoice_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM invoices WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $invoice_id, $user_id);
if ($stmt->execute()) {
    header('Location: ../invoices.php');
    exit;
} else {
    echo "Failed to delete invoice.";
}
?> 