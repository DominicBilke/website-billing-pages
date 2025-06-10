<?php
require_once __DIR__ . '/../inc/header.php';
require_once __DIR__ . '/../inc/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: invoices.php');
    exit();
}

try {
    $pdo->beginTransaction();

    // Insert invoice
    $stmt = $pdo->prepare("
        INSERT INTO invoices (
            client_id, invoice_date, due_date, payment_method, 
            subtotal, tax_amount, total_amount, notes, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'draft')
    ");

    $client_id = $_POST['client_id'];
    $invoice_date = $_POST['invoice_date'];
    $due_date = $_POST['due_date'];
    $payment_method = $_POST['payment_method'];
    $notes = $_POST['notes'] ?? '';

    // Calculate totals
    $subtotal = 0;
    foreach ($_POST['items'] as $item) {
        $subtotal += $item['quantity'] * $item['price'];
    }
    $tax_rate = getPaymentSettings()['tax_rate'] / 100;
    $tax_amount = $subtotal * $tax_rate;
    $total_amount = $subtotal + $tax_amount;

    $stmt->execute([
        $client_id, $invoice_date, $due_date, $payment_method,
        $subtotal, $tax_amount, $total_amount, $notes
    ]);

    $invoice_id = $pdo->lastInsertId();

    // Insert invoice items
    $stmt = $pdo->prepare("
        INSERT INTO invoice_items (
            invoice_id, description, quantity, unit_price, total_price
        ) VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($_POST['items'] as $item) {
        $total_price = $item['quantity'] * $item['price'];
        $stmt->execute([
            $invoice_id,
            $item['description'],
            $item['quantity'],
            $item['price'],
            $total_price
        ]);
    }

    // Generate invoice number
    $invoice_number = 'INV-' . str_pad($invoice_id, 6, '0', STR_PAD_LEFT);
    $stmt = $pdo->prepare("UPDATE invoices SET invoice_number = ? WHERE id = ?");
    $stmt->execute([$invoice_number, $invoice_id]);

    $pdo->commit();

    // Redirect to the new invoice
    header('Location: invoice_view.php?id=' . $invoice_id . '&success=1');
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: invoice_create.php?error=1');
    exit();
} 