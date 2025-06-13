<?php
/**
 * Admin helper functions
 */

require_once __DIR__ . '/../config/db.php';

function isAdmin($user_id = null) {
    global $pdo;
    
    if ($user_id === null) {
        $user_id = $_SESSION['user_id'] ?? null;
    }
    
    if (!$user_id) {
        return false;
    }
    
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $role = $stmt->fetchColumn();
    
    return $role === 'admin';
}

function getPaymentSettings($user_id = null) {
    global $pdo;
    
    if ($user_id === null) {
        $user_id = $_SESSION['user_id'] ?? null;
    }
    
    if (!$user_id) {
        return [];
    }
    
    $stmt = $pdo->prepare("SELECT * FROM settings WHERE setting_key LIKE 'payment_%' AND user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

function getPaymentStats() {
    global $pdo;
    
    $stats = [
        'total_paid' => 0,
        'total_pending' => 0,
        'total_overdue' => 0,
        'recent_payments' => []
    ];
    
    // Get payment totals
    $stmt = $pdo->query("
        SELECT 
            payment_status,
            COUNT(*) as count,
            SUM(amount) as total
        FROM payments 
        GROUP BY payment_status
    ");
    
    while ($row = $stmt->fetch()) {
        switch ($row['payment_status']) {
            case 'paid':
                $stats['total_paid'] = $row['total'];
                break;
            case 'pending':
                $stats['total_pending'] = $row['total'];
                break;
            case 'overdue':
                $stats['total_overdue'] = $row['total'];
                break;
        }
    }
    
    // Get recent payments
    $stmt = $pdo->query("
        SELECT p.*, i.invoice_number, c.name as client_name
        FROM payments p
        JOIN invoices i ON p.invoice_id = i.id
        JOIN clients c ON i.client_id = c.id
        ORDER BY p.payment_date DESC
        LIMIT 5
    ");
    
    $stats['recent_payments'] = $stmt->fetchAll();
    
    return $stats;
}

function getInvoiceStats() {
    global $pdo;
    
    $stats = [
        'total_invoices' => 0,
        'total_amount' => 0,
        'paid_invoices' => 0,
        'unpaid_invoices' => 0,
        'overdue_invoices' => 0
    ];
    
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_count,
            SUM(total_amount) as total_amount,
            SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_count,
            SUM(CASE WHEN payment_status = 'unpaid' THEN 1 ELSE 0 END) as unpaid_count,
            SUM(CASE WHEN payment_status = 'overdue' THEN 1 ELSE 0 END) as overdue_count
        FROM invoices
    ");
    
    $row = $stmt->fetch();
    if ($row) {
        $stats['total_invoices'] = $row['total_count'];
        $stats['total_amount'] = $row['total_amount'];
        $stats['paid_invoices'] = $row['paid_count'];
        $stats['unpaid_invoices'] = $row['unpaid_count'];
        $stats['overdue_invoices'] = $row['overdue_count'];
    }
    
    return $stats;
}

function getClientStats() {
    global $pdo;
    
    $stats = [
        'total_clients' => 0,
        'active_clients' => 0,
        'total_revenue' => 0,
        'top_clients' => []
    ];
    
    // Get total and active clients
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_count,
            COUNT(CASE WHEN status = 'active' THEN 1 END) as active_count
        FROM clients
    ");
    
    $row = $stmt->fetch();
    if ($row) {
        $stats['total_clients'] = $row['total_count'];
        $stats['active_clients'] = $row['active_count'];
    }
    
    // Get total revenue
    $stmt = $pdo->query("
        SELECT SUM(total_amount) as total_revenue
        FROM invoices
        WHERE payment_status = 'paid'
    ");
    
    $row = $stmt->fetch();
    if ($row) {
        $stats['total_revenue'] = $row['total_revenue'];
    }
    
    // Get top clients
    $stmt = $pdo->query("
        SELECT 
            c.name,
            COUNT(i.id) as invoice_count,
            SUM(i.total_amount) as total_amount
        FROM clients c
        LEFT JOIN invoices i ON c.id = i.client_id
        GROUP BY c.id
        ORDER BY total_amount DESC
        LIMIT 5
    ");
    
    $stats['top_clients'] = $stmt->fetchAll();
    
    return $stats;
}

function generateInvoiceNumber() {
    global $pdo;
    
    // Get the invoice prefix from settings
    $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'invoice_prefix'");
    $prefix = $stmt->fetchColumn() ?: 'INV';
    
    // Get the current year
    $year = date('Y');
    
    // Get the last invoice number for this year
    $stmt = $pdo->prepare("
        SELECT MAX(CAST(SUBSTRING(invoice_number, 6) AS UNSIGNED)) as max_num 
        FROM invoices 
        WHERE invoice_number LIKE ?
    ");
    $stmt->execute([$prefix . '-' . $year . '-%']);
    $result = $stmt->fetch();
    
    // Generate the next number
    $next_num = ($result['max_num'] ?? 0) + 1;
    
    // Format the invoice number
    return sprintf('%s-%s-%04d', $prefix, $year, $next_num);
}

function sendInvoiceEmail($invoice_id) {
    global $pdo;
    
    // Get invoice details
    $stmt = $pdo->prepare("
        SELECT i.*, c.name as client_name, c.email as client_email
        FROM invoices i
        JOIN clients c ON i.client_id = c.id
        WHERE i.id = ?
    ");
    $stmt->execute([$invoice_id]);
    $invoice = $stmt->fetch();
    
    if (!$invoice) {
        return false;
    }
    
    // Get email template
    $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'invoice_template'");
    $template = $stmt->fetchColumn();
    
    if (!$template) {
        $template = "Dear {client_name},\n\nPlease find attached invoice {invoice_number} for {amount}.\n\nDue date: {due_date}\n\nThank you for your business.";
    }
    
    // Replace placeholders
    $message = str_replace(
        ['{client_name}', '{invoice_number}', '{amount}', '{due_date}'],
        [$invoice['client_name'], $invoice['invoice_number'], $invoice['total_amount'], $invoice['due_date']],
        $template
    );
    
    // Get SMTP settings
    $stmt = $pdo->query("SELECT * FROM settings WHERE setting_key LIKE 'smtp_%'");
    $smtp_settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Send email using PHPMailer or similar
    // Implementation depends on your email library
    
    return true;
}

function checkOverdueInvoices() {
    global $pdo;
    
    // Get overdue invoices
    $stmt = $pdo->query("
        SELECT id, client_id, invoice_number, total_amount, due_date
        FROM invoices
        WHERE payment_status = 'unpaid'
        AND due_date < CURDATE()
    ");
    
    $overdue_invoices = $stmt->fetchAll();
    
    foreach ($overdue_invoices as $invoice) {
        // Update status
        $stmt = $pdo->prepare("
            UPDATE invoices 
            SET payment_status = 'overdue'
            WHERE id = ?
        ");
        $stmt->execute([$invoice['id']]);
        
        // Send reminder email
        sendPaymentReminder($invoice['id']);
    }
    
    return count($overdue_invoices);
}

function sendPaymentReminder($invoice_id) {
    global $pdo;
    
    // Get invoice details
    $stmt = $pdo->prepare("
        SELECT i.*, c.name as client_name, c.email as client_email
        FROM invoices i
        JOIN clients c ON i.client_id = c.id
        WHERE i.id = ?
    ");
    $stmt->execute([$invoice_id]);
    $invoice = $stmt->fetch();
    
    if (!$invoice) {
        return false;
    }
    
    // Get reminder template
    $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'payment_reminder_template'");
    $template = $stmt->fetchColumn();
    
    if (!$template) {
        $template = "Dear {client_name},\n\nThis is a reminder that invoice {invoice_number} for {amount} is overdue.\n\nDue date: {due_date}\nDays overdue: {days_overdue}\n\nPlease process the payment as soon as possible.\n\nThank you.";
    }
    
    // Calculate days overdue
    $due_date = new DateTime($invoice['due_date']);
    $today = new DateTime();
    $days_overdue = $today->diff($due_date)->days;
    
    // Replace placeholders
    $message = str_replace(
        ['{client_name}', '{invoice_number}', '{amount}', '{due_date}', '{days_overdue}'],
        [$invoice['client_name'], $invoice['invoice_number'], $invoice['total_amount'], $invoice['due_date'], $days_overdue],
        $template
    );
    
    // Send email using PHPMailer or similar
    // Implementation depends on your email library
    
    return true;
}

function updatePaymentSetting($key, $value) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE payment_settings SET setting_value = ? WHERE setting_key = ?");
    return $stmt->execute([$value, $key]);
}

function getPaymentMethods() {
    $settings = getPaymentSettings();
    return explode(',', $settings['payment_methods']);
}

function getCompanyBankDetails() {
    $settings = getPaymentSettings();
    return json_decode($settings['company_bank_details'], true);
} 