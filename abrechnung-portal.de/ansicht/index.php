<?php
	require('script/inc.php');

	if(isset($_GET['domain'])) $_SESSION['domain']=$_GET['domain'];
	if(isset($_GET['logout'])) session_destroy();
	$domain = $_SESSION['domain'];
?>
<!DOCTYPE html>
<html>
<head lang="de">
  <title>Abrechnung-Portal.de</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../bootstrap-5.2.0-beta1/css/bootstrap.min.css" rel="stylesheet">
  <script src="../bootstrap-5.2.0-beta1/js/bootstrap.bundle.min.js"></script>
  <script src="../GM_Utils/GPX2GM.js"></script>
		<style>
			.map {width:100%;height:100vh;}
			@media screen and (min-width:700px) {
				.map { display:inline-block; width: 72%; width:calc(75% - 25px); height:95vh; height:calc(100vh - 10px); margin:0; padding:0 }
			}
		</style>

</head>
<body>
<div class="container mt-3">
  <h2 class="h3 text-center"><a href="https://www.abrechnung-portal.de/ansicht"><img src="../img/logo.png" class="rounded" alt="Abrechnung-Portal.de" width="300"/></a></h2>
  <br>
<ul class="nav justify-content-end">
 <li class="nav-item">
    <a class="nav-link" href="#">
			<b>Login: </b>
			<?php if($domain) { echo "Sie sind eingeloggt in Domain ".$domain; }
                         else { echo "Sie sind nicht eingeloggt!"; } ?>
	</a>

	</li>
</ul>
  <!-- Nav tabs -->
  <ul class="nav nav-tabs nav-justified" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" href="https://www.abrechnung-portal.de/ansicht/">Login</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="https://www.abrechnung-portal.de/ansicht/index.php?menu1=Tourenabrechnung">Tourenabrechnung</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="https://www.abrechnung-portal.de/ansicht/index.php?menu1=Arbeitsabrechnung">Arbeitsabrechnung</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="https://www.abrechnung-portal.de/ansicht/index.php?menu1=Aufgabenabrechnung">Aufgabenabrechnung</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="https://www.abrechnung-portal.de/ansicht/index.php?menu1=Geldabrechnung">Geldabrechnung</a>
    </li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div id="Login" class="container tab-pane <?php echo ($_GET['menu1'] ? "fade" : "active"); ?>"><br>
      <h3 class="h3">Login</h3>
		<form action="index.php" method="GET">
  				<label for="domain" class="form-label">Domain: </label> 
  				<input id="domain" class="form-control" name="domain" maxlength="100" type="text"><br/>
  				<button type="submit" class="btn btn-primary" name="login" value="1">Login</button>
  				<button type="submit" class="btn btn-primary" name="logout" value="1">Logout</button>
			</form>
    </div>
    <div id="Tourenabrechnung" class="container tab-pane <?php echo ($_GET['menu1'] == "Tourenabrechnung" ? "active" : "fade"); ?>"><br>
<?php

$login=0;
$pdo = new PDO('mysql:host=localhost;dbname=d033c52f', 'd033c52f', 'Abrechnung');

$sql = "SELECT datenbank, benutzer, psw FROM domaindaten WHERE domain='".$domain."'";
foreach ($pdo->query($sql) as $row) {

 $_SESSION['touren_dbname'] = $row['datenbank'];
 $_SESSION['touren_dbuser'] = $row['benutzer'];
 $_SESSION['touren_dbpsw'] = $row['psw'];
 $login=1;
}

if($login) {

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['touren_dbname'], $_SESSION['touren_dbuser'], $_SESSION['touren_dbpsw']);

       $sql = "SELECT `verteiler`, `monat`, `projekt` FROM `tourdaten`";

       $monate = ['alle'];
       $benutzer = ['alle'];   
       $projekte = ['alle'];       
       foreach ($pdo->query($sql) as $row) {
           if(!in_array($row['verteiler'], $benutzer)) $benutzer[] = $row['verteiler'];
           if(!in_array($row['monat'], $monate)) $monate[] = $row['monat']; 
           if(!in_array($row['projekt'], $projekte)) $projekte[] = $row['projekt']; }
