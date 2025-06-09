<?php
require('script/inc.php');
check_auth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    $content = display_message('danger', 'Invalid entry ID.');
    require_once('templates/base.php');
    exit;
}

// Fetch current entry
$sql = "SELECT * FROM tour_billing WHERE id = $id";
$result = mysqli_query($conn, $sql);
if (!$row = mysqli_fetch_assoc($result)) {
    $content = display_message('danger', 'Entry not found.');
    require_once('templates/base.php');
    exit;
}

$message = '';
if (isset($_POST['submit'])) {
    $tour_name = sanitize_input($_POST['tour_name']);
    $project = sanitize_input($_POST['project']);
    $date = sanitize_input($_POST['date']);
    $hours = sanitize_input($_POST['hours']);
    $rate = sanitize_input($_POST['rate']);
    $description = sanitize_input($_POST['description']);
    $total = $hours * $rate;

    $update_sql = "UPDATE tour_billing SET tour_name='$tour_name', project='$project', date='$date', hours='$hours', rate='$rate', total='$total', description='$description' WHERE id=$id";
    if (mysqli_query($conn, $update_sql)) {
        $message = display_message('success', 'Entry updated successfully!');
    } else {
        $message = display_message('danger', 'Error: ' . mysqli_error($conn));
    }
    // Refresh row data
    $sql = "SELECT * FROM tour_billing WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
}

$page_title = 'Edit Tour Billing Entry';
ob_start();
?>
<div class="form-container card">
    <div class="card-header"><h4>Edit Tour Billing Entry</h4></div>
    <div class="card-body">
        <?php echo $message; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="tour_name" class="form-label">Tour Name</label>
                <input type="text" class="form-control" id="tour_name" name="tour_name" value="<?php echo htmlspecialchars($row['tour_name']); ?>" required>
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
                <input type="number" class="form-control calculate-total" id="hours" name="hours" min="0" step="0.01" value="<?php echo $row['hours']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="rate" class="form-label">Rate</label>
                <input type="number" class="form-control calculate-total" id="rate" name="rate" min="0" step="0.01" value="<?php echo $row['rate']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="total" class="form-label">Total</label>
                <input type="number" class="form-control" id="total" name="total" value="<?php echo $row['total']; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"><?php echo htmlspecialchars($row['description']); ?></textarea>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Update Entry</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once('templates/base.php'); 