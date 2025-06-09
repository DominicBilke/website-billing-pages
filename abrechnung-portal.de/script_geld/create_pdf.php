<?php
 
 include 'inc_start.php';

$timestamp = time();
$datum = date("d.m.Y", $timestamp);
$nummer = date("d_m_Y", $timestamp);

$pdfName = "Rechnung_".$nummer.".pdf";
 
 
//////////////////////////// Inhalt des PDFs als HTML-Code \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 
 
// Erstellung des HTML-Codes. Dieser HTML-Code definiert das Aussehen eures PDFs.
// tcpdf unterstützt recht viele HTML-Befehle. Die Nutzung von CSS ist allerdings
// stark eingeschränkt.
 
$html= '<html>
	<head>
		<title>Rechnung vom '.$datum.'</title>
	</head>
	<body>
 
	<h2>Übersicht aller Touren</h2>
		<table>
    		<thead>	
      <tr>
         <th>Tour</th>
         <th>Verteiler</th>
         <th>Datum</th>
         <th>Startzeit</th>
         <th>Dauer</th>
         <th>Pause</th>
         <th>Flyer</th>
         <th>Gebiet</th>
         <th>Arbeitszeit</th>
         <th>Aktion</th>
      </tr>   
    </thead>
    <tbody>';

       $pdo = new PDO('mysql:host=localhost;dbname=d03360a0', 'd03360a0', 'Abrechnung');

       $sql = "SELECT * FROM tourdaten WHERE benutzer_id=".$_SESSION['id'];
       
       foreach ($pdo->query($sql) as $row) {
	$html .= '
       <tr>
         <td>'.$row['tour'].'</td>
         <td>'.$row['verteiler'].'</td>
         <td>'.$row['datum'].'</td>
         <td>'.$row['startzeit'].' Uhr</td>
         <td>'.$row['dauer'].' h:min</td>
         <td>'.$row['pause'].' h:min</td>
         <td>'.$row['flyer'].'</td>
         <td>'.$row['gebiet'].'</td>
         <td>'.$row['arbeitszeit'].' h:min</td>
         <td>
		<form action="script/tour_loeschen.php" method="POST" autocomplete="off">
			<input type="hidden" name="id" value="'.$row['id'].'"/>
			<button type="submit" name="tour_loeschen" value="1">Löschen</button>
		</form>
		<a href="upload/'.$row['datei'].'" download="upload/'.$row['datei'].'">Nachweis</a>
	</td>
      </tr>    '; } 

$html .= '
             </tbody>   
  		</table>

';
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
      $monate[] = 'alle';

       $pdo = new PDO('mysql:host=localhost;dbname=d03360a0', 'd03360a0', 'Abrechnung');

       $sql = "SELECT * FROM tourdaten WHERE benutzer_id='".$_SESSION['id']."'".$monat;
       foreach ($pdo->query($sql) as $row) {

	$zeit_h += strptime($row['arbeitszeit'], "%H:%M")['tm_hour'];
	$dauer_h += strptime($row['dauer'], "%H:%M")['tm_hour'];
	$pause_h += strptime($row['pause'], "%H:%M")['tm_hour'];

	$zeit_min += strptime($row['arbeitszeit'], "%H:%M")['tm_min'];
	$dauer_min += strptime($row['dauer'], "%H:%M")['tm_min'];
	$pause_min += strptime($row['pause'], "%H:%M")['tm_min'];

        if(!in_array($row['monat'], $monate)) $monate[] = $row['monat'];
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

    $html .= '
    <h1 align="center">Rechnung</h1>
    <p>'.$name.'<br/>
    '.$str_nr.'<br/>
    '.$plz_ort.'</p>
   
    <p>Datum: '.$datum.'</br>
    Ort: '.$plz_ort.'</p>
    
    <br/><br/>    
			<form action="/auswertung.php" method="GET" autocomplete="off">
 				<label>Monat :
				    <select name="monat">';
                                       foreach($monate as $m) {
 					 if($_GET['monat'] == $m) $html .= '<option selected>'.$m.'</option>';
					 else $html .= '<option>'.$m.'</option>';
					}
$html .= '				    </select>
				</label>
  				<button type="submit" name="monate" value="1">auswählen</button>
			</form>
    <br/><br/>     

    <p>Stundenlohn von '.$name.': '.$lohn.' Euro</p>
    <p>Dauer total: '.$dauer_h.' Stunden '.$dauer_min.' Minuten</p>
    <p>Pause total: '.$pause_h.' Stunden '.$pause_min.' Minuten</p>
    <p>Arbeitszeit total: '.$zeit_h.' Stunden '.$zeit_min.' Minuten</p>
    <p>Arbeitsstunden total: '.$total.' Stunden</p>
    <p>Lohn total: '.$tlohn.' Euro</p>'; 

$html .= '
</body>
</html>';
 
 
//////////////////////////// Erzeugung eures PDF Dokuments \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 
// TCPDF Library laden
require_once('tcpdf/tcpdf.php');
 
// Erstellung des PDF Dokuments
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
 
// Dokumenteninformationen
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($pdfAuthor);
$pdf->SetTitle('Rechnung '.$nummer);
$pdf->SetSubject('Rechnung '.$nummer);
 
 
// Header und Footer Informationen
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
 
// Auswahl des Font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
 
// Auswahl der MArgins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
 
// Automatisches Autobreak der Seiten
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
 
// Image Scale 
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
 
// Schriftart
$pdf->SetFont('dejavusans', '', 10);
 
// Neue Seite
$pdf->AddPage();
 
// Fügt den HTML Code in das PDF Dokument ein
$pdf->writeHTML($html, true, false, true, false, '');
 
//Ausgabe der PDF
 
//Variante 1: PDF direkt an den Benutzer senden:
$pdf->Output($pdfName, 'I');
 
//Variante 2: PDF im Verzeichnis abspeichern:
//$pdf->Output(dirname(__FILE__).'/'.$pdfName, 'F');
//$html .= 'PDF herunterladen: <a href="'.$pdfName.'">'.$pdfName.'</a>';
 
?>