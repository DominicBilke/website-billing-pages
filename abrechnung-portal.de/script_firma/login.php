<?php
$_SESSION['domain'] = $_POST['datenbank'];
$_SESSION['dbname'] = "";
$_SESSION['dbuser'] = "";
$_SESSION['dbpsw'] = "";
$firmen_id = "";
$login=0;

$pdo = new PDO('mysql:host=localhost;dbname=d03a0216', 'd03a0216', 'vTFFs9PD8UgNz6rm');

$sql = "SELECT firmen_id, datenbank, benutzer, psw FROM domaindaten WHERE domain='".$_SESSION['domain']."'";
foreach ($pdo->query($sql) as $row) {

 $_SESSION['dbname'] = $row['datenbank'];
 $_SESSION['dbuser'] = $row['benutzer'];
 $_SESSION['dbpsw'] = $row['psw'];
 $firmen_id = $row['firmen_id'];
 $login=1;
}

if($login) {

$sql = "SELECT benutzer, psw, person, plz_ort, strasse_nr FROM firmendaten WHERE id=".$firmen_id." AND benutzer='".$_POST['benutzer']."' AND psw='".$_POST['pswd']."'";




$pdo1 = new PDO('mysql:host=localhost;dbname='.$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpsw']);

foreach ($pdo->query($sql) as $row) {

$sql = "SELECT id, status FROM benutzer WHERE benutzer='".$_POST['benutzer']."' AND pswd='".$_POST['pswd']."'";

foreach ($pdo1->query($sql) as $row1) { 
$benutzer_id = $row1['id'];
$status = $row1['status']; 
}

//echo "<br /><br />".$sql."<br />";


if(!isset($benutzer_id))
{
$statement = $pdo1->prepare("INSERT INTO benutzer (benutzer, pswd, status)  VALUES (?, ?, ?)");

	$statement->execute(array($_POST['benutzer'], $_POST['pswd'], 'firma'));

	//echo "<br /><br />SQL Error 1<br />";
	//echo $statement->queryString."<br />";
	//echo $statement->errorInfo()[2]; 

	$sql = "SELECT id FROM benutzer WHERE benutzer='".$_POST['benutzer']."' AND pswd='".$_POST['pswd']."'";

	foreach ($pdo1->query($sql) as $row2) { $benutzer_id = $row2['id']; }

	//echo "<br /><br />".$sql."<br />";

	$statement = $pdo1->prepare("INSERT INTO benutzerdaten (benutzer_id, lohn, nachname, plz_ort, strasse_nr, vorname)  VALUES (?, ?, ?, ?, ?, ?)");

	$statement->execute(array($benutzer_id, '0', $row['person'], $row['plz_ort'], $row['strasse_nr'], ' '));

	//echo "<br /><br />SQL Error 1<br />";
	//echo $statement->queryString."<br />";
	//echo $statement->errorInfo()[2]; 

}
 $login=1;
}


$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpsw']);

$sql = "SELECT id, status FROM benutzer WHERE benutzer='".$_POST['benutzer']."' AND pswd='".$_POST['pswd']."'";
foreach ($pdo->query($sql) as $row) {
$benutzer_id = $row['id'];
$status = $row['status'];
}

if(isset($_POST['login']) && isset($benutzer_id))
{
        $_SESSION['firmen_id'] = $benutzer_id;
        $_SESSION['firmen_benutzer'] = $_POST['benutzer'];
        $_SESSION['firmen_status'] = $status;
 	  $_SESSION['firmen_dbname'] = $_SESSION['dbname'];
 	  $_SESSION['firmen_dbuser'] = $_SESSION['dbuser'];
 	  $_SESSION['firmen_dbpsw'] = $_SESSION['dbpsw'];

}
}
?>