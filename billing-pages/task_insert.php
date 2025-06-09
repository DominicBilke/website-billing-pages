<?php
require('script/inc.php');
check_auth();

$page_title = 'Add Task Billing Entry';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_name = sanitize_input($_POST['task_name']);
    $project = sanitize_input($_POST['project']);
    $date = sanitize_input($_POST['date']);
    $hours = (float)$_POST['hours'];
    $rate = (float)$_POST['rate'];
    $total = $hours * $rate;
    $description = sanitize_input($_POST['description']);

    $stmt = mysqli_prepare($conn, "INSERT INTO task_billing (task_name, project, date, hours, rate, total, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssdddss", $task_name, $project, $date, $hours, $rate, $total, $description);
    
    if (mysqli_stmt_execute($stmt)) {
        $content = display_message('Entry added successfully.', 'success');
        header("refresh:2;url=task_overview.php");
    } else {
        $content = display_message('Error adding entry: ' . mysqli_error($conn), 'danger');
    }
    mysqli_stmt_close($stmt);
}

ob_start();
?>
<div class="card">
    <div class="card-header">
        <h4>Add Task Billing Entry</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label for="task_name" class="form-label">Task Name</label>
                <input type="text" class="form-control" id="task_name" name="task_name" required>
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
            <a href="task_overview.php" class="btn btn-secondary">Cancel</a>
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
require_once('templates/base.php'); 