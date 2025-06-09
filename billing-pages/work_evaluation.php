<?php
require('script/inc.php');
check_auth();

$page_title = 'Work Billing Evaluation';

// Get work billing summary
$sql = "SELECT work_name, SUM(total) as total_billed, COUNT(*) as entry_count 
        FROM work_billing 
        GROUP BY work_name 
        ORDER BY total_billed DESC";
$result = mysqli_query($conn, $sql);

// Get total billed amount
$total_billed = get_total_billed('work_billing');

ob_start();
?>
<div class="card">
    <div class="card-header">
        <h4>Work Billing Evaluation</h4>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-4">
            <h5 class="alert-heading">Total Billed Amount</h5>
            <p class="mb-0"><?php echo format_currency($total_billed); ?></p>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Work Name</th>
                            <th>Total Billed</th>
                            <th>Entry Count</th>
                            <th>Average per Entry</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['work_name']); ?></td>
                                <td><?php echo format_currency($row['total_billed']); ?></td>
                                <td><?php echo $row['entry_count']; ?></td>
                                <td><?php echo format_currency($row['total_billed'] / $row['entry_count']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                No work billing entries found.
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once('templates/base.php'); 