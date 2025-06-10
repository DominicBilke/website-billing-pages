<?php
require_once __DIR__ . '/../../inc/header.php';
require_once __DIR__ . '/../../inc/db.php';

// Get filter parameters
$status = $_GET['status'] ?? 'all';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$client_id = $_GET['client_id'] ?? '';

// Build query
$query = "
    SELECT i.*, c.name as client_name 
    FROM invoices i 
    JOIN clients c ON i.client_id = c.id 
    WHERE 1=1
";
$params = [];

if ($status !== 'all') {
    $query .= " AND i.status = ?";
    $params[] = $status;
}

if ($date_from) {
    $query .= " AND i.created_at >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $query .= " AND i.created_at <= ?";
    $params[] = $date_to;
}

if ($client_id) {
    $query .= " AND i.client_id = ?";
    $params[] = $client_id;
}

$query .= " ORDER BY i.created_at DESC";

// Get invoices
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$invoices = $stmt->fetchAll();

// Get clients for filter
$clients = $pdo->query("SELECT id, name FROM clients ORDER BY name")->fetchAll();
?>

<div class="container">
    <div class="page-header">
        <h1>Invoices</h1>
        <div class="header-actions">
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Invoice
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Filter Invoices</h2>
        </div>
        <div class="card-body">
            <form method="GET" class="filter-form">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                            <option value="paid" <?php echo $status === 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="unpaid" <?php echo $status === 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                            <option value="sent" <?php echo $status === 'sent' ? 'selected' : ''; ?>>Sent</option>
                            <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option>

                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="client_id">Client</label>
                        <select id="client_id" name="client_id" class="form-control">
                            <option value="">All Clients</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client['id']; ?>" 
                                        <?php echo $client_id == $client['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($client['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="date_from">Date From</label>
                        <input type="date" id="date_from" name="date_from" class="form-control"
                               value="<?php echo htmlspecialchars($date_from); ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="date_to">Date To</label>
                        <input type="date" id="date_to" name="date_to" class="form-control"
                               value="<?php echo htmlspecialchars($date_to); ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="index.php" class="btn btn-outline">Clear Filters</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <?php if (empty($invoices)): ?>
                <div class="empty-state">
                    <i class="fas fa-file-invoice"></i>
                    <h3>No Invoices Found</h3>
                    <p>Start by creating your first invoice.</p>
                    <a href="create.php" class="btn btn-primary">Create Invoice</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $invoice): ?>
                                <tr>
                                    <td>
                                        <a href="view.php?id=<?php echo $invoice['id']; ?>" class="invoice-number">
                                            <?php echo htmlspecialchars($invoice['invoice_number']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($invoice['client_name']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($invoice['created_at'])); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($invoice['due_date'])); ?></td>
                                    <td><?php echo number_format($invoice['total_amount'], 2); ?> </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $invoice['status']; ?>">
                                            <?php echo ucfirst($invoice['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="view.php?id=<?php echo $invoice['id']; ?>" class="btn btn-sm btn-view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $invoice['id']; ?>" class="btn btn-sm btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="pdf.php?id=<?php echo $invoice['id']; ?>" class="btn btn-sm btn-primary" title="Download PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
<a href="delete.php?id=<?php echo $invoice['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this invoice?');">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../../inc/footer.php'; ?> 