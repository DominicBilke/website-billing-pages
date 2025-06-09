<?php
require('script/inc.php');
check_auth();

$page_title = 'Add Money Billing Entry';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $money_name = sanitize_input($_POST['money_name']);
    $project = sanitize_input($_POST['project']);
    $date = sanitize_input($_POST['date']);
    $hours = floatval($_POST['hours']);
    $rate = floatval($_POST['rate']);
    $total = $hours * $rate;
    $description = sanitize_input($_POST['description']);

    $sql = "INSERT INTO money_billing (money_name, project, date, hours, rate, total, description) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssddds", $money_name, $project, $date, $hours, $rate, $total, $description);
    
    if (mysqli_stmt_execute($stmt)) {
        redirect_with_message('money_overview.php', 'Money billing entry added successfully', 'success');
    } else {
        $error = "Error adding money billing entry: " . mysqli_error($conn);
    }
}

ob_start();
?>
<div class="card">
    <div class="card-header">
        <h4>Add Money Billing Entry</h4>
    </div>
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="money_name" class="form-label">Money Name</label>
                <input type="text" class="form-control" id="money_name" name="money_name" required>
            </div>

            <div class="mb-3">
                <label for="project" class="form-label">Project</label>
                <input type="text" class="form-control" id="project" name="project" required>
            </div>

            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>

            <div class="mb-3">
                <label for="hours" class="form-label">Hours</label>
                <input type="number" step="0.01" class="form-control" id="hours" name="hours" required>
            </div>

            <div class="mb-3">
                <label for="rate" class="form-label">Rate</label>
                <input type="number" step="0.01" class="form-control" id="rate" name="rate" required>
            </div>

            <div class="mb-3">
                <label for="total" class="form-label">Total</label>
                <input type="text" class="form-control" id="total" readonly>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Add Entry</button>
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
        totalInput.value = (hours * rate).toFixed(2);
    }

    hoursInput.addEventListener('input', calculateTotal);
    rateInput.addEventListener('input', calculateTotal);
});
</script>
<?php
$content = ob_get_clean();
require_once('templates/base.php'); 