echo '
	<h1 class="h3">Übersicht aller Touren</h1>
    <br/><br/>    
			<form action="./index.php" method="GET" autocomplete="off">
 				<label>Projekt :
				    <select name="projekt">';
 					foreach($projekte as $p) {
 					 if($_GET['projekt'] == $p) echo '<option selected>'.$p.'</option>';
					 else echo '<option>'.$p.'</option>';
					} 
echo '				    </select>
				</label>    |  
 				<label>Monat :
				    <select name="monat">';
 					foreach($monate as $m) {
 					 if($_GET['monat'] == $m) echo '<option selected>'.$m.'</option>';
					 else echo '<option>'.$m.'</option>';
					} 
echo '				    </select>
				</label>    |  
 				<label>Verteiler :
				    <select name="benutzer">';
                                        foreach($benutzer as $b) {
 					 if($_GET['benutzer'] == $b) echo '<option selected>'.$b.'</option>';
					 else echo '<option>'.$b.'</option>';
					} 
echo '				    </select>
				</label>
				<input type="hidden" value="Tourenabrechnung" name="menu1"/>
  				<button type="submit" name="benutzer_monate" value="1">auswählen</button>
			</form>';
?>
<table width="1024" class="table table-light">
    <thead>	
      <tr>
         <th>Tour</th>
         <th>Projekt</th>
         <th>Verteiler</th>
         <th>Datum</th>
         <th>Startzeit</th>
         <th>Dauer</th>
         <th>Pause</th>
         <th>Flyer</th>
         <th>Gebiet</th>
         <th>Arbeitszeit</th>
      </tr>   
    </thead>
    <tbody>
       <?php

		$sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'WHERE `benutzer_id`'));
		if(!strrpos($monat_benutzer, 'WHERE `benutzer_id`')) $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'AND `benutzer_id`'));

       
       foreach ($pdo->query($sql) as $row) {
	if($row['gpx']) $gpx = $row['gpx'];
	else $gpx = str_replace("jpg", "gpx", $row['datei']);  
        $gpx_str .= "../touren_upload/".$gpx.", ";
	echo '
       <tr>
         <td>'.$row['id'].'
	     <a href="../touren_upload/'.$row['datei'].'" download="'.$row['datei'].'">Nachweis</a>
         </td>
         <td>'.$row['projekt'].'
	     <a href="../gesamtkarte.php?gpx_str=touren_upload/'.$gpx.'" target="_new">Karte</a>
	 </td>
         <td>'.$row['verteiler'].'</td>
         <td>'.$row['datum'].'</td>
         <td>'.$row['startzeit'].' Uhr</td>
         <td>'.$row['dauer'].' h:min</td>
         <td>'.$row['pause'].' h:min</td>
         <td>'.$row['flyer'].'</td>
         <td>'.$row['gebiet'].'</td>
         <td>'.$row['arbeitszeit'].' h:min</td>
      </tr>    '; } ?>
             </tbody>   
  		</table>

<?php if($gpx_str) { echo '
  <h1 class="h3">Kartenansicht über alle ausgewählten Touren</h1>
  <div class="map gpxview:'.$gpx_str.':Karte" style="width:1024px;height:1024px">
    <noscript><p>Zum Anzeigen der Karte wird Javascript benötigt.</p></noscript>
  </div>';
 }

       
       $dauer_h = 0;
       $dauer_min = 0;
       $pause_h = 0;
       $pause_min = 0;
       $zeit_h = 0;
       $zeit_min = 0;
       $lohn = 0;
       $total = 0;
       $tlohn = 0;
       $name = " ";
       $str_nr = " ";
       $plz_ort = " ";
	$timestamp = time();
	$datum = date("d.m.Y", $timestamp);
      $monate = ['alle'];
      $projekte = ['alle'];
      $benutzer = ['alle'];

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['touren_dbname'], $_SESSION['touren_dbuser'], $_SESSION['touren_dbpsw']);

		$sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'WHERE `benutzer_id`'));
		if(!strrpos($monat_benutzer, 'WHERE `benutzer_id`')) $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'AND `benutzer_id`'));

       foreach ($pdo->query($sql) as $row) {

	$zeit_h += strptime($row['arbeitszeit'], "%H:%M")['tm_hour'];
	$dauer_h += strptime($row['dauer'], "%H:%M")['tm_hour'];
	$pause_h += strptime($row['pause'], "%H:%M")['tm_hour'];

	$zeit_min += strptime($row['arbeitszeit'], "%H:%M")['tm_min'];
	$dauer_min += strptime($row['dauer'], "%H:%M")['tm_min'];
	$pause_min += strptime($row['pause'], "%H:%M")['tm_min'];
	}

       $sql = "SELECT * FROM tourdaten";       
       foreach ($pdo->query($sql) as $row) {
            if(!in_array($row['monat'], $monate)) $monate[] = $row['monat'];
            if(!in_array($row['projekt'], $projekte)) $projekte[] = $row['projekt'];
            if(!in_array($row['verteiler'], $benutzer)) $benutzer[] = $row['verteiler'];
}

       $sql = "SELECT * FROM benutzerdaten";       
       foreach ($pdo->query($sql) as $row) {
            $lohn = $row['lohn']; 
            $name = $row['vorname']." ".$row['nachname'];
	    $str_nr = $row['strasse_nr'];
	    $plz_ort = $row['plz_ort'];
}

