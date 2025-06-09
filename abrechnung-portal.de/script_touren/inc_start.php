<?php
session_start();

    $url = (empty($_SERVER['HTTPS'])) ? 'http://' : 'https://';
    $url .= $_SERVER['HTTP_HOST'];
    $url .= $_SERVER['REQUEST_URI']; // $url enthält jetzt die komplette URL
    if(strpos($url, "?"))
	    $url .= "&";
    else
   	    $url .= "?";
    $_SESSION['url'] = $url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Abrechnungstool.de</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="bootstrap513/css/bootstrap.min.css" rel="stylesheet">
  <script src="bootstrap513/js/bootstrap.bundle.min.js"></script>
  <script src="GM_Utils/GPX2GM.js"></script>
		<style>
			.map {width:100%;height:100vh;}
			@media screen and (min-width:700px) {
				.map { display:inline-block; width: 72%; width:calc(75% - 25px); height:95vh; height:calc(100vh - 10px); margin:0; padding:0 }
			}
		</style>
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-sm bg-secondary navbar-dark fixed-top">
  <div class="container-fluid">
    <ul class="navbar-nav">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">Abrechnungstool-Menü</a>
    <ul class="dropdown-menu">
      <li>
        <a class="dropdown-item" href="index.php">Login</a>
      </li>
      <li>
        <a class="dropdown-item" href="einfuegen.php">Einfügen</a>
      </li>
      <li>
        <a class="dropdown-item" href="uebersicht.php">Übersicht</a>
      </li>
      <li>
        <a class="dropdown-item" href="auswertung.php">Auswertung</a>
      </li>
      <li>
        <a class="dropdown-item" href="verwaltung.php">Verwaltung</a>
      </li>

<?php		
	if(($_SESSION['status'] == 'admin') || ($_SESSION['status'] == 'firma')) {
        	echo '
      <li>
        <a href="gesamtuebersicht.php" class="dropdown-item">Gesamtübersicht</a>
      </li>
      <li>
        <a href="gesamtauswertung.php" class="dropdown-item">Gesamtauswertung</a>
      </li>
      <li>
        <a href="benutzerverwaltung.php" class="dropdown-item">Benutzerverwaltung</a>
      </li>';
	}
?>
      <li>
        <a class="dropdown-item" href="hilfe.php">Hilfe</a>
      </li>
	</li>
	</ul>
	<li class="nav-item">
	<a class="nav-link" href="#">
			<b>Login: </b>
			<?php if(isset($_SESSION['id'])) { echo "Sie sind eingeloggt als ".$_SESSION['benutzer']." in Domain ".$_SESSION['domain']; }
                         else { echo "Sie sind nicht eingeloggt!"; } ?>
	</a>

	</li>
	<li class="nav-item">
	<a class="nav-link" href="#">
		<b>Meldungen: </b>
		<?php if(isset($_GET['meldung'])) echo $_GET['meldung']; ?>
	</a>
	</li>
    </ul>
  </div>
</nav>