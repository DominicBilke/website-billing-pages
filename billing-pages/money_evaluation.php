<?php
require('script/inc.php');
check_auth();

$page_title = 'Money Billing Evaluation';

// Get money billing summary
$sql = "SELECT money_name, SUM(total) as total_billed, COUNT(*) as entry_count 
        FROM money_billing 
        GROUP BY money_name 
        ORDER BY total_billed DESC";
$result = mysqli_query($conn, $sql);

// Get total billed amount
$total_billed = get_total_billed('money_billing');

ob_start();
?>
<div class="card">
    <div class="card-header">
        <h4>Money Billing Evaluation</h4>
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
                            <th>Money Name</th>
                            <th>Total Billed</th>
                            <th>Entry Count</th>
                            <th>Average per Entry</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['money_name']); ?></td>
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
                No money billing entries found.
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once('templates/base.php'); 