<?php
if(isset($_SESSION['id'])) {


$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpsw']);

       if($_SESSION['arbeits_status'] == 'firma' )  $sql = "SELECT * FROM tourdaten";
	 else $sql = "SELECT * FROM tourdaten WHERE benutzer_id='".$_SESSION['id']."'";  

       $monate = ['alle'];
       $benutzer = ['alle'];   
       $projekte = ['alle'];       
       foreach ($pdo->query($sql) as $row) {
           if(!in_array($row['verteiler'], $benutzer)) $benutzer[] = $row['verteiler'];
           if(!in_array($row['monat'], $monate)) $monate[] = $row['monat']; 
           if(!in_array($row['projekt'], $projekte)) $projekte[] = $row['projekt']; }

echo '	<h3 class="h3">Übersicht aller Arbeitswege</h1>';
echo '   
			<form action="index.php" method="GET" autocomplete="off">
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
				<input type="hidden" value="Arbeits-Uebersicht" name="menu2"/>
  				<button type="submit" name="benutzer_monate" value="1">auswählen</button>
			</form>';
?>
<table width="1024" class="table table-light">
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
         <th>Aktion</th>
      </tr>   
    </thead>
    <tbody>
       <?php

       if($_SESSION['arbeits_status'] == 'firma' )
 	 { 
		$sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'WHERE `benutzer_id`'));
		if(!strrpos($monat_benutzer, 'WHERE `benutzer_id`')) $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'AND `benutzer_id`'));
	 }
	 else $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, '=')+1).$_SESSION['id'];

       $gpx_str = "";
       foreach ($pdo->query($sql) as $row) {
	if($row['gpx']) $gpx = $row['gpx'];
	else $gpx = str_replace("jpg", "gpx", $row['datei']);  
        $gpx_str .= "arbeits_upload/".$gpx.",";
	echo '
       <tr>
         <td>'.$row['id'].'
	     <a href="arbeits_upload/'.$row['datei'].'" download="'.$row['datei'].'">Nachweis</a>
         </td>
         <td>'.$row['projekt'].'
	     <a href="gesamtkarte.php?gpx_str=arbeits_upload/'.$gpx.'">Karte</a>
	 </td>
         <td>'.$row['verteiler'].'</td>
         <td>'.$row['datum'].'</td>
         <td>'.$row['startzeit'].' Uhr</td>
         <td>'.$row['dauer'].' h:min</td>
         <td>'.$row['pause'].' h:min</td>
         <td>'.$row['gebiet'].'</td>
         <td>'.$row['arbeitszeit'].' h:min</td>
         <td>
		<form action="script_arbeit/tour_loeschen.php" method="POST" autocomplete="off">
			<input type="hidden" name="id" value="'.$row['id'].'"/>
			<input type="hidden" name="datei" value="'.$row['datei'].'"/>
			<input type="hidden" name="gpx" value="'.$row['gpx'].'"/>
			<button type="submit" name="tour_loeschen" value="1">Löschen</button>
		</form>
	</td>
      </tr>    '; } 

	$gpx_str = rtrim($gpx_str, ",");
?>
             </tbody>   
  		</table>

<?php 
	if($gpx_str) { echo '<div class="h3"><a href="gesamtkarte.php?gpx_str='.$gpx_str.'" target="_new">Kartenansicht über alle ausgewählten Touren</a></div>'; }

}
?>