<?php
require_once __DIR__ . '/../../inc/header.php';
require_once __DIR__ . '/../../inc/db.php';

$error = '';
$success = '';

// Get clients for dropdown
$clients = $pdo->query("SELECT id, name FROM clients ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_POST['client_id'] ?? '';
    $invoice_date = $_POST['invoice_date'] ?? '';
    $due_date = $_POST['due_date'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $items = $_POST['items'] ?? [];

    if (empty($client_id) || empty($invoice_date) || empty($due_date) || empty($items)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $pdo->beginTransaction();

            // Generate invoice number
            $year = date('Y');
            $stmt = $pdo->query("SELECT MAX(number) as max_num 
                                FROM invoices 
                                WHERE invoice_number LIKE 'INV-$year-%'");
            $result = $stmt->fetch();
            $next_num = ($result['max_num'] ?? 0) + 1;
            $invoice_number = sprintf('INV-%s-%04d', $year, $next_num);

            // Calculate totals
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }
            $tax_rate = 0.19; // 19% VAT
            $tax_amount = $subtotal * $tax_rate;
            $total_amount = $subtotal + $tax_amount;

            // Insert invoice
            $stmt = $pdo->prepare("
                INSERT INTO invoices (
                    user_id, number, invoice_number, client_id, date, due_date,
                    subtotal, tax_rate, tax_amount, total_amount,
                    notes, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'unpaid', NOW())
            ");

            $stmt->execute([
                $_SESSION['user_id'], $next_num, $invoice_number, $client_id, $invoice_date, $due_date,
                $subtotal, $tax_rate, $tax_amount, $total_amount,
                $notes
            ]);

            $invoice_id = $pdo->lastInsertId();

            // Insert invoice items
            $stmt = $pdo->prepare("
                INSERT INTO invoice_items (
                    invoice_id, description, quantity, unit_price, amount
                ) VALUES (?, ?, ?, ?, ?)
            ");

            foreach ($items as $item) {
                $amount = $item['quantity'] * $item['unit_price'];
                $stmt->execute([
                    $invoice_id,
                    $item['description'],
                    $item['quantity'],
                    $item['unit_price'],
                    $amount
                ]);
            }

            $pdo->commit();
            $success = 'Invoice created successfully!';
            
            // Redirect to view page
            header("Location: view.php?id=$invoice_id");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'An error occurred: ' . $e->getMessage();
        }
    }
}
?>

<div class="container">
    <div class="page-header">
        <h1>Create Invoice</h1>
        <div class="header-actions">
            <a href="/invoices" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Invoices
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" class="form" id="invoice-form">
                <div class="form-section">
                    <h3>Invoice Details</h3>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="client_id">Client *</label>
                            <select id="client_id" name="client_id" class="form-control" required>
                                <option value="">Select Client</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?php echo $client['id']; ?>"
                                            <?php echo isset($_POST['client_id']) && $_POST['client_id'] == $client['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($client['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="invoice_date">Invoice Date *</label>
                            <input type="date" id="invoice_date" name="invoice_date" class="form-control" required
                                   value="<?php echo htmlspecialchars($_POST['invoice_date'] ?? date('Y-m-d')); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="due_date">Due Date *</label>
                            <input type="date" id="due_date" name="due_date" class="form-control" required
                                   value="<?php echo htmlspecialchars($_POST['due_date'] ?? date('Y-m-d', strtotime('+30 days'))); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Invoice Items</h3>
                    <div id="invoice-items">
                        <div class="invoice-item">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Description *</label>
                                    <input type="text" name="items[0][description]" class="form-control" required>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Quantity *</label>
                                    <input type="number" name="items[0][quantity]" class="form-control item-quantity" required min="1" value="1">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Unit Price *</label>
                                    <input type="number" name="items[0][unit_price]" class="form-control item-price" required min="0" step="0.01" value="0.00">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Amount</label>
                                    <input type="text" class="form-control item-amount" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline" id="add-item">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>

                <div class="form-section">
                    <h3>Totals</h3>
                    <div class="totals-grid">
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span id="subtotal">0.00 €</span>
                        </div>
                        <div class="total-row">
                            <span>Tax (19%):</span>
                            <span id="tax-amount">0.00 €</span>
                        </div>
                        <div class="total-row total">
                            <span>Total:</span>
                            <span id="total-amount">0.00 €</span>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Additional Information</h3>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="4"><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Create Invoice</button>
                    <a href="index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const invoiceItems = document.getElementById('invoice-items');
    const addItemButton = document.getElementById('add-item');
    let itemCount = 1;

    function updateItemAmount(item) {
        const quantity = parseFloat(item.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(item.querySelector('.item-price').value) || 0;
        const amount = quantity * price;
        item.querySelector('.item-amount').value = amount.toFixed(2) + ' €';
        updateTotals();
    }

    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.invoice-item').forEach(item => {
            const amount = parseFloat(item.querySelector('.item-amount').value) || 0;
            subtotal += amount;
        });

        const taxRate = 0.19;
        const taxAmount = subtotal * taxRate;
        const total = subtotal + taxAmount;

        document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' €';
        document.getElementById('tax-amount').textContent = taxAmount.toFixed(2) + ' €';
        document.getElementById('total-amount').textContent = total.toFixed(2) + ' €';
    }

    function addItem() {
        const template = `
            <div class="invoice-item">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Description *</label>
                        <input type="text" name="items[${itemCount}][description]" class="form-control" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Quantity *</label>
                        <input type="number" name="items[${itemCount}][quantity]" class="form-control item-quantity" required min="1" value="1">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Unit Price *</label>
                        <input type="number" name="items[${itemCount}][unit_price]" class="form-control item-price" required min="0" step="0.01" value="0.00">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Amount</label>
                        <input type="text" class="form-control item-amount" readonly>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-item">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
        `;

        invoiceItems.insertAdjacentHTML('beforeend', template);
        itemCount++;

        // Add event listeners to new item
        const newItem = invoiceItems.lastElementChild;
        newItem.querySelector('.item-quantity').addEventListener('input', () => updateItemAmount(newItem));
        newItem.querySelector('.item-price').addEventListener('input', () => updateItemAmount(newItem));
        newItem.querySelector('.remove-item').addEventListener('click', () => {
            newItem.remove();
            updateTotals();
        });
    }

    // Add event listeners to initial item
    const initialItem = invoiceItems.querySelector('.invoice-item');
    initialItem.querySelector('.item-quantity').addEventListener('input', () => updateItemAmount(initialItem));
    initialItem.querySelector('.item-price').addEventListener('input', () => updateItemAmount(initialItem));

    // Add event listener to add item button
    addItemButton.addEventListener('click', addItem);
});
</script>

<?php require_once __DIR__ . '/../../inc/footer.php'; ?> 