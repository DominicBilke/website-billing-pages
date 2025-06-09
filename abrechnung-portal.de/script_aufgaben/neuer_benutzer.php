<?php
session_start();
$url = $_SESSION['url'];

$meldung = "meldung=Keine Daten eingetragen!";

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpsw']);

if(isset($_SESSION['id']) && isset($_POST['neuer_benutzer']))
{

	$statement = $pdo->prepare("INSERT INTO benutzer (benutzer, pswd, status)  VALUES (?, ?, ?)");

	$statement->execute(array($_POST['benutzer'], $_POST['pswd'], $_POST['status']));

	/*echo "<br /><br />SQL Error 1<br />";
	echo $statement->queryString."<br />";
	echo $statement->errorInfo()[2]; */

	$sql = "SELECT id FROM benutzer WHERE benutzer='".$_POST['benutzer']."' AND pswd='".$_POST['pswd']."'";

	foreach ($pdo->query($sql) as $row) { $benutzer_id = $row['id']; }

	//echo "<br /><br />".$sql."<br />";

	$statement = $pdo->prepare("INSERT INTO benutzerdaten (benutzer_id, lohn, nachname, plz_ort, strasse_nr, vorname)  VALUES (?, ?, ?, ?, ?, ?)");

	$statement->execute(array($benutzer_id, $_POST['lohn'], $_POST['nachname'], $_POST['plz_ort'], $_POST['strasse_nr'], $_POST['vorname']));
	
	/*echo "<br /><br />SQL Error 3<br />";
	echo $statement->queryString."<br />";
	echo $statement->errorInfo()[2]; */

	$meldung = "meldung=Neuen Benutzer eingetragen!";
}

//echo '<br/><br/><a href="http://www.abrechnungstool.de/inhalt.php">Zurueck</a>';

header("Location: ".$url.$meldung);
?>