$zeit_h += intdiv($zeit_min, 60);
$zeit_min = fmod($zeit_min, 60);
$dauer_h += intdiv($dauer_min, 60);
$dauer_min = fmod($dauer_min, 60);
$pause_h += intdiv($pause_min, 60);
$pause_min = fmod($pause_min, 60);

$total = $zeit_h;
$total += ($zeit_min / 60);
$total = number_format($total, 2, ",", ".");

$tlohn = $lohn * ($zeit_h + ($zeit_min / 60));
$tlohn = number_format($tlohn, 2, ",", ".");

    echo '
    <h1 class="h3">Auswertung</h1>
    <p>Datum: '.$datum.'<br/>
    

    <br/><br/>     

    <p>Dauer total: '.$dauer_h.' Stunden '.$dauer_min.' Minuten</p>
    <p>Pause total: '.$pause_h.' Stunden '.$pause_min.' Minuten</p>
    <p>Arbeitszeit total: '.$zeit_h.' Stunden '.$zeit_min.' Minuten</p>
    <p>Arbeitsstunden total: '.$total.' Stunden</p>'; 
} ?>


    </div>
    <div id="Arbeitsabrechnung" class="container tab-pane <?php echo ($_GET['menu1'] == "Arbeitsabrechnung" ? "active" : "fade"); ?>"><br>

<?php

$login=0;
$pdo = new PDO('mysql:host=localhost;dbname=d034faa8', 'd034faa8', 'Arbeit');

$sql = "SELECT datenbank, benutzer, psw FROM domaindaten WHERE domain='".$domain."'";
foreach ($pdo->query($sql) as $row) {

 $_SESSION['arbeits_dbname'] = $row['datenbank'];
 $_SESSION['arbeits_dbuser'] = $row['benutzer'];
 $_SESSION['arbeits_dbpsw'] = $row['psw'];
 $login=1;
}

if($login) {


$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['arbeits_dbname'], $_SESSION['arbeits_dbuser'], $_SESSION['arbeits_dbpsw']);

       $sql = "SELECT `verteiler`, `monat`, `projekt` FROM `tourdaten`";

       $monate = ['alle'];
       $benutzer = ['alle'];   
       $projekte = ['alle'];       
       foreach ($pdo->query($sql) as $row) {
           if(!in_array($row['verteiler'], $benutzer)) $benutzer[] = $row['verteiler'];
           if(!in_array($row['monat'], $monate)) $monate[] = $row['monat']; 
           if(!in_array($row['projekt'], $projekte)) $projekte[] = $row['projekt']; }
