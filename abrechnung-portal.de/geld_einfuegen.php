<?php
if(isset($_SESSION['id'])) {

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpsw']);

       $sql = "SELECT `verteiler`, `monat`, `projekt` FROM `tourdaten` WHERE `benutzer_id`=".$_SESSION['id'];

       $benutzer = ['alle'];   
       $projekte = ['alle'];       
       foreach ($pdo->query($sql) as $row) {
           if(!in_array($row['verteiler'], $benutzer)) $benutzer[] = $row['verteiler'];
           if(!in_array($row['monat'], $monate)) $monate[] = $row['monat']; 
           if(!in_array($row['projekt'], $projekte)) $projekte[] = $row['projekt']; }
       $projekt = $row['projekt'];
       $verteiler = $row['verteiler'];

$monate = array(1=>"Januar",
                2=>"Februar",
                3=>"M&auml;rz",
                4=>"April",
                5=>"Mai",
                6=>"Juni",
                7=>"Juli",
                8=>"August",
                9=>"September",
                10=>"Oktober",
                11=>"November",
                12=>"Dezember");
$monat = date("n");
$monat = $monate[$monat]." ".date("Y");

echo '
		<h3 class="h3">Geldbetrag einfügen</h3>
		<p>
		
			<form action="script_geld/tourdaten.php" method="POST" autocomplete="off" enctype="multipart/form-data">
  				<input name="tour" maxlength="100" type="hidden" value="0" required>
  				<label for="projekt" class="form-label">Projekt* (Text): </label> 
  				<input name="projekt" class="form-control" maxlength="100" type="text" value="'.$projekt.'" required><br/>
  				<label for="verteiler" class="form-label">Name* (Text): </label> 
  				<input name="verteiler" class="form-control" maxlength="100" type="text" value="'.$verteiler.'" required><br/>
  				<label for="datum" class="form-label">Datum* (Format dd.mm.yyyy): </label> 
  				<input name="datum" class="form-control" maxlength="100" type="text" value="'.date("d.m.Y").'" required  pattern="[0-9]{2}.[0-9]{2}.[0-9]{4}"><br/>
  				<label for="startzeit" class="form-label">Art* (text): </label> 
  				<input name="startzeit" class="form-control" maxlength="100" type="text" required><br/>
  				<label for="dauer" class="form-label">Wiederkehr* (Text): </label> 
  				<input name="dauer" class="form-control" maxlength="100" type="text" required><br/>
  				<label for="pause" class="form-label">Kategorie* (Text): </label> 
  				<input name="pause" class="form-control" maxlength="100" type="text" required><br/>
  				<input name="arbeitszeit" maxlength="100" type="hidden" value="EUR" required/>
  				<label for="gebiet" class="form-label">Betrag* (Format -00000.00): </label> 
  				<input name="gebiet" class="form-control" maxlength="100" type="text" required pattern="^-?[0-9]{1,12}(?:\.[0-9]{1,3})?$"><br/>
  				<label for="monat" class="form-label">Monat* (Text): </label> 
  				<input name="monat" class="form-control" maxlength="100" type="text" value="'.$monat.'" required><br/>
				<label class="form-label">Rechnung* (pdf, png, jpg, gif):
    					<input class="form-control" name="datei" type="file" size="50" accept=".pdf, .jpeg, .jpg, .png, .gif, .csv" required> 
  				</label><br/>
				<label class="form-label">Zahlung (pdf, png, jpg, gif):
    					<input class="form-control" name="gpx" type="file" size="50" value=" " accept=".pdf, .jpeg, .jpg, .png, .gif, .csv"> 
  				</label><br/>
  				<input name="flyer" maxlength="100" type="hidden" value="-" required>
  				<button type="submit" class="btn btn-primary" name="tourdaten" value="1">Speichern</button>
			</form>
			* notwendige Felder
		</p>'; } ?>