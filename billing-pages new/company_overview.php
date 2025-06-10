<?php
require('script/inc.php');
check_auth();

// Pagination
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Search functionality
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$search_condition = $search ? "WHERE company_name LIKE '%$search%' OR project LIKE '%$search%'" : '';

// Get total records for pagination
$total_records_sql = "SELECT COUNT(*) as count FROM company_billing $search_condition";
$total_records_result = mysqli_query($conn, $total_records_sql);
$total_records = mysqli_fetch_assoc($total_records_result)['count'];
$total_pages = ceil($total_records / $records_per_page);

// Get records
$sql = "SELECT * FROM company_billing $search_condition ORDER BY date DESC LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $sql);

$page_title = 'Company Billing Overview';
ob_start();
?>
<div class="table-container card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Company Billing Overview</h4>
        <form class="d-flex search-form" id="searchForm" action="company_overview.php" method="get">
            <input class="form-control me-2" type="search" id="searchInput" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-outline-primary" type="submit">Search</button>
            <button class="btn btn-outline-secondary ms-2" id="clearSearch">Clear</button>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Company</th>
                        <th>Project</th>
                        <th>Hours</th>
                        <th>Rate</th>
                        <th>Total</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo format_date($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['project']); ?></td>
                            <td><?php echo $row['hours']; ?></td>
                            <td><?php echo format_currency($row['rate']); ?></td>
                            <td><?php echo format_currency($row['total']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <a href="company_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning btn-icon"><i class="fas fa-edit"></i></a>
                                <a href="company_delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger btn-icon delete-confirm"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <nav>
            <ul class="pagination">
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php if($search) echo '&search=' . urlencode($search); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once('templates/base.php'); 