echo '
  	<h1 class="h3">Übersicht aller Arbeiten</h1>
    <br/><br/>    
			<form action="./index.php" method="GET" autocomplete="off">
 				<label>Projekt :
				    <select name="projekt">';
 					foreach($projekte as $p) {
 					 if($_GET['projekt'] == $p) echo '<option selected>'.$p.'</option>';
					 else echo '<option>'.$p.'</option>';
					} 
echo '				    </select>
				</label>    |  
 				<label>Monat :
				    <select name="monat">';
 					foreach($monate as $m) {
 					 if($_GET['monat'] == $m) echo '<option selected>'.$m.'</option>';
					 else echo '<option>'.$m.'</option>';
					} 
echo '				    </select>
				</label>    |  
 				<label>Arbeiter :
				    <select name="benutzer">';
                                        foreach($benutzer as $b) {
 					 if($_GET['benutzer'] == $b) echo '<option selected>'.$b.'</option>';
					 else echo '<option>'.$b.'</option>';
					} 
echo '				    </select>
				</label>
				<input type="hidden" value="Arbeitsabrechnung" name="menu1"/>
  				<button type="submit" name="benutzer_monate" value="1">auswählen</button>
			</form>';
?>
<table class="table table-light">
    <thead>	
      <tr>
         <th>Nr.</th>
         <th>Projekt</th>
         <th>Arbeiter</th>
         <th>Datum</th>
         <th>Startzeit</th>
         <th>Dauer</th>
         <th>Pause</th>
         <th>Gebiet</th>
         <th>Arbeitszeit</th>
      </tr>   
    </thead>
    <tbody>
       <?php

		$sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'WHERE `benutzer_id`'));
		if(!strrpos($monat_benutzer, 'WHERE `benutzer_id`')) $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'AND `benutzer_id`'));
       
       foreach ($pdo->query($sql) as $row) {
	if($row['gpx']) $gpx = $row['gpx'];
	else $gpx = str_replace("jpg", "gpx", $row['datei']);  
        $gpx_str .= "../arbeits_upload/".$gpx.", ";
	echo '
       <tr>
         <td>'.$row['id'].'
	     <a href="../arbeits_upload/'.$row['datei'].'" download="'.$row['datei'].'">Nachweis</a>
         </td>
         <td>'.$row['projekt'].'
	     <a href="../gesamtkarte.php?gpx_str=arbeits_upload/'.$gpx.'" target="_new">Karte</a>
	 </td>
         <td>'.$row['verteiler'].'</td>
         <td>'.$row['datum'].'</td>
         <td>'.$row['startzeit'].' Uhr</td>
         <td>'.$row['dauer'].' h:min</td>
         <td>'.$row['pause'].' h:min</td>
         <td>'.$row['gebiet'].'</td>
         <td>'.$row['arbeitszeit'].' h:min</td>
      </tr>    '; } ?>
             </tbody>   
  		</table>

<?php if($gpx_str) { echo '
  <h1 class="h3">Kartenansicht über alle ausgewählten Arbeitswege</h1>
  <div class="map gpxview:'.$gpx_str.':Karte" style="width:1024px;height:1024px">
    <noscript><p>Zum Anzeigen der Karte wird Javascript benötigt.</p></noscript>
  </div>';
 }

       
       $dauer_h = 0;
       $dauer_min = 0;
       $pause_h = 0;
       $pause_min = 0;
       $zeit_h = 0;
       $zeit_min = 0;
       $lohn = 0;
       $total = 0;
       $tlohn = 0;
       $name = " ";
       $str_nr = " ";
       $plz_ort = " ";
	$timestamp = time();
	$datum = date("d.m.Y", $timestamp);
      $monate = ['alle'];
      $projekte = ['alle'];
      $benutzer = ['alle'];

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['arbeits_dbname'], $_SESSION['arbeits_dbuser'], $_SESSION['arbeits_dbpsw']);

		$sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'WHERE `benutzer_id`'));
		if(!strrpos($monat_benutzer, 'WHERE `benutzer_id`')) $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'AND `benutzer_id`'));
       foreach ($pdo->query($sql) as $row) {

	$zeit_h += strptime($row['arbeitszeit'], "%H:%M")['tm_hour'];
	$dauer_h += strptime($row['dauer'], "%H:%M")['tm_hour'];
	$pause_h += strptime($row['pause'], "%H:%M")['tm_hour'];

	$zeit_min += strptime($row['arbeitszeit'], "%H:%M")['tm_min'];
	$dauer_min += strptime($row['dauer'], "%H:%M")['tm_min'];
	$pause_min += strptime($row['pause'], "%H:%M")['tm_min'];
	}

       $sql = "SELECT * FROM tourdaten";       
       foreach ($pdo->query($sql) as $row) {
            if(!in_array($row['monat'], $monate)) $monate[] = $row['monat'];
            if(!in_array($row['projekt'], $projekte)) $projekte[] = $row['projekt'];
            if(!in_array($row['verteiler'], $benutzer)) $benutzer[] = $row['verteiler'];
}

       $sql = "SELECT * FROM benutzerdaten";       
       foreach ($pdo->query($sql) as $row) {
            $lohn = $row['lohn']; 
            $name = $row['vorname']." ".$row['nachname'];
	    $str_nr = $row['strasse_nr'];
	    $plz_ort = $row['plz_ort'];
}

