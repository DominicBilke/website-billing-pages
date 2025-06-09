<?php
session_start();
$url = $_SESSION['url'];

$timestamp = time();
$datum = date("d.m.Y", $timestamp);
$nummer = date("d_m_Y", $timestamp);

$pdfName = "Uebersicht_".$nummer.".pdf";
 

    if(isset($_GET['monat'])) {
      $monat = $_GET['monat'];
      $inc_monat = $_GET['monat'];
      if($monat == 'alle') $monat = "";
      else $monat = "`monat` = '".$monat."'"; }
    else {
      	$monat = "";
	$inc_monat = 'alle'; }

    if(isset($_GET['benutzer'])) {
      $benutzer = $_GET['benutzer'];
      $inc_benutzer = $_GET['benutzer'];
      if($benutzer == 'alle') $benutzer = "";
      else $benutzer = "`verteiler` = '".$benutzer."'"; }
    else {
      	$benutzer = "";
	$inc_benutzer = 'alle'; }

    if(isset($_GET['projekt'])) {
      $projekt = $_GET['projekt'];
      $inc_projekt = $_GET['projekt'];
      if($projekt == 'alle') $projekt = "";
      else $projekt = "`projekt` = '".$projekt."'"; }
    else {
      	$projekt = "";
	$inc_projekt = 'alle'; }

if($monat && $benutzer && $projekt) {
	$monat_benutzer = " WHERE ".$monat." AND ".$benutzer." AND ".$projekt; 
        $inc_string = "?".$inc_monat."&".$inc_benutzer."&".$inc_projekt; }
elseif($monat && $benutzer) {
	$monat_benutzer = " WHERE ".$monat." AND ".$benutzer; 
        $inc_string = "?".$inc_monat."&".$inc_benutzer; }
elseif($monat && $projekt) {
	$monat_benutzer = " WHERE ".$monat." AND ".$projekt; 
        $inc_string = "?".$inc_monat."&".$inc_projekt; }
elseif($benutzer && $projekt) {
	$monat_benutzer = " WHERE ".$benutzer." AND ".$projekt; 
        $inc_string = "?".$inc_benutzer."&".$inc_projekt; }
elseif($monat) {
	$monat_benutzer = " WHERE ".$monat; 
        $inc_string = "?".$inc_monat;}
elseif($benutzer) {
	$monat_benutzer = " WHERE ".$benutzer; 
        $inc_string = "?".$inc_benutzer; }
elseif($projekt) {
	$monat_benutzer = " WHERE ".$projekt; 
        $inc_string = "?".$inc_projekt; }
else {
	$monat_benutzer = ""; 
        $inc_string = ""; }
 
 
 
//////////////////////////// Inhalt des PDFs als HTML-Code \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 
 
// Erstellung des HTML-Codes. Dieser HTML-Code definiert das Aussehen eures PDFs.
// tcpdf unterstützt recht viele HTML-Befehle. Die Nutzung von CSS ist allerdings
// stark eingeschränkt.
 
$html1 = '<html>
	<head>
		<title>Übersicht vom '.$datum.'</title>
	</head>
	<body>
 
	<h2>Übersicht aller Touren</h2>
		<table border="1" width="100%">
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
         <th>Aktion</th>
      </tr>   
    </thead>
    <tbody>';

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

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpsw']);

       $sql = "SELECT * FROM tourdaten".$monat_benutzer;
       
       foreach ($pdo->query($sql) as $row) {
	$html1 .= '
       <tr>
         <td>'.$row['id'].'</td>
         <td>'.$row['projekt'].'</td>
         <td>'.$row['verteiler'].'</td>
         <td>'.$row['datum'].'</td>
         <td>'.$row['startzeit'].' Uhr</td>
         <td>'.$row['dauer'].' h:min</td>
         <td>'.$row['pause'].' h:min</td>
         <td>'.$row['flyer'].'</td>
         <td>'.$row['gebiet'].'</td>
         <td>'.$row['arbeitszeit'].' h:min</td>
         <td>
		<a href="http://www.abrechnungstool.de/upload/'.$row['datei'].'" download="'.$row['datei'].'">Nachweis</a>
	</td>
      </tr>    '; 


	$zeit_h += strptime($row['arbeitszeit'], "%H:%M")['tm_hour'];
	$dauer_h += strptime($row['dauer'], "%H:%M")['tm_hour'];
	$pause_h += strptime($row['pause'], "%H:%M")['tm_hour'];

	$zeit_min += strptime($row['arbeitszeit'], "%H:%M")['tm_min'];
	$dauer_min += strptime($row['dauer'], "%H:%M")['tm_min'];
	$pause_min += strptime($row['pause'], "%H:%M")['tm_min'];

} 

$html1 .= '
             </tbody>   
  	</table>
    </body>
</html>';

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


$html2 = '<html>
	<head>
		<title>Übersicht vom '.$datum.'</title>
	</head>
	<body>
    <h1>Gesamtauswertung</h1>
    <p>'.$name.'<br/>
    '.$str_nr.'<br/>
    '.$plz_ort.'</p>
   
    <p>Datum: '.$datum.'<br/>
    Ort: '.$plz_ort.'</p>
    
    <p>Monat(e) : '.$inc_monat.'</p>
    <p>Projekt(e) : '.$inc_projekt.'</p>
    <p>Verteiler : '.$inc_benutzer.'</p>

    <p>Stundenlohn: '.$lohn.' Euro</p>
    <p>Dauer total: '.$dauer_h.' Stunden '.$dauer_min.' Minuten</p>
    <p>Pause total: '.$pause_h.' Stunden '.$pause_min.' Minuten</p>
    <p>Arbeitszeit total: '.$zeit_h.' Stunden '.$zeit_min.' Minuten</p>
    <p>Arbeitsstunden total: '.$total.' Stunden</p>
    <p>Lohn total: '.$tlohn.' Euro</p>
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
$pdf->SetTitle('Übersicht '.$nummer);
$pdf->SetSubject('Übersicht '.$nummer);
 
 
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
$pdf->AddPage('L');
 
// Fügt den HTML Code in das PDF Dokument ein
$pdf->writeHTML($html1, true, false, true, false, '');

// Neue Seite
$pdf->AddPage('L');
 
// Fügt den HTML Code in das PDF Dokument ein
$pdf->writeHTML($html2, true, false, true, false, '');
 
//Ausgabe der PDF
 
if(isset($_GET['speichern']))
{ 
	//Variante 2: PDF im Verzeichnis abspeichern:
	$pdf->Output($_SERVER['DOCUMENT_ROOT'].'ansicht/'.$_SESSION['domain'].'.pdf', 'F');
	$html .= 'PDF herunterladen: <a href="'.$_SERVER['DOCUMENT_ROOT'].'ansicht/'.$_SESSION['domain'].'.pdf">'.$_SESSION['domain'].'.pdf</a>';
	$meldung = "meldung=PDF gespeichert. PDF ist erreichbar unter https://abrechnungstool.de/".$_SESSION['domain'];
	header("Location: ".$url.$meldung);
} 
else {
	//Variante 1: PDF direkt an den Benutzer senden:
	$pdf->Output($pdfName, 'I'); }
?>