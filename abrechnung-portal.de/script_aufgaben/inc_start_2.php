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
echo '
<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="index,follow" />
	<meta name = "keywords" content = "Aufgabenabrechnung, Abrechnung, Aufgaben, Nachweis, Auswertung, PDF, Benutzerverwaltung" />
	<meta http-equiv="content-type" content="text/html; charset=UFT-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Die Aufgabenabrechnung kann für jede Firma separat unter einer Domain eingerichtet werden. Es beinhaltet die Eingabe, Darstellung und Auswertung von Aufgaben. Aufgaben können mit Bildnachweis hochgeladen werden und ausgewertet werden. Diese können gefiltert werden. Eine Abrechnung in Form von PDF ist möglich. Die PDF-Abrechnung kann als Nachweis von Aufgaben weitergereicht werden. Es können Benutzer, Admins und Firmen-Logins eingerichtet werden. Die Admin- und Firmen-Logins haben Zugriff auf alle Aufgaben. Die Benutzer können nur die Eigenen verwalten. Alle Logins können sich anmelden und Ihre Daten eingeben, ansehen und auswerten.">
	<link rel="stylesheet" href="../css/standard.css" id="stylesheet">
	<link rel="alternate stylesheet" href="../css/classic.css" title="standard">
	<script src="../js/stylesheet-wechsler.js"></script>
	<title>Aufgabenabrechnung.de</title>
</head>';
?>