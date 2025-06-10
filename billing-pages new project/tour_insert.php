<?php
require('script/inc.php');
check_auth();

$message = '';
if(isset($_POST['submit'])) {
    $tour_name = sanitize_input($_POST['tour_name']);
    $project = sanitize_input($_POST['project']);
    $date = sanitize_input($_POST['date']);
    $hours = sanitize_input($_POST['hours']);
    $rate = sanitize_input($_POST['rate']);
    $description = sanitize_input($_POST['description']);
    $total = $hours * $rate;
    $sql = "INSERT INTO tour_billing (tour_name, project, date, hours, rate, total, description, user_id) VALUES ('$tour_name', '$project', '$date', '$hours', '$rate', '$total', '$description', '{$_SESSION['id']}')";
    if(mysqli_query($conn, $sql)) {
        $message = display_message('success', 'Entry added successfully!');
    } else {
        $message = display_message('danger', 'Error: ' . mysqli_error($conn));
    }
}

$page_title = 'Add Tour Billing Entry';
ob_start();
?>
<div class="form-container card">
    <div class="card-header"><h4>Add Tour Billing Entry</h4></div>
    <div class="card-body">
        <?php echo $message; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="tour_name" class="form-label">Tour Name</label>
                <input type="text" class="form-control" id="tour_name" name="tour_name" required>
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
                <input type="number" class="form-control calculate-total" id="hours" name="hours" min="0" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="rate" class="form-label">Rate</label>
                <input type="number" class="form-control calculate-total" id="rate" name="rate" min="0" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="total" class="form-label">Total</label>
                <input type="number" class="form-control" id="total" name="total" readonly>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Add Entry</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once('templates/base.php'); 