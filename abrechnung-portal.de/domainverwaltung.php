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

<h2 class="h2">Übersicht aller Domains</h2>
	
       <?php
$domains[1]['dbname'] = "d033c52f";
$domains[1]['dbpsw'] = "Abrechnung";
$domains[1]['name'] = "Abrechnungstool";

$domains[2]['dbname'] = "d03a0216";
$domains[2]['dbpsw'] = "vTFFs9PD8UgNz6rm";
$domains[2]['name'] = "Firmenabrechnung";

$domains[3]['dbname'] = "d035eb93";
$domains[3]['dbpsw'] = "aKhzarV6qFs3vEpx";
$domains[3]['name'] = "Geldabrechnung";

$domains[4]['dbname'] = "d03a0212";
$domains[4]['dbpsw'] = "6RzEovLMuwhzLU5a";
$domains[4]['name'] = "Aufgabenabrechnung";

$domains[5]['dbname'] = "d034faa8";
$domains[5]['dbpsw'] = "Arbeit";
$domains[5]['name'] = "Arbeitsabrechnung";

for($i=1; $i<=5; $i++)
{

echo '	
<h3 class="h3">'.$domains[$i]['name'].'</h3>
<table width="1024" class="table table-light">
    		<thead>	
      <tr>
         <th>ID</th>
         <th>Domain</th>
         <th>Datenbank</th>
         <th>Benutzer</th>
         <th>Passwort</th>
      </tr>   
    </thead>
    <tbody>';
$pdo = new PDO('mysql:host=localhost;dbname='.$domains[$i]['dbname'], $domains[$i]['dbname'], $domains[$i]['dbpsw']);

       $sql = "SELECT * FROM domaindaten";
       
       foreach ($pdo->query($sql) as $row) {
	echo '
       <tr>
         <td>'.$row['firmen_id'].'</td>
         <td>'.$row['domain'].'</td>
         <td>'.$row['datenbank'].'</td>
         <td>'.$row['benutzer'].'</td>
         <td>'.$row['psw'].'</td>
      </tr>    '; } ?>
             </tbody>   
  		</table>
		<table width="1024" class="table table-light">
    		<thead>	
      <tr>
         <th>ID</th>
         <th>Benutzer</th>
         <th>Passwort</th>        
         <th>Firma</th>
         <th>Person</th>
         <th>Strasse Nr</th>
         <th>PLZ Ort</th>
         <th>IBAN</th>
         <th>BIC</th>
         <th>Inhaber</th>
      </tr>   
    </thead>
    <tbody>
       <?php
$pdo = new PDO('mysql:host=localhost;dbname='.$domains[$i]['dbname'], $domains[$i]['dbname'], $domains[$i]['dbpsw']);

       $sql = "SELECT * FROM firmendaten";
       
       foreach ($pdo->query($sql) as $row) {
	echo '
       <tr>
         <td>'.$row['id'].'</td>
         <td>'.$row['benutzer'].'</td>
         <td>'.$row['psw'].'</td>
         <td>'.$row['firma'].'</td>
         <td>'.$row['person'].'</td>
         <td>'.$row['strasse_nr'].'</td>
         <td>'.$row['plz_ort'].'</td>
         <td>'.$row['iban'].'</td>
         <td>'.$row['bic'].'</td>
         <td>'.$row['inhaber'].'</td>
      </tr>    '; } ?>
             </tbody>   
  		</table>
<?php
}

?>
		<h2 class="h3">Domaindaten</h2>
		<p>
			<form action="/script/neue_domain.php" method="POST" autocomplete="off">
  				<label for="domain" class="form-label">Domain: </label> 
  				<input name="domain" class="form-control" maxlength="100" type="text" required><br/>
  				<label for="datenbank" class="form-label">Datenbank: </label> 
  				<input name="datenbank" class="form-control" maxlength="100" type="text" required><br/>
  				<label for="benutzer" class="form-label">Benutzer: </label> 
  				<input name="benutzer" class="form-control" maxlength="100" type="text" required><br/>
  				<label for="psw" class="form-label">Passwort: </label> 
  				<input name="psw" class="form-control" maxlength="100" type="text" required><br/>
  				<label for="firma" class="form-label">Firma: </label> 
  				<input name="firma" class="form-control" maxlength="100" type="text" required><br/>
  				<label for="person" class="form-label">Ansprechpartner: </label> 
  				<input name="person" class="form-control" maxlength="100" type="text" required><br/>
  				<label for="strasse_nr" class="form-label">Strasse Nr: </label> 
  				<input name="strasse_nr" class="form-control" maxlength="100" type="text" required><br/>
  				<label for="plz_ort" class="form-label">PLZ Ort: </label> 
  				<input name="plz_ort" class="form-control" maxlength="100" type="text" required><br/>
				<label for="typ" class="form-label">Domaintyp:</label>
				<select name="typ"  class="form-select">
				<?php 
					for($i=1; $i<=5; $i++)
					{

						echo '<option value="'.$domains[$i]['name'].'">'.$domains[$i]['name'].'</option>';
					}
				?>
				</select><br/>
  				<input name="iban" maxlength="100" type="hidden" value="-" required>
  				<input name="bic" maxlength="100" type="hidden" value="-" required>
  				<input name="inhaber" maxlength="100" type="hidden" value="-" required>
  				<button type="submit" class="btn btn-primary" name="neue_domain" value="1">Neuen Domain anlegen</button>
			</form>
		</p>

		<h2 class="h3">Domain löschen</h2>
		<p>
			<form action="/script/domain_loeschen.php" method="POST" autocomplete="off">
  				<label for="id" class="form-label">Domain-ID: </label> 
  				<input name="id" class="form-control" maxlength="100" type="text" required><br/>
				<label for="typ" class="form-label">Domaintyp:</label>
				<select name="typ"  class="form-select">
				<?php 
					for($i=1; $i<=5; $i++)
					{

						echo '<option value="'.$domains[$i]['name'].'">'.$domains[$i]['name'].'</option>';
					}
				?>
				</select><br/>
  				<button type="submit" class="btn btn-primary" name="neue_domain" value="1">Domain löschen</button>
			</form>
		</p>

		<h2 class="h3 text-center">Anleitung zur Erstellung einer Domain:</h2>
		<ol>
		<li>Im KAS bei All-Inkl.com einloggen und die Datenbanken bearbeiten.</li>
 		<li>Datenbank über sql Script anlegen: <a href="new_database_export.sql" class="text-info" download="new_database_export.sql">new_database_export.sql</a></li>
		<li>Benutzer und Lohn editieren.</li>
		<li>Domain über die <a href="domainverwaltung.php" class="text-info">Domainverwaltung</a> anlegen.</li>
		<li>Neuen Ordner anlegen mit Domain als Bezeichnung</li>
		<li>Das Backup bei den CronJobs installieren</a>
		</ol>
</div>

</body>
</html>