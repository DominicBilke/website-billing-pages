<?php 

session_start();
$url = $_SESSION['url'];


$empfaenger = 'anmeldung@arbeitsabrechnung.de';
$betreff = "Arbeitsabrechnung.de: Anmeldung der Firma ".$_POST['firma'];
$from = "From: ".$_POST['vorname']." ".$_POST['nachname']." <".$_POST['mail'].">";
$text = "
Anmeldung der Firma ".$_POST['firma']."

Eingang: ".date("d.m.Y - H:i")."
Benutzer: ".$_POST['benutzer']."
Passwort: ".$_POST['passwort']."
Account 1: ".$_POST['account_1']."
Account 2: ".$_POST['account_2']."
Account 3: ".$_POST['account_3']."
E-Mail: ".$_POST['mail']."
Firma: ".$_POST['firma']."
Ansprechpartner: ".$_POST['person']."
Stasse Nr: ".$_POST['strasse_nr']."
PLZ Ort: ".$_POST['plz_ort']."
Kontoinhaber: ".$_POST['inhaber']."
IBAN: ".$_POST['iban']."
BIC: ".$_POST['bic']."
";
 
mail($empfaenger, $betreff, $text, $from);

	header("Location: https://www.arbeitsabrechnung.de/paypal.php");
?>