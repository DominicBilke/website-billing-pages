<?php

if(isset($_SESSION['id'])) {
       
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

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpsw']);
   
     
       if($_SESSION['arbeits_status'] == 'firma' )
 	 { 
		$sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'WHERE `benutzer_id`'));
		if(!strrpos($monat_benutzer, 'WHERE `benutzer_id`')) $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'AND `benutzer_id`'));
	 }
	 else $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, '=')+1).$_SESSION['id'];


       foreach ($pdo->query($sql) as $row) {

	$zeit_h += strptime($row['arbeitszeit'], "%H:%M")['tm_hour'];
	$dauer_h += strptime($row['dauer'], "%H:%M")['tm_hour'];
	$pause_h += strptime($row['pause'], "%H:%M")['tm_hour'];

	$zeit_min += strptime($row['arbeitszeit'], "%H:%M")['tm_min'];
	$dauer_min += strptime($row['dauer'], "%H:%M")['tm_min'];
	$pause_min += strptime($row['pause'], "%H:%M")['tm_min'];
	}

       if($_SESSION['geld_status'] == 'firma' )  $sql = "SELECT * FROM tourdaten";
	 else $sql = "SELECT * FROM tourdaten WHERE benutzer_id='".$_SESSION['id']."'";  
     
       foreach ($pdo->query($sql) as $row) {
            if(!in_array($row['monat'], $monate)) $monate[] = $row['monat'];
            if(!in_array($row['projekt'], $projekte)) $projekte[] = $row['projekt'];
            if(!in_array($row['verteiler'], $benutzer)) $benutzer[] = $row['verteiler'];
}

       $sql = "SELECT * FROM benutzerdaten WHERE benutzer_id='".$_SESSION['id']."'";       
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
    <h3 class="h3">Auswertung</h3>
    <h3 class="h3"><a href="script_arbeit/pdf_erstellen_einzeln.php'.$inc_string.'" class="text-info">[DOWNLOAD ALS PDF]</a></h3>
    <p>'.$name.'<br/>
    '.$str_nr.'<br/>
    '.$plz_ort.'</p>
   
    <p>Datum: '.$datum.'<br/>
    Ort: '.$plz_ort.'</p>';
    
echo '
    <br/><br/>    
			<form action="index.php" method="GET" autocomplete="off">
 				<label>Projekt :
				    <select name="projekt">';
 					foreach($projekte as $p) {
 					 if($_GET['projekt'] == $p) echo '<option selected>'.$p.'</option>';
					 else echo '<option>'.$p.'</option>';
					} 
echo '				    </select>
				</label><br/><br/>
 				<label>Monat :
				    <select name="monat">';
 					foreach($monate as $m) {
 					 if($_GET['monat'] == $m) echo '<option selected>'.$m.'</option>';
					 else echo '<option>'.$m.'</option>';
					} 
echo '				    </select>
				</label><br/><br/>
 				<label>Arbeiter :
				    <select name="benutzer">';
                                        foreach($benutzer as $b) {
 					 if($_GET['benutzer'] == $b) echo '<option selected>'.$b.'</option>';
					 else echo '<option>'.$b.'</option>';
					} 
echo '				    </select>
				</label><br/><br/>
				<input type="hidden" value="Arbeitsabrechnung" name="menu1"/>
				<input type="hidden" value="Arbeits-Auswertung" name="menu2"/>
  				<button type="submit" name="benutzer_monate" value="1">auswählen</button>
			</form>
    <br/><br/>     

    <p>Stundenlohn von '.$name.': '.$lohn.' Euro</p>
    <p>Dauer total: '.$dauer_h.' Stunden '.$dauer_min.' Minuten</p>
    <p>Pause total: '.$pause_h.' Stunden '.$pause_min.' Minuten</p>
    <p>Arbeitszeit total: '.$zeit_h.' Stunden '.$zeit_min.' Minuten</p>
    <p>Arbeitsstunden total: '.$total.' Stunden</p>
    <p>Lohn total: '.$tlohn.' Euro</p>'; }?>