<?php
session_start();
if(isset($_POST['typ']) && isset($_POST['vorname']) && isset($_POST['nachname']) && isset($_POST['strasse_nr']) && isset($_POST['plz_ort'])) {

if($_POST['typ'] == 'Abrechnungstool') {

        $_SESSION['id'] = $_SESSION['touren_id'];
        $_SESSION['benutzer'] = $_SESSION['touren_benutzer'];
        $_SESSION['status'] = $_SESSION['touren_status'];
 	  $_SESSION['dbname'] = $_SESSION['touren_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['touren_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['touren_dbpsw'];

require('../script_touren/benutzerdaten.php');
}
else if($_POST['typ'] == 'Firmenabrechnung') {

        $_SESSION['id'] = $_SESSION['firmen_id'];
        $_SESSION['benutzer'] = $_SESSION['firmen_benutzer'];
        $_SESSION['status'] = $_SESSION['firmen_status'];
 	  $_SESSION['dbname'] = $_SESSION['firmen_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['firmen_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['firmen_dbpsw'];

require('../script_firma/benutzerdaten.php');
}
else if($_POST['typ'] == 'Geldabrechnung') {

        $_SESSION['id'] = $_SESSION['geld_id'];
        $_SESSION['benutzer'] = $_SESSION['geld_benutzer'];
        $_SESSION['status'] = $_SESSION['geld_status'];
 	  $_SESSION['dbname'] = $_SESSION['geld_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['geld_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['geld_dbpsw'];

require('../script_geld/benutzerdaten.php');
}
else if($_POST['typ'] == 'Aufgabenabrechnung') {

        $_SESSION['id'] = $_SESSION['aufgaben_id'];
        $_SESSION['benutzer'] = $_SESSION['aufgaben_benutzer'];
        $_SESSION['status'] = $_SESSION['aufgaben_status'];
 	  $_SESSION['dbname'] = $_SESSION['aufgaben_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['aufgaben_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['aufgaben_dbpsw'];

require('../script_aufgaben/benutzerdaten.php');
}
else if($_POST['typ'] == 'Arbeitsabrechnung') {

        $_SESSION['id'] = $_SESSION['arbeits_id'];
        $_SESSION['benutzer'] = $_SESSION['arbeits_benutzer'];
        $_SESSION['status'] = $_SESSION['arbeits_status'];
 	  $_SESSION['dbname'] = $_SESSION['arbeits_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['arbeits_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['arbeits_dbpsw'];

require('../script_arbeit/benutzerdaten.php');
}
else if($_POST['typ'] == 'alle') {

        $_SESSION['id'] = $_SESSION['touren_id'];
        $_SESSION['benutzer'] = $_SESSION['touren_benutzer'];
        $_SESSION['status'] = $_SESSION['touren_status'];
 	  $_SESSION['dbname'] = $_SESSION['touren_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['touren_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['touren_dbpsw'];

require('../script_touren/benutzerdaten.php');

        $_SESSION['id'] = $_SESSION['firmen_id'];
        $_SESSION['benutzer'] = $_SESSION['firmen_benutzer'];
        $_SESSION['status'] = $_SESSION['firmen_status'];
 	  $_SESSION['dbname'] = $_SESSION['firmen_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['firmen_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['firmen_dbpsw'];

require('../script_firma/benutzerdaten.php');

        $_SESSION['id'] = $_SESSION['geld_id'];
        $_SESSION['benutzer'] = $_SESSION['geld_benutzer'];
        $_SESSION['status'] = $_SESSION['geld_status'];
 	  $_SESSION['dbname'] = $_SESSION['geld_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['geld_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['geld_dbpsw'];

require('../script_geld/benutzerdaten.php');

        $_SESSION['id'] = $_SESSION['aufgaben_id'];
        $_SESSION['benutzer'] = $_SESSION['aufgaben_benutzer'];
        $_SESSION['status'] = $_SESSION['aufgaben_status'];
 	  $_SESSION['dbname'] = $_SESSION['aufgaben_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['aufgaben_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['aufgaben_dbpsw'];


require('../script_aufgaben/benutzerdaten.php');

        $_SESSION['id'] = $_SESSION['arbeits_id'];
        $_SESSION['benutzer'] = $_SESSION['arbeits_benutzer'];
        $_SESSION['status'] = $_SESSION['arbeits_status'];
 	  $_SESSION['dbname'] = $_SESSION['arbeits_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['arbeits_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['arbeits_dbpsw'];

require('../script_arbeit/benutzerdaten.php');
}

}

header('Location: https://www.abrechnung-portal.de/verwaltung.php');

?>