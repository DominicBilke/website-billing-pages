<?php
require('script/inc.php');
check_auth();

$page_title = 'Task Billing Overview';

// Get search parameters
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Build search condition
$search_condition = '';
if (!empty($search)) {
    $search_condition = "WHERE task_name LIKE '%$search%' OR project LIKE '%$search%' OR description LIKE '%$search%'";
}

// Get total records for pagination
$total_records_sql = "SELECT COUNT(*) as count FROM task_billing $search_condition";
$total_records_result = mysqli_query($conn, $total_records_sql);
$total_records = mysqli_fetch_assoc($total_records_result)['count'];
$total_pages = ceil($total_records / $per_page);

// Get records for current page
$sql = "SELECT * FROM task_billing $search_condition ORDER BY date DESC LIMIT $offset, $per_page";
$result = mysqli_query($conn, $sql);

ob_start();
?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Task Billing Overview</h4>
        <a href="task_insert.php" class="btn btn-primary">Add New Entry</a>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search by task name, project, or description..." value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="task_overview.php" class="btn btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Task Name</th>
                            <th>Project</th>
                            <th>Date</th>
                            <th>Hours</th>
                            <th>Rate</th>
                            <th>Total</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['task_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['project']); ?></td>
                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                                <td><?php echo htmlspecialchars($row['hours']); ?></td>
                                <td><?php echo format_currency($row['rate']); ?></td>
                                <td><?php echo format_currency($row['total']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="task_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="task_delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this entry?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">
                No task billing entries found.
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once('templates/base.php'); 