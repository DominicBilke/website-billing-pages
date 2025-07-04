<?php
session_start();

    $url = (empty($_SERVER['HTTPS'])) ? 'http://' : 'https://';
    $url .= $_SERVER['HTTP_HOST'];
    $url .= $_SERVER['REQUEST_URI']; // $url enthält jetzt die komplette URL
    if(strpos($url, "?"))
	    $url .= "&";
    else
   	    $url .= "?";
    $_SESSION['url'] = $url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Geldabrechnung.de</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../bootstrap513/css/bootstrap.min.css" rel="stylesheet">
  <script src="../bootstrap513/js/bootstrap.bundle.min.js"></script>
		<style>
			.map {width:100%;height:100vh}
			@media screen and (min-width:700px) {
				.map { display:inline-block; width: 72%; width:calc(75% - 25px); height:95vh; height:calc(100vh - 10px); margin:0; padding:0 }
			}
		</style>
</head>

<body class="bg-light">
