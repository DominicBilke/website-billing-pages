<?php
session_start();
$url = $_SESSION['url'];

$meldung = "meldung=Keine Daten gelöscht!";

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['firmen_dbname'], $_SESSION['firmen_dbuser'], $_SESSION['firmen_dbpsw']);

if(isset($_SESSION['firmen_id']) && isset($_POST['tour_loeschen']))
{
$statement = $pdo->prepare("DELETE FROM tourdaten  WHERE id = :id");

$statement->execute(array('id'=> $_POST['id'])); 

	/*echo "<br /><br />SQL Error <br />";
	echo $statement->queryString."<br />";
	echo $statement->errorInfo()[2]; */

$datei = "../upload/".$_POST['datei'];
$gpx = "../upload/".$_POST['gpx'];

/*if (file_exists($datei)) 
    $success1 = unlink($datei);

if (file_exists($gpx)) 
    $success2 = unlink($gpx);*/

$meldung = "meldung=Daten gelöscht!";
}

//echo '<br/><br/><a href="http://www.abrechnungstool.de/inhalt.php">Zurueck</a>';

header("Location: ".$url.$meldung);
?>