$zeit_h += intdiv($zeit_min, 60);
$zeit_min = fmod($zeit_min, 60);
$dauer_h += intdiv($dauer_min, 60);
$dauer_min = fmod($dauer_min, 60);
$pause_h += intdiv($pause_min, 60);
$pause_min = fmod($pause_min, 60);

$total = $zeit_h;
$total += ($zeit_min / 60);
$total = number_format($total, 2, ",", ".");

$tlohn = $lohn * ($zeit_h + ($zeit_min / 60));
$tlohn = number_format($tlohn, 2, ",", ".");

    echo '
    <h1 class="h3">Auswertung</h1>
    <p>Datum: '.$datum.'<br/>
    

    <br/><br/>     

    <p>Dauer total: '.$dauer_h.' Stunden '.$dauer_min.' Minuten</p>
    <p>Pause total: '.$pause_h.' Stunden '.$pause_min.' Minuten</p>
    <p>Arbeitszeit total: '.$zeit_h.' Stunden '.$zeit_min.' Minuten</p>
    <p>Arbeitsstunden total: '.$total.' Stunden</p>'; 
}

?>




  </div>
    <div id="Aufgabenabrechnung" class="container tab-pane <?php echo ($_GET['menu1'] == "Aufgabenabrechnung" ? "active" : "fade"); ?>"><br>

<?php
$login=0;
$pdo = new PDO('mysql:host=localhost;dbname=d03a0212', 'd03a0212', '6RzEovLMuwhzLU5a');

$sql = "SELECT datenbank, benutzer, psw FROM domaindaten WHERE domain='".$domain."'";
foreach ($pdo->query($sql) as $row) {

 $_SESSION['aufgaben_dbname'] = $row['datenbank'];
 $_SESSION['aufgaben_dbuser'] = $row['benutzer'];
 $_SESSION['aufgaben_dbpsw'] = $row['psw'];
 $login=1;
}

if($login) {

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['aufgaben_dbname'], $_SESSION['aufgaben_dbuser'], $_SESSION['aufgaben_dbpsw']);

       $sql = "SELECT `verteiler`, `monat`, `projekt` FROM `tourdaten`";

       $monate = ['alle'];
       $benutzer = ['alle'];   
       $projekte = ['alle'];       
       foreach ($pdo->query($sql) as $row) {
           if(!in_array($row['verteiler'], $benutzer)) $benutzer[] = $row['verteiler'];
           if(!in_array($row['monat'], $monate)) $monate[] = $row['monat']; 
           if(!in_array($row['projekt'], $projekte)) $projekte[] = $row['projekt']; }
