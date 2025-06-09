<?php
session_start();
$url = $_SESSION['url'];

$meldung = "meldung=Keine Daten gelöscht!";

$pdo = new PDO('mysql:host=localhost;dbname=d03a0216', 'd03a0216', 'vTFFs9PD8UgNz6rm');

if(isset($_SESSION['id']) && isset($_POST['benutzer_loeschen']))
{
$statement = $pdo->prepare("DELETE FROM firmendatendaten  WHERE id = :id");

$statement->execute(array('id'=> $_POST['id'])); 

	/*echo "<br /><br />SQL Error <br />";
	echo $statement->queryString."<br />";
	echo $statement->errorInfo()[2]; */

$statement = $pdo->prepare("DELETE FROM domaindaten  WHERE firmen_id = :id");

$statement->execute(array('id'=> $_POST['id'])); 

	/*echo "<br /><br />SQL Error <br />";
	echo $statement->queryString."<br />";
	echo $statement->errorInfo()[2]; */

$meldung = "meldung=Domain gelöscht!";

}



// header("Location: ".$url.$meldung);
?>