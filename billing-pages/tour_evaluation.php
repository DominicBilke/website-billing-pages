<?php
require('script/inc.php');
check_auth();

// Get summary data
$sql = "SELECT tour_name, SUM(total) as total_billed, COUNT(*) as entry_count FROM tour_billing GROUP BY tour_name ORDER BY total_billed DESC";
$result = mysqli_query($conn, $sql);

$total_billed = get_total_billed('tour_billing');

$page_title = 'Tour Billing Evaluation';
ob_start();
?>
<div class="card mb-4">
    <div class="card-header"><h4>Tour Billing Evaluation</h4></div>
    <div class="card-body">
        <div class="alert alert-info">Total billed across all tours: <strong><?php echo format_currency($total_billed); ?></strong></div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tour</th>
                        <th>Number of Entries</th>
                        <th>Total Billed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['tour_name']); ?></td>
                            <td><?php echo $row['entry_count']; ?></td>
                            <td><?php echo format_currency($row['total_billed']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="chart-container text-center">
            <img src="tour_evaluation_chart.php" alt="Tour Billing Chart" class="img-fluid">
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once('templates/base.php'); 