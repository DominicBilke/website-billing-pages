<?php
session_start();
$url = $_SESSION['url'];

$meldung = "meldung=Keine Daten eingetragen!";

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['geld_dbname'], $_SESSION['geld_dbuser'], $_SESSION['geld_dbpsw']);

if(isset($_SESSION['geld_id']) && isset($_POST['tourdaten']))
{

	$filenamejpg = 'Rechnung_'.date('Y_m_d_His').".".pathinfo($_FILES['datei']['name'], PATHINFO_EXTENSION);
	$filenamegpx = 'Zahlung_'.date('Y_m_d_His').".".pathinfo($_FILES['gpx']['name'], PATHINFO_EXTENSION);

	$statement = $pdo->prepare("INSERT INTO tourdaten (benutzer_id, tour, verteiler, datum, startzeit, dauer, pause, flyer, gebiet, arbeitszeit, monat, datei, projekt, gpx)  VALUES (?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

	$statement->execute(array($_SESSION['geld_id'], $_POST['tour'], $_POST['verteiler'], $_POST['datum'], $_POST['startzeit'], $_POST['dauer'],$_POST['pause'], $_POST['flyer'], $_POST['gebiet'], $_POST['arbeitszeit'], $_POST['monat'], $filenamejpg, $_POST['projekt'], $filenamegpx));

	/*echo "SQL Error <br />";
	echo $statement->queryString."<br />";
	echo $statement->errorInfo()[2];*/

	move_uploaded_file($_FILES['datei']['tmp_name'], '../geld_upload/'.$filenamejpg);
	move_uploaded_file($_FILES['gpx']['tmp_name'], '../geld_upload/'.$filenamegpx);

	$meldung = "meldung=Daten erfolgreich eingetragen!";

}

header("Location: ".$url.$meldung);
?>