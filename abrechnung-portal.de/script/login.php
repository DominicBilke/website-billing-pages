<?php
session_start();

if(isset($_POST['logout']))
{
	$url=$_SESSION['url'];
	session_destroy();
	header("Location: ".$url);
        exit;
}

require('../script_arbeit/login.php');
require('../script_geld/login.php');
require('../script_aufgaben/login.php');
require('../script_touren/login.php');
require('../script_firma/login.php');

header('Location: https://www.abrechnung-portal.de/');

?>