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