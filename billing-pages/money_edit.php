<?php
require('script/inc.php');
check_auth();

$page_title = 'Edit Money Billing Entry';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $content = display_message('Invalid entry ID.', 'danger');
    require_once('templates/base.php');
    exit;
}

$id = (int)$_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $money_name = sanitize_input($_POST['money_name']);
    $project = sanitize_input($_POST['project']);
    $date = sanitize_input($_POST['date']);
    $hours = (float)$_POST['hours'];
    $rate = (float)$_POST['rate'];
    $total = $hours * $rate;
    $description = sanitize_input($_POST['description']);

    $stmt = mysqli_prepare($conn, "UPDATE money_billing SET money_name=?, project=?, date=?, hours=?, rate=?, total=?, description=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sssdddssi", $money_name, $project, $date, $hours, $rate, $total, $description, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $content = display_message('Entry updated successfully.', 'success');
        header("refresh:2;url=money_overview.php");
    } else {
        $content = display_message('Error updating entry: ' . mysqli_error($conn), 'danger');
    }
    mysqli_stmt_close($stmt);
}

// Get current entry
$stmt = mysqli_prepare($conn, "SELECT * FROM money_billing WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    ob_start();
    ?>
    <div class="card">
        <div class="card-header">
            <h4>Edit Money Billing Entry</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="money_name" class="form-label">Money Name</label>
                    <input type="text" class="form-control" id="money_name" name="money_name" value="<?php echo htmlspecialchars($row['money_name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="project" class="form-label">Project</label>
                    <input type="text" class="form-control" id="project" name="project" value="<?php echo htmlspecialchars($row['project']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($row['date']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="hours" class="form-label">Hours</label>
                    <input type="number" step="0.01" class="form-control" id="hours" name="hours" value="<?php echo htmlspecialchars($row['hours']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="rate" class="form-label">Rate</label>
                    <input type="number" step="0.01" class="form-control" id="rate" name="rate" value="<?php echo htmlspecialchars($row['rate']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="total" class="form-label">Total</label>
                    <input type="text" class="form-control" id="total" value="<?php echo format_currency($row['total']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($row['description']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Entry</button>
                <a href="money_overview.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const hoursInput = document.getElementById('hours');
        const rateInput = document.getElementById('rate');
        const totalInput = document.getElementById('total');

        function calculateTotal() {
            const hours = parseFloat(hoursInput.value) || 0;
            const rate = parseFloat(rateInput.value) || 0;
            const total = hours * rate;
            totalInput.value = new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(total);
        }

        hoursInput.addEventListener('input', calculateTotal);
        rateInput.addEventListener('input', calculateTotal);
    });
    </script>
    <?php
    $content = ob_get_clean();
} else {
    $content = display_message('Entry not found.', 'danger');
}

mysqli_stmt_close($stmt);
require_once('templates/base.php'); 