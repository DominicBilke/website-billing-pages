<?php
session_start();
$url = $_SESSION['url'];

$meldung = "meldung=Keine Daten eingetragen!";

$pdo = new PDO('mysql:host=localhost;dbname=d03a0216', 'd03a0216', 'vTFFs9PD8UgNz6rm');

if(isset($_POST['neue_domain']))
{

	$statement = $pdo->prepare("INSERT INTO firmendaten (firma, person, strasse_nr, plz_ort, iban, bic, inhaber, benutzer, psw, mail)  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

	$statement->execute(array($_POST['firma'], $_POST['person'], $_POST['strasse_nr'], $_POST['plz_ort'], $_POST['iban'], $_POST['bic'], $_POST['inhaber'], 'admin', 'Abrechnung', 'info@abrechnungstool.de'));

	//echo "<br /><br />SQL Error 1<br />";
	//echo $statement->queryString."<br />";
	//echo $statement->errorInfo()[2]; 

	$sql = "SELECT id FROM firmendaten WHERE firma='".$_POST['firma']."' AND person='".$_POST['person']."'";

	foreach ($pdo->query($sql) as $row) { $firmen_id = $row['id']; }

	//echo "<br /><br />".$sql."<br />";

	$statement = $pdo->prepare("INSERT INTO domaindaten (domain, datenbank, benutzer, psw, firmen_id)  VALUES (?, ?, ?, ?, ?)");

	$statement->execute(array($_POST['domain'], $_POST['datenbank'], $_POST['benutzer'], $_POST['psw'], $firmen_id));
	
	//echo "<br /><br />SQL Error 3<br />";
	//echo $statement->queryString."<br />";
	//echo $statement->errorInfo()[2]; 

	$meldung = "meldung=Neue Domain eingetragen!";
}

//echo '<br/><br/><a href="http://www.abrechnungstool.de/inhalt.php">Zurueck</a>';

// header("Location: ".$url.$meldung);
?>