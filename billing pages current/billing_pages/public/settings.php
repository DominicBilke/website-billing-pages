<?php
require_once __DIR__ . '/../inc/header.php';
require_once __DIR__ . '/../inc/db.php';

$error = '';
$success = '';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Update general settings
        $general_settings = [
            'company_name' => $_POST['company_name'] ?? '',
            'company_email' => $_POST['company_email'] ?? '',
            'company_phone' => $_POST['company_phone'] ?? '',
            'company_address' => $_POST['company_address'] ?? '',
            'company_city' => $_POST['company_city'] ?? '',
            'company_postal' => $_POST['company_postal'] ?? '',
            'company_country' => $_POST['company_country'] ?? '',
            'company_vat' => $_POST['company_vat'] ?? '',
            'currency' => $_POST['currency'] ?? 'EUR',
            'tax_rate' => $_POST['tax_rate'] ?? '19',
            'invoice_prefix' => $_POST['invoice_prefix'] ?? 'INV',
            'payment_terms' => $_POST['payment_terms'] ?? '30',
            'late_fee_rate' => $_POST['late_fee_rate'] ?? '0',
            'smtp_host' => $_POST['smtp_host'] ?? '',
            'smtp_port' => $_POST['smtp_port'] ?? '',
            'smtp_user' => $_POST['smtp_user'] ?? '',
            'smtp_pass' => $_POST['smtp_pass'] ?? '',
            'smtp_secure' => $_POST['smtp_secure'] ?? 'tls'
        ];

        foreach ($general_settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) 
                                 VALUES (?, ?) 
                                 ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->execute([$key, $value, $value]);
        }

        // Update invoice templates
        $templates = [
            'invoice_template' => $_POST['invoice_template'] ?? '',
            'payment_reminder_template' => $_POST['payment_reminder_template'] ?? '',
            'welcome_email_template' => $_POST['welcome_email_template'] ?? ''
        ];

        foreach ($templates as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) 
                                 VALUES (?, ?) 
                                 ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->execute([$key, $value, $value]);
        }

        $pdo->commit();
        $success = 'Settings updated successfully!';
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'An error occurred: ' . $e->getMessage();
    }
}

// Get current settings
$stmt = $pdo->query("SELECT * FROM settings");
$settings = $stmt->fetchAll();

foreach($settings as $k => $set) {
  $settings[$set['setting_key']] = $set['setting_value'];
}

?>

<div class="container">
    <div class="page-header">
        <h1>System Settings</h1>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" class="form">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Company Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="company_name">Company Name</label>
                            <input type="text" id="company_name" name="company_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['company_name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="company_email">Email</label>
                            <input type="email" id="company_email" name="company_email" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['company_email'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="company_phone">Phone</label>
                            <input type="tel" id="company_phone" name="company_phone" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['company_phone'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="company_address">Address</label>
                            <textarea id="company_address" name="company_address" class="form-control" rows="2"><?php 
                                echo htmlspecialchars($settings['company_address'] ?? ''); 
                            ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_city">City</label>
                                    <input type="text" id="company_city" name="company_city" class="form-control" 
                                           value="<?php echo htmlspecialchars($settings['company_city'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_postal">Postal Code</label>
                                    <input type="text" id="company_postal" name="company_postal" class="form-control" 
                                           value="<?php echo htmlspecialchars($settings['company_postal'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="company_country">Country</label>
                            <input type="text" id="company_country" name="company_country" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['company_country'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="company_vat">VAT Number</label>
                            <input type="text" id="company_vat" name="company_vat" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['company_vat'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Invoice Settings</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select id="currency" name="currency" class="form-control">
                                <option value="EUR" <?php echo ($settings['currency'] ?? '') === 'EUR' ? 'selected' : ''; ?>>Euro (€)</option>
                                <option value="USD" <?php echo ($settings['currency'] ?? '') === 'USD' ? 'selected' : ''; ?>>US Dollar ($)</option>
                                <option value="GBP" <?php echo ($settings['currency'] ?? '') === 'GBP' ? 'selected' : ''; ?>>British Pound (£)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tax_rate">Tax Rate (%)</label>
                            <input type="number" id="tax_rate" name="tax_rate" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['tax_rate'] ?? '19'); ?>" min="0" max="100" step="0.1">
                        </div>
                        <div class="form-group">
                            <label for="invoice_prefix">Invoice Number Prefix</label>
                            <input type="text" id="invoice_prefix" name="invoice_prefix" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['invoice_prefix'] ?? 'INV'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="payment_terms">Default Payment Terms (days)</label>
                            <input type="number" id="payment_terms" name="payment_terms" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['payment_terms'] ?? '30'); ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label for="late_fee_rate">Late Payment Fee Rate (%)</label>
                            <input type="number" id="late_fee_rate" name="late_fee_rate" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['late_fee_rate'] ?? '0'); ?>" min="0" step="0.1">
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Email Settings</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="smtp_host">SMTP Host</label>
                            <input type="text" id="smtp_host" name="smtp_host" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['smtp_host'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="smtp_port">SMTP Port</label>
                            <input type="number" id="smtp_port" name="smtp_port" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="smtp_user">SMTP Username</label>
                            <input type="text" id="smtp_user" name="smtp_user" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['smtp_user'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="smtp_pass">SMTP Password</label>
                            <input type="password" id="smtp_pass" name="smtp_pass" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['smtp_pass'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="smtp_secure">SMTP Security</label>
                            <select id="smtp_secure" name="smtp_secure" class="form-control">
                                <option value="tls" <?php echo ($settings['smtp_secure'] ?? '') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                <option value="ssl" <?php echo ($settings['smtp_secure'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                <option value="none" <?php echo ($settings['smtp_secure'] ?? '') === 'none' ? 'selected' : ''; ?>>None</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Email Templates</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="invoice_template">Invoice Email Template</label>
                    <textarea id="invoice_template" name="invoice_template" class="form-control" rows="6"><?php 
                        echo htmlspecialchars($settings['invoice_template'] ?? ''); 
                    ?></textarea>
                    <small class="form-text text-muted">
                        Available variables: {invoice_number}, {client_name}, {amount}, {due_date}
                    </small>
                </div>
                <div class="form-group">
                    <label for="payment_reminder_template">Payment Reminder Template</label>
                    <textarea id="payment_reminder_template" name="payment_reminder_template" class="form-control" rows="6"><?php 
                        echo htmlspecialchars($settings['payment_reminder_template'] ?? ''); 
                    ?></textarea>
                    <small class="form-text text-muted">
                        Available variables: {invoice_number}, {client_name}, {amount}, {due_date}, {days_overdue}
                    </small>
                </div>
                <div class="form-group">
                    <label for="welcome_email_template">Welcome Email Template</label>
                    <textarea id="welcome_email_template" name="welcome_email_template" class="form-control" rows="6"><?php 
                        echo htmlspecialchars($settings['welcome_email_template'] ?? ''); 
                    ?></textarea>
                    <small class="form-text text-muted">
                        Available variables: {client_name}, {login_url}
                    </small>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</div>

<?php require_once 'inc/footer.php'; ?> 