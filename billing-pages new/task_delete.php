<?php
require('script/inc.php');
check_auth();

$page_title = 'Delete Task Billing Entry';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $content = display_message('Invalid entry ID.', 'danger');
    require_once('templates/base.php');
    exit;
}

$id = (int)$_GET['id'];

// Delete the entry
$stmt = mysqli_prepare($conn, "DELETE FROM task_billing WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    $content = display_message('Entry deleted successfully.', 'success');
    header("refresh:2;url=task_overview.php");
} else {
    $content = display_message('Error deleting entry: ' . mysqli_error($conn), 'danger');
}

mysqli_stmt_close($stmt);
require_once('templates/base.php'); 