echo '
	<h1 class="h3">Übersicht aller Aufgaben</h1>
    <br/><br/>    
			<form action="./index.php" method="GET" autocomplete="off">
 				<label>Projekt :
				    <select name="projekt">';
 					foreach($projekte as $p) {
 					 if($_GET['projekt'] == $p) echo '<option selected>'.$p.'</option>';
					 else echo '<option>'.$p.'</option>';
					} 
echo '				    </select>
				</label>    |  
 				<label>Monat :
				    <select name="monat">';
 					foreach($monate as $m) {
 					 if($_GET['monat'] == $m) echo '<option selected>'.$m.'</option>';
					 else echo '<option>'.$m.'</option>';
					} 
echo '				    </select>
				</label>    |  
 				<label>Arbeiter :
				    <select name="benutzer">';
                                        foreach($benutzer as $b) {
 					 if($_GET['benutzer'] == $b) echo '<option selected>'.$b.'</option>';
					 else echo '<option>'.$b.'</option>';
					} 
echo '				    </select>
				</label>
				<input type="hidden" value="Aufgabenabrechnung" name="menu1"/>
  				<button type="submit" name="benutzer_monate" value="1">auswählen</button>
			</form>';
?>
<table class="table table-light">
    <thead>	
      <tr>      
	   <th>Nr.</th>
         <th>Aufgabe</th>
         <th>Name</th>
         <th>Datum</th>
         <th>Startzeit</th>
         <th>Dauer</th>
         <th>Pause</th>
         <th>Ort</th>
         <th>Arbeitszeit</th>

      </tr>   
    </thead>
    <tbody>
       <?php

		$sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'WHERE `benutzer_id`'));
		if(!strrpos($monat_benutzer, 'WHERE `benutzer_id`')) $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'AND `benutzer_id`'));
       
       foreach ($pdo->query($sql) as $row) {
	if($row['gpx']) $gpx = $row['gpx'];
	else $gpx = str_replace("jpg", "gpx", $row['datei']);  
        $gpx_str .= "../arbeits_upload/".$gpx.", ";
	echo '
       <tr>
         <td>'.$row['id'].'
	     <a href="../arbeits_upload/'.$row['datei'].'" download="'.$row['datei'].'">Nachweis</a>
         </td>
         <td>'.$row['projekt'].'
	 </td>
         <td>'.$row['verteiler'].'</td>
         <td>'.$row['datum'].'</td>
         <td>'.$row['startzeit'].' Uhr</td>
         <td>'.$row['dauer'].' h:min</td>
         <td>'.$row['pause'].' h:min</td>
         <td>'.$row['gebiet'].'</td>
         <td>'.$row['arbeitszeit'].' h:min</td>

      </tr>    '; } ?>
             </tbody>   
  		</table>

<?php
       $dauer_h = 0;
       $dauer_min = 0;
       $pause_h = 0;
       $pause_min = 0;
       $zeit_h = 0;
       $zeit_min = 0;
       $lohn = 0;
       $total = 0;
       $tlohn = 0;
       $name = " ";
       $str_nr = " ";
       $plz_ort = " ";
	$timestamp = time();
	$datum = date("d.m.Y", $timestamp);
      $monate = ['alle'];
      $projekte = ['alle'];
      $benutzer = ['alle'];

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['aufgaben_dbname'], $_SESSION['aufgaben_dbuser'], $_SESSION['aufgaben_dbpsw']);

		$sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'WHERE `benutzer_id`'));
		if(!strrpos($monat_benutzer, 'WHERE `benutzer_id`')) $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'AND `benutzer_id`'));
       foreach ($pdo->query($sql) as $row) {

	$zeit_h += strptime($row['arbeitszeit'], "%H:%M")['tm_hour'];
	$dauer_h += strptime($row['dauer'], "%H:%M")['tm_hour'];
	$pause_h += strptime($row['pause'], "%H:%M")['tm_hour'];

	$zeit_min += strptime($row['arbeitszeit'], "%H:%M")['tm_min'];
	$dauer_min += strptime($row['dauer'], "%H:%M")['tm_min'];
	$pause_min += strptime($row['pause'], "%H:%M")['tm_min'];
	}

       $sql = "SELECT * FROM tourdaten";       
       foreach ($pdo->query($sql) as $row) {
            if(!in_array($row['monat'], $monate)) $monate[] = $row['monat'];
            if(!in_array($row['projekt'], $projekte)) $projekte[] = $row['projekt'];
            if(!in_array($row['verteiler'], $benutzer)) $benutzer[] = $row['verteiler'];
}

       $sql = "SELECT * FROM benutzerdaten";       
       foreach ($pdo->query($sql) as $row) {
            $lohn = $row['lohn']; 
            $name = $row['vorname']." ".$row['nachname'];
	    $str_nr = $row['strasse_nr'];
	    $plz_ort = $row['plz_ort'];
}

