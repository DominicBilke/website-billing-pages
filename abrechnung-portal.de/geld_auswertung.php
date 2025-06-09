<?php

if(isset($_SESSION['id'])) {
       
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

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpsw']);


       if($_SESSION['geld_status'] == 'firma' )
 	 { 
		$sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'WHERE `benutzer_id`'));
		if(!strrpos($monat_benutzer, 'WHERE `benutzer_id`')) $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'AND `benutzer_id`'));
	 }
	 else $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, '=')+1).$_SESSION['id'];

       foreach ($pdo->query($sql) as $row) {
		$geld = floatval($row['gebiet']);
		if($geld > 0) $einnahmen += $geld;
		elseif($geld < 0) $ausgaben += $geld;
		$total += $geld;
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
		$firma = $_SESSION['firma'];
}

    echo '
    <h3 class="h3">Auswertung</h3>
    <h3 class="h3"><a href="script_geld/pdf_erstellen_einzeln.php'.$inc_string.'" class="text-info">[DOWNLOAD ALS PDF]</a></h3>
    <p>'.$firma.'<br/>
    '.$name.'<br/>
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
 				<label>Name :
				    <select name="benutzer">';
                                        foreach($benutzer as $b) {
 					 if($_GET['benutzer'] == $b) echo '<option selected>'.$b.'</option>';
					 else echo '<option>'.$b.'</option>';
					} 
echo '				    </select>
				</label><br/><br/>
				<input type="hidden" value="Geldabrechnung" name="menu1"/>
				<input type="hidden" value="Geld-Auswertung" name="menu2"/>
  				<button type="submit" name="benutzer_monate" value="1">auswählen</button>
			</form>
    <br/><br/>     


    <p>Einnahmen: '.$einnahmen.' EUR</p>
    <p>Ausgaben: '.$ausgaben.' EUR</p>
    <p>Stand am Ende: '.$total.' EUR</p>
    <p>&nbsp;</p>'; 
}
?>