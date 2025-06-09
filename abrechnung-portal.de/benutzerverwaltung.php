<?php

if(isset($_GET['id']) && isset($_GET['bearbeiten']))
  {
     $button="Benutzer bearbeiten";
     $ben_link = "/script/benutzer_aendern.php";
     $ben_id = $_GET['id'];
     $bearbeiten=true;
  }  
  else
  {
     $button="Neuer Benutzer";
     $ben_link = "/script/neuer_benutzer.php";
     $ben_id = $_GET['id'];
     $bearbeiten=false;
  }  

if(isset($_SESSION['id'])) {
?>
<h3 class="h3">Übersicht aller Benutzer</h3>
		<table width="1024" class="table table-light">
    		<thead>	
      <tr>
         <th>Aktion</th>
         <th>Benutzer</th>
         <th>Passwort</th>
         <th>Lohn</th>
         <th>Vorname</th>
         <th>Nachname</th>
         <th>Strasse Nr</th>
         <th>PLZ, Ort</th>
         <th>Status</th>
         <th>Aktion</th>
      </tr>   
    </thead>
    <tbody>
       <?php
$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpsw']);

       $sql = "SELECT * FROM benutzerdaten, benutzer WHERE benutzerdaten.benutzer_id = benutzer.id";
       
       foreach ($pdo->query($sql) as $row) {
	echo '
       <tr>
         <td>
		<form action="index.php" method="GET" autocomplete="off">
			<input type="hidden" value="Firmenabrechnung" name="menu1"/>
			<input type="hidden" value="Firma-Benutzerverwaltung" name="menu2"/>
			<input type="hidden" name="id" value="'.$row['id'].'"/>
			<button type="submit" name="bearbeiten" value="1">Bearbeiten</button>
		</form>
         <td>'.$row['benutzer'].'</td>
         <td>'.$row['pswd'].'</td>
         <td>'.$row['lohn'].' Euro</td>
         <td>'.$row['vorname'].'</td>
         <td>'.$row['nachname'].'</td>
         <td>'.$row['strasse_nr'].'</td>
         <td>'.$row['plz_ort'].'</td>
         <td>'.$row['status'].'</td>
         <td>
		<form action="script/benutzer_loeschen.php" method="POST" autocomplete="off">
			<input type="hidden" name="id" value="'.$row['id'].'"/>
			<button type="submit" name="benutzer_loeschen" value="1">Löschen</button>
		</form>
	</td>
      </tr>    '; } 
   
echo '
             </tbody>   
  		</table>'; 
  		  
      if($bearbeiten)  {
       $sql = "SELECT * FROM benutzerdaten, benutzer WHERE benutzerdaten.benutzer_id = benutzer.id AND benutzer.id=".$ben_id;
       foreach ($pdo->query($sql) as $row) {	
       echo '
       <h3 class="h3">Benutzerdaten</h3>
		<p>
			<form action="/script/benutzer_aendern.php" method="POST" autocomplete="off">
  				<label for="benutzer" class="form-label">Benutzer: </label> 
  				<input name="benutzer" class="form-control" maxlength="100" type="text" value="'.$row['benutzer'].'" required><br/>
  				<label for="pswd" class="form-label">Passwort: </label> 
  				<input name="pswd" class="form-control" maxlength="100" type="text" value="'.$row['pswd'].'" required><br/>
  				<label for="lohn" class="form-label">Lohn: </label> 
  				<input name="lohn" class="form-control" maxlength="100" type="text" value="'.$row['lohn'].'" required><br/>
  				<label for="vorname" class="form-label">Vorname: </label> 
  				<input name="vorname" class="form-control" maxlength="100" type="text" value="'.$row['vorname'].'" required><br/>
  				<label for="nachname" class="form-label">Nachname: </label> 
  				<input name="nachname" class="form-control" maxlength="100" type="text" value="'.$row['nachname'].'" required><br/>
  				<label for="strasse_nr" class="form-label">Strasse Nr: </label> 
  				<input name="strasse_nr" class="form-control" maxlength="100" type="text" value="'.$row['strasse_nr'].'" required><br/>
  				<label for="plz_ort" class="form-label">PLZ Ort: </label> 
  				<input name="plz_ort" class="form-control" maxlength="100" type="text" value="'.$row['plz_ort'].'" required><br/>
  				<input type="hidden" name="id" value="'.$row['id'].'" required/>
  				<input type="hidden" name="status" value="'.$row['status'].'" required/>
  				<button type="submit" class="btn btn-primary" name="benutzer_aendern" value="1">Benutzer bearbeiten</button>
			</form>
		</p>';
		break;
       }
  }  
  else
  {
  		echo '
		<h3 class="h3">Benutzerdaten</h3>
		<p>
			<form action="/script/neuer_benutzer.php" method="POST" autocomplete="off">
  				<label for="benutzer" class="form-label">Benutzer: </label> 
  				<input name="benutzer" class="form-control" maxlength="100" type="text" required><br/>
  				<label for="pswd" class="form-label">Passwort: </label> 
  				<input name="pswd" class="form-control" maxlength="100" type="text" required><br/>
  				<label for="lohn" class="form-label">Lohn: </label> 
  				<input name="lohn" class="form-control" maxlength="100" type="text" required><br/>
  				<label for="vorname" class="form-label">Vorname: </label> 
  				<input name="vorname" class="form-control" maxlength="100" type="text" required><br/>
  				<label for="nachname" class="form-label">Nachname: </label> 
  				<input name="nachname" class="form-control" maxlength="100" type="text" required><br/>
  				<label for="strasse_nr" class="form-label">Strasse Nr: </label> 
  				<input name="strasse_nr" class="form-control" maxlength="100" type="text" required><br/>
  				<label for="plz_ort" class="form-label">PLZ Ort: </label> 
  				<input name="plz_ort" class="form-control" maxlength="100" type="text" required><br/>
 				<label for="sratus" class="form-label">Status:
				    <select name="status" class="form-select">
				      <option label="Benutzer">benutzer</option>
				      <option label="Firma">firma</option>
				      <option label="Admin">admin</option>
				    </select><br/>
  				<button type="submit" class="btn btn-primary" name="neuer_benutzer" value="1">Neuen benutzer anlegen</button>
			</form>
		</p>';
   }
}
?>


