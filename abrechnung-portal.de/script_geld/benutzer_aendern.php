<?php
session_start();
$url = $_SESSION['url'];

$meldung = "meldung=Keine Daten eingetragen!";

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpsw']);

if(isset($_SESSION['id']) && isset($_POST['benutzer_aendern']))
{

	$statement = $pdo->prepare("UPDATE benutzer SET benutzer=?, pswd=?, status=? WHERE id=?");

	$statement->execute(array($_POST['benutzer'], $_POST['pswd'], $_POST['status'], $_POST['id']));

	/*echo "<br /><br />SQL Error 1<br />";
	echo $statement->queryString."<br />";
	echo $statement->errorInfo()[2];*/ 

	//echo "<br /><br />".$sql."<br />";

	$statement = $pdo->prepare("UPDATE benutzerdaten SET lohn=?, nachname=?, plz_ort=?, strasse_nr=?, vorname=? WHERE benutzer_id=?");

	$statement->execute(array($_POST['lohn'], $_POST['nachname'], $_POST['plz_ort'], $_POST['strasse_nr'], $_POST['vorname'], $_POST['id']));
	
	/*echo "<br /><br />SQL Error 3<br />";
	echo $statement->queryString."<br />";
	echo $statement->errorInfo()[2];*/ 
	
	$meldung = "meldung=Benutzer geändert!";
}

//echo '<br/><br/><a href="http://www.abrechnungstool.de/inhalt.php">Zurueck</a>';

header("Location: ".$url.$meldung);
?>