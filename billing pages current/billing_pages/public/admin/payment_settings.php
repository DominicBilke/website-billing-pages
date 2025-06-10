<?php
require_once __DIR__ . '/../../inc/header.php';
require_once __DIR__ . '/../../inc/db.php';
require_once __DIR__ . '/../../inc/admin_functions.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isAdmin($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = true;
    
    // Update payment methods
    if (isset($_POST['payment_methods'])) {
        $methods = implode(',', $_POST['payment_methods']);
        $success = $success && updatePaymentSetting('payment_methods', $methods);
    }
    
    // Update bank details
    if (isset($_POST['bank_details'])) {
        $bank_details = json_encode($_POST['bank_details']);
        $success = $success && updatePaymentSetting('company_bank_details', $bank_details);
    }
    
    // Update other settings
    $settings = ['currency', 'tax_rate', 'payment_terms', 'paypal_email'];
    foreach ($settings as $setting) {
        if (isset($_POST[$setting])) {
            $success = $success && updatePaymentSetting($setting, $_POST[$setting]);
        }
    }
    
    if ($success) {
        header('Location: payment_settings.php?success=1');
        exit();
    }
}

// Get current settings
$settings = getPaymentSettings();
$bank_details = getCompanyBankDetails();
$payment_methods = getPaymentMethods();
?>

<div class="container">
    <div class="page-header">
        <h1>Payment Settings</h1>
        <div class="header-actions">
            <a href="payments.php" class="btn btn-secondary">Back to Payments</a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Settings updated successfully.</div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <h3>General Settings</h3>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Methods</label>
                            <div class="form-check">
                                <input type="checkbox" name="payment_methods[]" value="bank_transfer" class="form-check-input" 
                                    <?php echo in_array('bank_transfer', $payment_methods) ? 'checked' : ''; ?>>
                                <label class="form-check-label">Bank Transfer</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="payment_methods[]" value="credit_card" class="form-check-input"
                                    <?php echo in_array('credit_card', $payment_methods) ? 'checked' : ''; ?>>
                                <label class="form-check-label">Credit Card</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="payment_methods[]" value="paypal" class="form-check-input"
                                    <?php echo in_array('paypal', $payment_methods) ? 'checked' : ''; ?>>
                                <label class="form-check-label">PayPal</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Currency</label>
                            <input type="text" name="currency" class="form-control" value="<?php echo htmlspecialchars($settings['currency']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tax Rate (%)</label>
                            <input type="number" name="tax_rate" class="form-control" value="<?php echo htmlspecialchars($settings['tax_rate']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Terms (days)</label>
                            <input type="number" name="payment_terms" class="form-control" value="<?php echo htmlspecialchars($settings['payment_terms']); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h3>Bank Details</h3>
                        
                        <div class="mb-3">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_details[bank_name]" class="form-control" 
                                value="<?php echo htmlspecialchars($bank_details['bank_name'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Account Name</label>
                            <input type="text" name="bank_details[account_name]" class="form-control"
                                value="<?php echo htmlspecialchars($bank_details['account_name'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">IBAN</label>
                            <input type="text" name="bank_details[iban]" class="form-control"
                                value="<?php echo htmlspecialchars($bank_details['iban'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">BIC</label>
                            <input type="text" name="bank_details[bic]" class="form-control"
                                value="<?php echo htmlspecialchars($bank_details['bic'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../inc/footer.php'; ?> 