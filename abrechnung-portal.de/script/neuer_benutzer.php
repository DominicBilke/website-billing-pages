<?php
session_start();


        $_SESSION['id'] = $_SESSION['arbeits_id'];
        $_SESSION['benutzer'] = $_SESSION['arbeits_benutzer'];
        $_SESSION['status'] = $_SESSION['arbeits_status'];
 	  $_SESSION['dbname'] = $_SESSION['arbeits_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['arbeits_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['arbeits_dbpsw'];

require('../script_arbeit/neuer_benutzer.php');

        $_SESSION['id'] = $_SESSION['geld_id'];
        $_SESSION['benutzer'] = $_SESSION['geld_benutzer'];
        $_SESSION['status'] = $_SESSION['geld_status'];
 	  $_SESSION['dbname'] = $_SESSION['geld_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['geld_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['geld_dbpsw'];

require('../script_geld/neuer_benutzer.php');

        $_SESSION['id'] = $_SESSION['aufgaben_id'];
        $_SESSION['benutzer'] = $_SESSION['aufgaben_benutzer'];
        $_SESSION['status'] = $_SESSION['aufgaben_status'];
 	  $_SESSION['dbname'] = $_SESSION['aufgaben_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['aufgaben_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['aufgaben_dbpsw'];

require('../script_aufgaben/neuer_benutzer.php');

        $_SESSION['id'] = $_SESSION['touren_id'];
        $_SESSION['benutzer'] = $_SESSION['touren_benutzer'];
        $_SESSION['status'] = $_SESSION['touren_status'];
 	  $_SESSION['dbname'] = $_SESSION['touren_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['touren_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['touren_dbpsw'];

require('../script_touren/neuer_benutzer.php');

        $_SESSION['id'] = $_SESSION['firmen_id'];
        $_SESSION['benutzer'] = $_SESSION['firmen_benutzer'];
        $_SESSION['status'] = $_SESSION['firmen_status'];
 	  $_SESSION['dbname'] = $_SESSION['firmen_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['firmen_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['firmen_dbpsw'];

require('../script_firma/neuer_benutzer.php');

header('Location: https://www.abrechnung-portal.de/');

?>