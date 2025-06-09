<?php
if(isset($_SESSION['id'])) {


$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpsw']);
   
       if($_SESSION['geld_status'] == 'firma' )  $sql = "SELECT * FROM tourdaten";
	 else $sql = "SELECT * FROM tourdaten WHERE benutzer_id='".$_SESSION['id']."'";  


       $monate = ['alle'];
       $benutzer = ['alle'];   
       $projekte = ['alle'];       
       foreach ($pdo->query($sql) as $row) {
           if(!in_array($row['verteiler'], $benutzer)) $benutzer[] = $row['verteiler'];
           if(!in_array($row['monat'], $monate)) $monate[] = $row['monat']; 
           if(!in_array($row['projekt'], $projekte)) $projekte[] = $row['projekt']; }

echo '	<h3 class="h3">Übersicht aller Geldbeträge</h3>';
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
 				<label>Name :
				    <select name="benutzer">';
                                        foreach($benutzer as $b) {
 					 if($_GET['benutzer'] == $b) echo '<option selected>'.$b.'</option>';
					 else echo '<option>'.$b.'</option>';
					} 
echo '				    </select>
				</label>
				<input type="hidden" value="Geldabrechnung" name="menu1"/>
				<input type="hidden" value="Geld-Uebersicht" name="menu2"/>
  				<button type="submit" name="benutzer_monate" value="1">auswählen</button>
			</form>';
?>
<table width="1024" class="table table-light">
    <thead>	
      <tr>
         <th>Nr.</th>
         <th>Projekt</th>
         <th>Name</th>
         <th>Datum</th>
         <th>Art</th>
         <th>Wiederkehr</th>
         <th>Kategorie</th>
         <th>Betrag</th>
         <th>Währung</th>
         <th>Aktion</th>
      </tr>   
    </thead>
    <tbody>
       <?php

       if($_SESSION['geld_status'] == 'firma' )
 	 { 
		$sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'WHERE `benutzer_id`'));
		if(!strrpos($monat_benutzer, 'WHERE `benutzer_id`')) $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'AND `benutzer_id`'));
	 }
	 else $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, '=')+1).$_SESSION['id'];

       $i = 0;

       foreach ($pdo->query($sql) as $row) {

        $i++;

	if($row['gpx'] && pathinfo($row['gpx'], PATHINFO_EXTENSION)) $gpx = '<a href="geld_upload/'.$row['gpx'].'" download="'.$row['gpx'].'">Zahlung</a>';
	else $gpx = '';

	echo '
       <tr>
         <td>'.$i.'
	     <a href="geld_upload/'.$row['datei'].'" download="'.$row['datei'].'">Rechnung</a><br/>
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
         <td>
		<form action="script_geld/tour_loeschen.php" method="POST" autocomplete="off">
			<input type="hidden" name="id" value="'.$row['id'].'"/>
			<input type="hidden" name="datei" value="'.$row['datei'].'"/>
			<input type="hidden" name="gpx" value="'.$row['gpx'].'"/>
			<button type="submit" name="tour_loeschen" value="1">Löschen</button>
		</form>
	</td>
      </tr>    '; } 

	$gpx_str = rtrim($gpx_str, ", ");
?>
             </tbody>   
  		</table>
<?php 
echo '<h3 class="h3">Darstellung der Kategorien und des Betrages</h3>';
echo '<img src="graph.php'.$inc_string.'"/>';
}
?>