<?php
require('script/inc.php');
check_auth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    $content = display_message('danger', 'Invalid entry ID.');
    require_once('templates/base.php');
    exit;
}

$sql = "DELETE FROM company_billing WHERE id = $id";
if (mysqli_query($conn, $sql)) {
    $content = display_message('success', 'Entry deleted successfully! Redirecting...');
    echo "<meta http-equiv='refresh' content='1;url=company_overview.php'>";
} else {
    $content = display_message('danger', 'Error deleting entry. Redirecting...');
    echo "<meta http-equiv='refresh' content='2;url=company_overview.php'>";
}
require_once('templates/base.php'); 