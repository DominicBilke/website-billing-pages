<?php
require_once __DIR__ . '/../inc/header.php';
require_once __DIR__ . '/../inc/db.php';

// Get all clients for the dropdown
$stmt = $pdo->query("SELECT id, name FROM clients ORDER BY name");
$clients = $stmt->fetchAll();

// Get payment methods from settings
$payment_methods = getPaymentMethods();
?>

<div class="container">
    <div class="page-header">
        <h1>Create New Invoice</h1>
        <div class="header-actions">
            <a href="invoices.php" class="btn btn-secondary">Back to Invoices</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="invoice_process.php" id="invoiceForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-section">
                            <h3>Invoice Details</h3>
                            
                            <div class="mb-3">
                                <label class="form-label">Client</label>
                                <select name="client_id" class="form-select" required>
                                    <option value="">Select Client</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?php echo $client['id']; ?>">
                                            <?php echo htmlspecialchars($client['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Invoice Date</label>
                                <input type="date" name="invoice_date" class="form-control" required 
                                    value="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control" required
                                    value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="">Select Payment Method</option>
                                    <?php foreach ($payment_methods as $method): ?>
                                        <option value="<?php echo $method; ?>">
                                            <?php echo ucwords(str_replace('_', ' ', $method)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-section">
                            <h3>Invoice Items</h3>
                            
                            <div id="invoiceItems">
                                <div class="invoice-item">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <input type="text" name="items[0][description]" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Quantity</label>
                                                <input type="number" name="items[0][quantity]" class="form-control" required min="1" value="1">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Price</label>
                                                <input type="number" name="items[0][price]" class="form-control" required min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Total</label>
                                                <input type="text" class="form-control item-total" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary" id="addItem">
                                <i class="fas fa-plus"></i> Add Item
                            </button>

                            <div class="invoice-summary mt-4">
                                <div class="row">
                                    <div class="col-md-6 offset-md-6">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <span id="subtotal">0.00 €</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Tax (<?php echo getPaymentSettings()['tax_rate']; ?>%):</span>
                                            <span id="tax">0.00 €</span>
                                        </div>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total:</span>
                                            <span id="total">0.00 €</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-section">
                            <h3>Additional Information</h3>
                            
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3" 
                                    placeholder="Add any additional notes or terms here..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Create Invoice</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const invoiceItems = document.getElementById('invoiceItems');
    const addItemBtn = document.getElementById('addItem');
    let itemCount = 1;

    // Add new item
    addItemBtn.addEventListener('click', function() {
        const newItem = document.createElement('div');
        newItem.className = 'invoice-item mt-3';
        newItem.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <input type="text" name="items[${itemCount}][description]" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <input type="number" name="items[${itemCount}][quantity]" class="form-control" required min="1" value="1">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <input type="number" name="items[${itemCount}][price]" class="form-control" required min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <input type="text" class="form-control item-total" readonly>
                        <button type="button" class="btn btn-sm btn-danger mt-2 remove-item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        invoiceItems.appendChild(newItem);
        itemCount++;
        updateTotals();
    });

    // Remove item
    invoiceItems.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.parentElement.classList.contains('remove-item')) {
            const item = e.target.closest('.invoice-item');
            item.remove();
            updateTotals();
        }
    });

    // Calculate totals
    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.invoice-item').forEach(item => {
            const quantity = parseFloat(item.querySelector('input[name$="[quantity]"]').value) || 0;
            const price = parseFloat(item.querySelector('input[name$="[price]"]').value) || 0;
            const total = quantity * price;
            item.querySelector('.item-total').value = total.toFixed(2) + ' €';
            subtotal += total;
        });

        const taxRate = <?php echo getPaymentSettings()['tax_rate']; ?> / 100;
        const tax = subtotal * taxRate;
        const total = subtotal + tax;

        document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' €';
        document.getElementById('tax').textContent = tax.toFixed(2) + ' €';
        document.getElementById('total').textContent = total.toFixed(2) + ' €';
    }

    // Update totals when quantity or price changes
    invoiceItems.addEventListener('input', function(e) {
        if (e.target.name && (e.target.name.includes('[quantity]') || e.target.name.includes('[price]'))) {
            updateTotals();
        }
    });
});
</script>

<?php require_once 'inc/footer.php'; ?> 