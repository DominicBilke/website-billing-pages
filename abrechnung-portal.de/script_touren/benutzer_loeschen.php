<?php
session_start();
$url = $_SESSION['url'];

$meldung = "meldung=Keine Daten gelöscht!";

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpsw']);

if(isset($_SESSION['id']) && isset($_POST['benutzer_loeschen']))
{
$statement = $pdo->prepare("DELETE FROM benutzer  WHERE id = :id");

$statement->execute(array('id'=> $_POST['id'])); 

	/*echo "<br /><br />SQL Error <br />";
	echo $statement->queryString."<br />";
	echo $statement->errorInfo()[2]; */

$statement = $pdo->prepare("DELETE FROM benutzerdaten  WHERE benutzer_id = :id");

$statement->execute(array('id'=> $_POST['id'])); 

	/*echo "<br /><br />SQL Error <br />";
	echo $statement->queryString."<br />";
	echo $statement->errorInfo()[2]; */

$meldung = "meldung=Benutzer gelöscht!";

}


header("Location: ".$url.$meldung);
?>