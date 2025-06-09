<?php
	require('script/inc.php');
?>
<!DOCTYPE html>
<html>
<head lang="de">
  <title>Abrechnung-Portal.de</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="bootstrap-5.2.0-beta1/css/bootstrap.min.css" rel="stylesheet">
  <script src="bootstrap-5.2.0-beta1/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-3">
  <h1 class="h3 text-center"><a href="https://www.abrechnung-portal.de"><img src="img/logo.png" class="rounded" alt="Abrechnung-Portal.de" width="300"/></a></h1>
  <br>
<ul class="nav justify-content-end">
 <li class="nav-item">
    <a class="nav-link" href="#">
			<b>Login: </b>
			<?php 

	  if($_SESSION['firmen_id']) {
        $_SESSION['id'] = $_SESSION['firmen_id'];
        $_SESSION['benutzer'] = $_SESSION['firmen_benutzer'];
        $_SESSION['status'] = $_SESSION['firmen_status'];
 	  $_SESSION['dbname'] = $_SESSION['firmen_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['firmen_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['firmen_dbpsw']; }

	  if($_SESSION['arbeits_id']) {
        $_SESSION['id'] = $_SESSION['arbeits_id'];
        $_SESSION['benutzer'] = $_SESSION['arbeits_benutzer'];
        $_SESSION['status'] = $_SESSION['arbeits_status'];
 	  $_SESSION['dbname'] = $_SESSION['arbeits_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['arbeits_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['arbeits_dbpsw']; }


	  if($_SESSION['touren_id']) {
        $_SESSION['id'] = $_SESSION['touren_id'];
        $_SESSION['benutzer'] = $_SESSION['touren_benutzer'];
        $_SESSION['status'] = $_SESSION['touren_status'];
 	  $_SESSION['dbname'] = $_SESSION['touren_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['touren_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['touren_dbpsw']; }


	  if($_SESSION['aufgaben_id']) {
        $_SESSION['id'] = $_SESSION['aufgaben_id'];
        $_SESSION['benutzer'] = $_SESSION['aufgaben_benutzer'];
        $_SESSION['status'] = $_SESSION['aufgaben_status'];
 	  $_SESSION['dbname'] = $_SESSION['aufgaben_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['aufgaben_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['aufgaben_dbpsw']; }


	  if($_SESSION['geld_id']) {
        $_SESSION['id'] = $_SESSION['geld_id'];
        $_SESSION['benutzer'] = $_SESSION['geld_benutzer'];
        $_SESSION['status'] = $_SESSION['geld_status'];
 	  $_SESSION['dbname'] = $_SESSION['geld_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['geld_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['geld_dbpsw']; }


				if(isset($_SESSION['id'])) { echo "Sie sind eingeloggt als ".$_SESSION['benutzer']." in Domain ".$_SESSION['domain']; }
                         else { echo "Sie sind nicht eingeloggt!"; } ?>
	</a>

	</li>
	<li class="nav-item">
    <a class="nav-link" href="#">
		<b>Meldungen: </b>
		<?php if(isset($_GET['meldung'])) echo $_GET['meldung']; ?>
	</a>
	</li>
	<?php if(isset($_SESSION['id'])) { 
	echo '<li class="nav-item">
    <a class="nav-link" href="https://www.abrechnung-portal.de/index.php?menu1=Verwaltung">Verwaltung</a>
	</li>';
	}
	?>


	<li class="nav-item">
    <a class="nav-link" href="datenschutz.php">Datenschutz
	</a>
	</li>
	<li class="nav-item">
    <a class="nav-link" href="impressum.php">Impressum</a>
	</li>
</ul>
  <!-- Nav tabs -->
  <ul class="nav nav-tabs nav-justified" role="tablist">
    <li class="nav-item">
      <a class="nav-link <?php echo ($_GET['menu1'] ? "" : "active"); ?>" href="https://www.abrechnung-portal.de">Login</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo ($_GET['menu1'] == "Firmenabrechnung" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Firmenabrechnung">Firmenabrechnung</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo ($_GET['menu1'] == "Tourenabrechnung" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Tourenabrechnung">Tourenabrechnung</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo ($_GET['menu1'] == "Arbeitsabrechnung" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Arbeitsabrechnung">Arbeitsabrechnung</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo ($_GET['menu1'] == "Aufgabenabrechnung" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Aufgabenabrechnung">Aufgabenabrechnung</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo ($_GET['menu1'] == "Geldabrechnung" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Geldabrechnung">Geldabrechnung</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo ($_GET['menu1'] == "Hilfe" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Hilfe">Hilfe</a>
    </li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div id="Login" class="container bg-light p-2 tab-pane <?php echo ($_GET['menu1'] ? "fade" : "active"); ?>"><br>
      <h2 class="h3">Login</h2>
      <?php 
		require('login.php');
      ?>
    </div>
    <div id="Login" class="container bg-light p-2 tab-pane <?php echo ($_GET['menu1'] == "Verwaltung" ? "active" : "fade"); ?>"><br>
      <h2 class="h3">Verwaltung</h2>
      <?php 
		require('verwaltung.php');
      ?>
    </div>

<?php if($_SESSION['firmen_status'] == "firma") { ?>
    <div id="Firmenabrechnung" class=" bg-light p-2 container tab-pane  <?php echo ($_GET['menu1'] == "Firmenabrechnung" ? "active" : "fade"); ?>"><br>
<ul class="nav nav-tabs nav-justified">
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Firma-Einfuegen" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Firmenabrechnung&menu2=Firma-Einfuegen">Einfügen</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Firma-Uebersicht" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Firmenabrechnung&menu2=Firma-Uebersicht">Übersicht</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Firma-Auswertung" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Firmenabrechnung&menu2=Firma-Auswertung">Auswertung</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Firma-Benutzerverwaltung" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Firmenabrechnung&menu2=Firma-Benutzerverwaltung">Benutzerverwaltung</a>
  </li>
</ul>
      <h2 class="h3">Firmenabrechnung</h2>
      <p>Eine Mitarbeiterabrechnung ist möglich. Als PDF-Dokument in der Auswertung.</p>
      <div class="tab-content">
    <div id="Firma-Einfuegen" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Firma-Einfuegen" ? "active" : "fade"); ?>"><br>
      <?php 
        $_SESSION['id'] = $_SESSION['firmen_id'];
        $_SESSION['benutzer'] = $_SESSION['firmen_benutzer'];
        $_SESSION['status'] = $_SESSION['firmen_status'];
 	  $_SESSION['dbname'] = $_SESSION['firmen_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['firmen_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['firmen_dbpsw'];
		require('firmen_einfuegen.php');
      ?>
    </div>
    <div id="Firma-Uebersicht" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Firma-Uebersicht" ? "active" : "fade"); ?>"><br>
      <?php 
		require('firmen_uebersicht.php');
      ?>
    </div>
    <div id="Firma-Auswertung" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Firma-Auswertung" ? "active" : "fade"); ?>"><br>
      <?php 
		require('firmen_auswertung.php');
      ?>
    </div>
    <div id="Firma-Benutzerverwaltung" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Firma-Benutzerverwaltung" ? "active" : "fade"); ?>"><br>
      <?php 
		require('benutzerverwaltung.php');
      ?>
    </div>
    </div>
  </div>
<?php  }
	else if($_SESSION['firmen_status']) { ?>

    <div id="Firmenabrechnung" class="container tab-pane <?php echo ($_GET['menu1'] == "Firmenabrechnung" ? "active" : "fade"); ?>"><br>
      <h2 class="h3">Firmenabrechnung</h2>
      <p>Als Admin oder Benutzer haben Sie keinen Zugriff auf die Firmenabrechnung.</p>
    </div>
<?php } else { ?>

    <div id="Firmenabrechnung" class=" bg-light p-2 container tab-pane <?php echo ($_GET['menu1'] == "Firmenabrechnung" ? "active" : "fade"); ?>"><br>
<ul class="nav nav-tabs nav-justified">
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Firma-Einfuegen" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Firmenabrechnung&menu2=Firma-Einfuegen">Einfügen</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Firma-Uebersicht" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Firmenabrechnung&menu2=Firma-Uebersicht">Übersicht</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Firma-Auswertung" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Firmenabrechnung&menu2=Firma-Auswertung">Auswertung</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Firma-Benutzerverwaltung" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Firmenabrechnung&menu2=Firma-Benutzerverwaltung">Benutzerverwaltung</a>
  </li>
</ul>
      <h2 class="h3">Firmenabrechnung</h2>
      <p>Eine Mitarbeiterabrechnung ist möglich. Als PDF-Dokument in der Auswertung.</p>
    </div>
<?php } ?>
    <div id="Tourenabrechnung" class=" bg-light p-2 container tab-pane <?php echo ($_GET['menu1'] == "Tourenabrechnung" ? "active" : "fade"); ?>"><br>
<ul class="nav nav-tabs nav-justified">
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == 'Touren-Einfuegen' ? 'active' : ''); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Tourenabrechnung&menu2=Touren-Einfuegen">Einfügen</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == 'Touren-Uebersicht' ? 'active' : ''); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Tourenabrechnung&menu2=Touren-Uebersicht">Übersicht</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == 'Touren-Auswertung' ? 'active' : ''); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Tourenabrechnung&menu2=Touren-Auswertung">Auswertung</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == 'Touren-Rechnung' ? 'active' : ''); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Tourenabrechnung&menu2=Touren-Rechnung">Rechnung</a>
  </li>
</ul>
      <h2 class="h3">Tourenabrechnung</h2>
      <p>Es können Touren abgerechnet werden. Als GPX und Bild-Datei mit Darstellung der Strecke. Weiterhin als Übersichtsseite für jeden Benutzer. Ausgabe als PDF-Dokument mit Auswertung.</p>
      <div class="tab-content">
    <div id="Touren-Einfuegen" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == 'Touren-Einfuegen' ? 'active' : 'fade'); ?>"><br>
      <?php 
        $_SESSION['id'] = $_SESSION['touren_id'];
        $_SESSION['benutzer'] = $_SESSION['touren_benutzer'];
        $_SESSION['status'] = $_SESSION['touren_status'];
 	  $_SESSION['dbname'] = $_SESSION['touren_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['touren_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['touren_dbpsw'];

		require('touren_einfuegen.php');
      ?>
    </div>
    <div id="Touren-Uebersicht" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Touren-Uebersicht" ? "active" : "fade"); ?>"><br>
      <?php 
		require('touren_uebersicht.php');
      ?>
    </div>
    <div id="Touren-Auswertung" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Touren-Auswertung" ? "active" : "fade"); ?>"><br>
      <?php 
		require('touren_auswertung.php');
      ?>
    </div>
    <div id="Touren-Rechnung" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Touren-Rechnung" ? "active" : "fade"); ?>"><br>
      <?php 
		require('touren_rechnung.php');
      ?>
    </div>
    </div>
  </div>
    <div id="Arbeitsabrechnung" class=" bg-light p-2 container tab-pane <?php echo ($_GET['menu1'] == "Arbeitsabrechnung" ? "active" : "fade"); ?>"><br>
<ul class="nav nav-tabs nav-justified">
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Arbeits-Einfuegen" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Arbeitsabrechnung&menu2=Arbeits-Einfuegen">Einfügen</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Arbeits-Uebersicht" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Arbeitsabrechnung&menu2=Arbeits-Uebersicht">Übersicht</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Arbeits-Auswertung" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Arbeitsabrechnung&menu2=Arbeits-Auswertung">Auswertung</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Arbeits-Rechnung" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Arbeitsabrechnung&menu2=Arbeits-Rechnung">Rechnung</a>
  </li>
</ul>
      <h2 class="h3">Arbeitsabrechnung</h2>
      <p>Arbeiten können mit Nachweis (Bild, PDF) und GPX-Datei abgerechnet werden. Weiterhin gibt es eine Übersichtsseite für Firma und Benutzer. Ausgabe als PDF-Dokument
mit Auswertung.</p>
      <div class="tab-content">
    <div id="Arbeits-Einfuegen" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Arbeits-Einfuegen" ? "active" : "fade"); ?>"><br>
      <?php 
        $_SESSION['id'] = $_SESSION['arbeits_id'];
        $_SESSION['benutzer'] = $_SESSION['arbeits_benutzer'];
        $_SESSION['status'] = $_SESSION['arbeits_status'];
 	  $_SESSION['dbname'] = $_SESSION['arbeits_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['arbeits_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['arbeits_dbpsw'];
		require('arbeits_einfuegen.php');
      ?>
    </div>
    <div id="Arbeits-Uebersicht" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Arbeits-Uebersicht" ? "active" : "fade"); ?>"><br>
      <?php 
		require('arbeits_uebersicht.php');
      ?>
    </div>
    <div id="Arbeits-Auswertung" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Arbeits-Auswertung" ? "active" : "fade"); ?>"><br>
      <?php 
		require('arbeits_auswertung.php');
      ?>
    </div>
    <div id="Arbeits-Rechnung" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Arbeits-Rechnung" ? "active" : "fade"); ?>"><br>
      <?php 
		require('arbeits_rechnung.php');
      ?>
    </div>
    </div>
  </div>
    <div id="Aufgabenabrechnung" class=" bg-light p-2 container tab-pane <?php echo ($_GET['menu1'] == "Aufgabenabrechnung" ? "active" : "fade"); ?>"><br>
<ul class="nav nav-tabs nav-justified">
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Aufgaben-Einfuegen" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Aufgabenabrechnung&menu2=Aufgaben-Einfuegen">Einfügen</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Aufgaben-Uebersicht" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Aufgabenabrechnung&menu2=Aufgaben-Uebersicht">Übersicht</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Aufgaben-Auswertung" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Aufgabenabrechnung&menu2=Aufgaben-Auswertung">Auswertung</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Aufgaben-Rechnung" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Aufgabenabrechnung&menu2=Aufgaben-Rechnung">Rechnung</a>
  </li>
</ul>
      <h2 class="h3">Aufgabenabrechnung</h2>
      <p>Aufgaben können mit Nachweis (Bild, PDF) abgerechnet werden. Weiterhin gibt es eine Übersichtsseite für Firma und Benutzer. Ausgabe als PDF-
Dokument mit Auswertung.</p>
      <div class="tab-content">
    <div id="Aufgaben-Einfuegen" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Aufgaben-Einfuegen" ? "active" : "fade"); ?>"><br>
      <?php 
        $_SESSION['id'] = $_SESSION['aufgaben_id'];
        $_SESSION['benutzer'] = $_SESSION['aufgaben_benutzer'];
        $_SESSION['status'] = $_SESSION['aufgaben_status'];
 	  $_SESSION['dbname'] = $_SESSION['aufgaben_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['aufgaben_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['aufgaben_dbpsw'];
		require('aufgaben_einfuegen.php');
      ?>
    </div>
    <div id="Aufgaben-Uebersicht" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Aufgaben-Uebersicht" ? "active" : "fade"); ?>"><br>
      <?php 
		require('aufgaben_uebersicht.php');
      ?>
    </div>
    <div id="Aufgaben-Auswertung" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Aufgaben-Auswertung" ? "active" : "fade"); ?>"><br>
      <?php 
		require('aufgaben_auswertung.php');
      ?>
    </div>
    <div id="Aufgaben-Rechnung" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Aufgaben-Rechnung" ? "active" : "fade"); ?>"><br>
      <?php 
		require('aufgaben_rechnung.php');
      ?>
    </div>
    </div>
    </div>
    <div id="Geldabrechnung" class=" bg-light p-2 container tab-pane <?php echo ($_GET['menu1'] == "Geldabrechnung" ? "active" : "fade"); ?>"><br>
<ul class="nav nav-tabs nav-justified">
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Geld-Einfuegen" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Geldabrechnung&menu2=Geld-Einfuegen">Einfügen</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Geld-Uebersicht" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Geldabrechnung&menu2=Geld-Uebersicht">Übersicht</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Geld-Auswertung" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Geldabrechnung&menu2=Geld-Auswertung">Auswertung</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($_GET['menu2'] == "Geld-Rechnung" ? "active" : ""); ?>" href="https://www.abrechnung-portal.de/index.php?menu1=Geldabrechnung&menu2=Geld-Rechnung">Rechnung</a>
  </li>
</ul>
      <h2 class="h3">Geldabrechnung</h2>
      <p>Gelder können mit Rechnung und Zahlung abgerechnet werden. Ausgabe als PDF-Dokument mit Auswertung. Es ist eine EA-Abrechnung für das Finanzamt
möglich. Darstellung der Gelder als Graph.</p>
      <div class="tab-content">
    <div id="Geld-Einfuegen" class=" tab-pane container <?php echo ($_GET['menu2'] == "Geld-Einfuegen" ? "active" : "fade"); ?>"><br>
      <?php 
        $_SESSION['id'] = $_SESSION['geld_id'];
        $_SESSION['benutzer'] = $_SESSION['geld_benutzer'];
        $_SESSION['status'] = $_SESSION['geld_status'];
 	  $_SESSION['dbname'] = $_SESSION['geld_dbname'];
 	  $_SESSION['dbuser'] = $_SESSION['geld_dbuser'];
 	  $_SESSION['dbpsw'] = $_SESSION['geld_dbpsw'];
 	  $_SESSION['firma'] = $_SESSION['geld_firma'];
		require('geld_einfuegen.php');
      ?>
    </div>
    <div id="Geld-Uebersicht" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Geld-Uebersicht" ? "active" : "fade"); ?>"><br>
      <?php 
		require('geld_uebersicht.php');
      ?>
    </div>
    <div id="Geld-Auswertung" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Geld-Auswertung" ? "active" : "fade"); ?>"><br>
      <?php 
		require('geld_auswertung.php');
      ?>
    </div>
    <div id="Geld-Rechnung" class=" bg-light p-2 tab-pane container <?php echo ($_GET['menu2'] == "Geld-Rechnung" ? "active" : "fade"); ?>"><br>
      <?php 
		require('geld_rechnung.php');
      ?>
    </div>
    </div>
  </div>
    <div id="Hilfe" class=" bg-light p-2 container tab-pane <?php echo ($_GET['menu1'] == "Hilfe" ? "active" : "fade"); ?>"><br>
      <h2 class="h3">Hilfe</h2>
      <?php 
		require('hilfe.php');
      ?>
    </div>
  </div>
</div>

</body>
</html>