$zeit_h += intdiv($zeit_min, 60);
$zeit_min = fmod($zeit_min, 60);
$dauer_h += intdiv($dauer_min, 60);
$dauer_min = fmod($dauer_min, 60);
$pause_h += intdiv($pause_min, 60);
$pause_min = fmod($pause_min, 60);

$total = $zeit_h;
$total += ($zeit_min / 60);
$total = number_format($total, 2, ",", ".");

$tlohn = $lohn * ($zeit_h + ($zeit_min / 60));
$tlohn = number_format($tlohn, 2, ",", ".");

    echo '
    <h1 class="h3">Auswertung</h1>
    <p>Datum: '.$datum.'<br/>
    

    <br/><br/>     

    <p>Dauer total: '.$dauer_h.' Stunden '.$dauer_min.' Minuten</p>
    <p>Pause total: '.$pause_h.' Stunden '.$pause_min.' Minuten</p>
    <p>Arbeitszeit total: '.$zeit_h.' Stunden '.$zeit_min.' Minuten</p>
    <p>Arbeitsstunden total: '.$total.' Stunden</p>'; 
} ?>
    </div>


    <div id="Geldabrechnung" class="container tab-pane <?php echo ($_GET['menu1'] == "Geldabrechnung" ? "active" : "fade"); ?>"><br>

<?php
$login=0;
$pdo = new PDO('mysql:host=localhost;dbname=d035eb93', 'd035eb93', 'aKhzarV6qFs3vEpx');


$sql = "SELECT datenbank, benutzer, psw FROM domaindaten WHERE domain='".$domain."'";
foreach ($pdo->query($sql) as $row) {

 $_SESSION['geld_dbname'] = $row['datenbank'];
 $_SESSION['geld_dbuser'] = $row['benutzer'];
 $_SESSION['geld_dbpsw'] = $row['psw'];
 $login=1;
}

if($login) {

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['geld_dbname'], $_SESSION['geld_dbuser'], $_SESSION['geld_dbpsw']);

       $sql = "SELECT `verteiler`, `monat`, `projekt` FROM `tourdaten`";

       $monate = ['alle'];
       $benutzer = ['alle'];   
       $projekte = ['alle'];       
       foreach ($pdo->query($sql) as $row) {
           if(!in_array($row['verteiler'], $benutzer)) $benutzer[] = $row['verteiler'];
           if(!in_array($row['monat'], $monate)) $monate[] = $row['monat']; 
           if(!in_array($row['projekt'], $projekte)) $projekte[] = $row['projekt']; }
