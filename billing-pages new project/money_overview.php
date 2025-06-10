<?php
require('script/inc.php');
check_auth();

$page_title = 'Money Billing Overview';

// Get search parameters
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = RECORDS_PER_PAGE;
$offset = ($page - 1) * $records_per_page;

// Build search condition
$search_condition = get_search_condition($search, ['money_name', 'project', 'description']);

// Get total records for pagination
$count_sql = "SELECT COUNT(*) as total FROM money_billing WHERE $search_condition";
$count_result = mysqli_query($conn, $count_sql);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get records for current page
$sql = "SELECT * FROM money_billing WHERE $search_condition ORDER BY date DESC LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $sql);

ob_start();
?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Money Billing Overview</h4>
        <a href="money_insert.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Entry</a>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search money name, project, or description..." value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i> Search</button>
                <?php if ($search): ?>
                    <a href="money_overview.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i> Clear</a>
                <?php endif; ?>
            </div>
        </form>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Money Name</th>
                            <th>Project</th>
                            <th>Hours</th>
                            <th>Rate</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo format_date($row['date']); ?></td>
                                <td><?php echo htmlspecialchars($row['money_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['project']); ?></td>
                                <td><?php echo $row['hours']; ?></td>
                                <td><?php echo format_currency($row['rate']); ?></td>
                                <td><?php echo format_currency($row['total']); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="money_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                        <a href="money_delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this entry?')"><i class="fas fa-trash"></i></a>
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
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-info">
                <?php echo $search ? 'No entries found matching your search criteria.' : 'No money billing entries found.'; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once('templates/base.php'); 