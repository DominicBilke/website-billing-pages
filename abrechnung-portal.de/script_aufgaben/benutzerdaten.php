<?php
session_start();
$url = $_SESSION['url'];

$meldung = "meldung=Keine Daten aktualisiert!";

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpsw']);

if(isset($_SESSION['id']) && isset($_POST['benutzerdaten']))
{
$statement = $pdo->prepare("UPDATE benutzerdaten SET nachname = :nachname, plz_ort = :plz_ort , strasse_nr = :strasse_nr, vorname = :vorname  WHERE benutzer_id = :id");

$statement->execute(array('id'=> $_SESSION['id'], 'nachname' => $_POST['nachname'],'plz_ort' => $_POST['plz_ort'], 'strasse_nr' => $_POST['strasse_nr'], 'vorname' => $_POST['vorname'])); 

	/*echo "<br /><br />SQL Error <br />";
	echo $statement->queryString."<br />";
	echo $statement->errorInfo()[2]; */

$meldung = "meldung=Benutzerdaten aktualisiert!!";
}

//echo '<br/><br/><a href="http://www.abrechnungstool.de/inhalt.php">Zurueck</a>';

header("Location: ".$url.$meldung);
?>