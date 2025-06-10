<?php
require_once __DIR__ . '/../../inc/header.php';
require_once __DIR__ . '/../../inc/db.php';

// Get all clients
$stmt = $pdo->query("
    SELECT c.*, 
           COUNT(i.id) as total_invoices,
           SUM(i.total_amount) as total_paid
    FROM clients c
    LEFT JOIN invoices i ON c.id = i.client_id
    GROUP BY c.id
    ORDER BY c.name ASC
");
$clients = $stmt->fetchAll();
?>

<div class="container">
    <div class="page-header">
        <h1>Client Management</h1>
        <div class="header-actions">
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Client
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (empty($clients)): ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>No Clients Found</h3>
                    <p>Start by adding your first client.</p>
                    <a href="create.php" class="btn btn-primary">Add Client</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Client Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Total Invoices</th>
                                <th>Total Paid</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                                <tr>
                                    <td>
                                        <a href="edit.php?id=<?php echo $client['id']; ?>" class="client-name">
                                            <?php echo htmlspecialchars($client['name']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($client['email']); ?></td>
                                    <td><?php echo htmlspecialchars($client['phone']); ?></td>
                                    <td><?php echo number_format($client['total_invoices']); ?></td>
                                    <td><?php echo number_format($client['total_paid'], 2); ?> €</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit.php?id=<?php echo $client['id']; ?>" class="btn btn-sm btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/invoices/create.php?client_id=<?php echo $client['id']; ?>" class="btn btn-sm btn-primary" title="Create Invoice">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
<a href="delete.php?id=<?php echo $client['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this client?');">Delete</a>
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