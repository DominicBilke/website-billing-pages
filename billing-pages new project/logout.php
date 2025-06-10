<?php
require_once 'script/config.php';
require_once 'script/auth.php';

// Log out the user
$auth->logout();

// Redirect to login page
header('Location: login.php');
exit;
?> 