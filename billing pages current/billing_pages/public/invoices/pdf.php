<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../inc/InvoicePDF.php';

$user_id = $_SESSION['user_id'];
$invoice_id = $_GET['id'];

// Fetch invoice and client details
$stmt = $conn->prepare("SELECT i.*, c.name as client_name, c.address as client_address, c.email as client_email, c.phone as client_phone 
                       FROM invoices i 
                       JOIN clients c ON i.client_id = c.id
                       WHERE i.id = ? AND i.user_id = ?");
$stmt->bind_param("ii", $invoice_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();

$stmt = $conn->prepare("SELECT *
                       FROM invoice_items
			WHERE invoice_id = ?");
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$result = $stmt->get_result();
$invoice_items = $result->fetch_all(MYSQLI_ASSOC);

// Get current settings
$stmt = $conn->query("SELECT * FROM settings");
$settings = $stmt->fetch_all(MYSQLI_ASSOC);

foreach($settings as $k => $set) {
  $settings[$set['setting_key']] = $set['setting_value'];
}

if (!$invoice) {
    header('Location: ../invoices.php');
    exit;
}

// Prepare client data
$client = [
    'name' => $invoice['client_name'],
    'address' => $invoice['client_address'],
    'email' => $invoice['client_email'],
    'phone' => $invoice['client_phone']
];

// Generate PDF
$pdf = new InvoicePDF($invoice, $invoice_items, $client, $settings);
$pdf->generate();

// Output PDF
ob_end_clean();
$pdf->Output('Invoice_' . $invoice['invoice_number'] . '.pdf', 'D'); 