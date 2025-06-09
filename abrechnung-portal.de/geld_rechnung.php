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
    <h3 class="h3">Rechnung</h3>';
    
echo '   				    			<form action="index.php" method="GET" autocomplete="off">

 				<label for="projekt">Projekt :</label>
				    <select name="projekt">';
 					foreach($projekte as $p) {
 					 if($_GET['projekt'] == $p) echo '<option selected>'.$p.'</option>';
					 else echo '<option>'.$p.'</option>';
					} 
echo '				    </select> | 
 				<label for="monat">Monat :</label>
				    <select name="monat">';
 					foreach($monate as $m) {
 					 if($_GET['monat'] == $m) echo '<option selected>'.$m.'</option>';
					 else echo '<option>'.$m.'</option>';
					} 
echo '				    </select> | 
 				<label for="benutzer">Name :</label>
				    <select name="benutzer">';
                                        foreach($benutzer as $b) {
 					 if($_GET['benutzer'] == $b) echo '<option selected>'.$b.'</option>';
					 else echo '<option>'.$b.'</option>';
					} 
echo '				    </select>
				<input type="hidden" value="Geldabrechnung" name="menu1"/>
				<input type="hidden" value="Geld-Rechnung" name="menu2"/>
  				<button type="submit" name="benutzer_monate" value="1">auswählen</button>
			</form>
 					
    <br/><br/>	

    <form action="rechnung.php" method="GET" autocomplete="off"> 
				<input type="hidden" name="Abrechnung" value="Geldabrechnung"/>
				<input type="hidden" name="Name" value="'.$name.'"/>
				<input type="hidden" name="Str_Nr" value="'.$str_nr.'"/>
				<input type="hidden" name="PLZ_Ort" value="'.$plz_ort.'"/>
				<input type="hidden" name="Anzahl" value="1"/>
				<input type="hidden" name="Lohn" value="'.$total.'"/>
				<input type="hidden" name="Firma" value="'.$_SESSION['firma'].'"/>
 				<label class="form-label" for="Empfaenger">Empfänger:</label><br/>
				<textarea class="form-control" name="Empfaenger" required ></textarea><br/>
 				<label class="form-label" for="Kontoinhaber">Kontoinhaber:</label><br/>
				<input class="form-control" name="Kontoinhaber" type="text" required /><br/>
 				<label class="form-label" for="IBAN">IBAN:</label><br/>
				<input class="form-control" name="IBAN" type="text" required /><br/>
 				<label class="form-label" for="BIC">BIC:</label><br/>
				<input class="form-control" class="form-control" name="BIC" type="text" required /><br/>
 				<label class="form-label" for="Steuer">Umsatzsteuer (z.B. 0.19):</label><br/>
				<input class="form-control" name="Steuer" type="text" value="0.19" required /><br/>
 				<label class="form-label" for="Ziel">Zahlungsziel in Tagen:</label><br/>
				<input class="form-control" name="Ziel" type="text" value="14" required /><br/>
 				<label class="form-label" for="Nr">Rechnungs-Nr.:</label><br/>
				<input class="form-control" name="Nr" type="text" required /><br/>
 				<label class="form-label" for="Steuer-Nr">Steuer-Nr.:</label><br/>
				<input class="form-control" name="Steuer-Nr" type="text" required /><br/>
 				<label class="form-label" for="Lieferdatum">Lieferdatum:</label><br/>
				<input class="form-control" name="Lieferdatum" type="text" required /><br/>
 				<label class="form-label" for="Leistungszeitraum">Leistungszeitraum (nicht notwendig):</label><br/>
				<input class="form-control" name="Leistungszeitraum" type="text" /><br/>
  				<button class="btn btn-primary" type="submit" name="Rechnung" value="1">Rechnung erstellen</button>
			</form>'; } ?>