echo '
	<h1 class="h3">Übersicht aller Gelder</h1>
    <br/><br/>    
			<form action="./index.php" method="GET" autocomplete="off">
 				<label>Projekt :
				    <select name="projekt">';
 					foreach($projekte as $p) {
 					 if($_GET['projekt'] == $p) echo '<option selected>'.$p.'</option>';
					 else echo '<option>'.$p.'</option>';
					} 
echo '				    </select>
				</label>    |  
 				<label>Monat :
				    <select name="monat">';
 					foreach($monate as $m) {
 					 if($_GET['monat'] == $m) echo '<option selected>'.$m.'</option>';
					 else echo '<option>'.$m.'</option>';
					} 
echo '				    </select>
				</label>    |  
 				<label>Arbeiter :
				    <select name="benutzer">';
                                        foreach($benutzer as $b) {
 					 if($_GET['benutzer'] == $b) echo '<option selected>'.$b.'</option>';
					 else echo '<option>'.$b.'</option>';
					} 
echo '				    </select>
				</label>
				<input type="hidden" value="Geldabrechnung" name="menu1"/>
  				<button type="submit" name="benutzer_monate" value="1">auswählen</button>
			</form>';
?>
<table class="table table-light">
    <thead>	
      <tr>         <th>Nr.</th>
         <th>Projekt</th>
         <th>Name</th>
         <th>Datum</th>
         <th>Art</th>
         <th>Wiederkehr</th>
         <th>Kategorie</th>
         <th>Betrag</th>
         <th>Währung</th>
      </tr>   
    </thead>
    <tbody>
       <?php

		$sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'WHERE `benutzer_id`'));
		if(!strrpos($monat_benutzer, 'WHERE `benutzer_id`')) $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'AND `benutzer_id`'));
        $i = 0;

       foreach ($pdo->query($sql) as $row) {

        $i++;

	if($row['gpx'] && pathinfo($row['gpx'], PATHINFO_EXTENSION)) $gpx = '<a href="../geld_upload/'.$row['gpx'].'" download="'.$row['gpx'].'">Zahlung</a>';
	else $gpx = '';

	echo '
       <tr>
         <td>'.$i.'
	     <a href="../geld_upload/'.$row['datei'].'" download="'.$row['datei'].'">Rechnung</a><br/>
	     '.$gpx.'
         </td>
         <td>'.$row['projekt'].'</td>
         <td>'.$row['verteiler'].'</td>
         <td>'.$row['datum'].'</td>
         <td>'.$row['startzeit'].'</td>
         <td>'.$row['dauer'].'</td>
         <td>'.$row['pause'].'</td>
         <td align="right">'.number_format($row['gebiet'], 2).'</td>
         <td>'.$row['arbeitszeit'].'</td>

      </tr>    '; } ?>
             </tbody>   
  		</table>

<?php
        $einnahmen = 0;
       $ausgaben = 0;
       $total = 0;
       $name = " ";
       $str_nr = " ";
       $plz_ort = " ";
	$timestamp = time();
	$datum = date("d.m.Y", $timestamp);
      $monate = ['alle'];
      $projekte = ['alle'];
      $benutzer = ['alle'];

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['geld_dbname'], $_SESSION['geld_dbuser'], $_SESSION['geld_dbpsw']);


		$sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'WHERE `benutzer_id`'));
		if(!strrpos($monat_benutzer, 'WHERE `benutzer_id`')) $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'AND `benutzer_id`'));

       foreach ($pdo->query($sql) as $row) {
		$geld = floatval($row['gebiet']);
		if($geld > 0) $einnahmen += $geld;
		elseif($geld < 0) $ausgaben += $geld;
		$total += $geld;
	}



    echo '
    <h3 class="h3">Auswertung</h3>
   
    <p>Datum: '.$datum.'</p>
    <br/><br/>     


    <p>Einnahmen: '.$einnahmen.' EUR</p>
    <p>Ausgaben: '.$ausgaben.' EUR</p>
    <p>Stand am Ende: '.$total.' EUR</p>
    <p>&nbsp;</p>'; 
}
 ?>
  </div>
  </div>
</div>

</body>
</html>