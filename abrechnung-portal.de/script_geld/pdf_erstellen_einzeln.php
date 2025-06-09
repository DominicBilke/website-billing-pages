<?php
 
session_start();

    $url = (empty($_SERVER['HTTPS'])) ? 'http://' : 'https://';
    $url .= $_SERVER['HTTP_HOST'];
    $url .= $_SERVER['REQUEST_URI']; // $url enthält jetzt die komplette URL
    if(strpos($url, "?"))
	    $url .= "&";
    else
   	    $url .= "?";
    $_SESSION['url'] = $url;

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
$benutzer_id = "`benutzer_id`=".$_SESSION['geld_id'];
if($monat && $benutzer && $projekt) {
	$monat_benutzer = " WHERE ".$monat." AND ".$benutzer." AND ".$projekt." AND ".$benutzer_id; 
        $inc_string = "?".$inc_monat."&".$inc_benutzer."&".$inc_projekt; }
elseif($monat && $benutzer) {
	$monat_benutzer = " WHERE ".$monat." AND ".$benutzer." AND ".$benutzer_id; 
        $inc_string = "?".$inc_monat."&".$inc_benutzer; }
elseif($monat && $projekt) {
	$monat_benutzer = " WHERE ".$monat." AND ".$projekt." AND ".$benutzer_id; 
        $inc_string = "?".$inc_monat."&".$inc_projekt; }
elseif($benutzer && $projekt) {
	$monat_benutzer = " WHERE ".$benutzer." AND ".$projekt." AND ".$benutzer_id; 
        $inc_string = "?".$inc_benutzer."&".$inc_projekt; }
elseif($monat) {
	$monat_benutzer = " WHERE ".$monat." AND ".$benutzer_id; 
        $inc_string = "?".$inc_monat;}
elseif($benutzer) {
	$monat_benutzer = " WHERE ".$benutzer." AND ".$benutzer_id; 
        $inc_string = "?".$inc_benutzer; }
elseif($projekt) {
	$monat_benutzer = " WHERE ".$projekt." AND ".$benutzer_id; 
        $inc_string = "?".$inc_projekt; }
else {
	$monat_benutzer = " WHERE ".$benutzer_id; 
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
 
	<h2>Übersicht aller Geldbeträge</h2>
		<table border="1" width="100%" cellpadding="2">
    		<thead>	
      <tr>         <th>Nr.</th>
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
    <tbody>';       

       $einnahmen = 0;
       $ausgaben = 0;
       $total = 0;
       $name = " ";
       $str_nr = " ";
       $plz_ort = " ";

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['geld_dbname'], $_SESSION['geld_dbuser'], $_SESSION['geld_dbpsw']);

	if($_SESSION['geld_status'] == 'firma' )
 	 { 
		$sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'WHERE `benutzer_id`'));
		if(!strrpos($monat_benutzer, 'WHERE `benutzer_id`')) $sql = "SELECT * FROM tourdaten".substr($monat_benutzer, 0, strrpos($monat_benutzer, 'AND `benutzer_id`'));
	 }
	 else $sql = "SELECT * FROM tourdaten".$monat_benutzer;
       

       $i = 0;

       foreach ($pdo->query($sql) as $row) {

        $i++;

	if($row['gpx'] && pathinfo($row['gpx'], PATHINFO_EXTENSION)) $gpx = '<a href="https://www.geldabrechnung.de/upload/'.$row['gpx'].'" download="'.$row['gpx'].'">Zahlung</a>';
	else $gpx = '';


	$html1.= '
       <tr>
         <td>'.$i.'</td>
         <td>'.$row['projekt'].'</td>
         <td>'.$row['verteiler'].'</td>
         <td>'.$row['datum'].'</td>
         <td>'.$row['startzeit'].'</td>
         <td>'.$row['dauer'].'</td>
         <td>'.$row['pause'].'</td>
         <td align="right">'.number_format($row['gebiet'], 2).'</td>
         <td>'.$row['arbeitszeit'].'</td>
         <td><a href="https://www.abrechnung-portal.de/geld_upload/'.$row['datei'].'" download="'.$row['datei'].'">Rechnung</a><br/>'.$gpx.'</td>
      </tr>    '; 

		$geld = floatval($row['gebiet']);
		if($geld > 0) $einnahmen += $geld;
		elseif($geld < 0) $ausgaben += $geld;
		$total += $geld;

} 

$html1 .= '
             </tbody>   
  		</table>
</body>
</html>';

       $sql = "SELECT * FROM benutzerdaten WHERE benutzer_id='".$_SESSION['geld_id']."'";
       
       foreach ($pdo->query($sql) as $row) {
            $lohn = $row['lohn']; 
            $name = $row['vorname']." ".$row['nachname'];
	    $str_nr = $row['strasse_nr'];
	    $plz_ort = $row['plz_ort'];
	    $firma = $_SESSION['firma'];
}


$html2 = '<html>
	<head>
		<title>Übersicht vom '.$datum.'</title>
	</head>
	<body>
    <h1>Auswertung</h1>
    <p>'.$firma.'<br/>
    '.$name.'<br/>
    '.$str_nr.'<br/>
    '.$plz_ort.'</p>
   
    <p>Datum: '.$datum.'<br/>
    Ort: '.$plz_ort.'</p>
    
    <p>Monat(e) : '.$inc_monat.'</p>
    <p>Projekt(e) : '.$inc_projekt.'</p>
    <p>Arbeiter : '.$inc_benutzer.'</p>
    <p></p>
    <p>Einnahmen: '.$einnahmen.' EUR</p>
    <p>Ausgaben: '.$ausgaben.' EUR</p>
    <p>Stand am Ende: '.$total.' EUR</p>
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
	$pdf->Output('ansicht/'.$_SESSION['domain'].'.pdf', 'F');
	$html .= 'PDF herunterladen: <a href="ansicht/'.$_SESSION['domain'].'.pdf">'.$_SESSION['domain'].'.pdf</a>';
	$meldung = "PDF gespeichert. PDF ist erreichbar unter https://abrechnungstool.de/".$_SESSION['domain'];
	header("Location: ".$url.$meldung);
} 
else {
	//Variante 1: PDF direkt an den Benutzer senden:
	$pdf->Output($pdfName, 'I'); }
?>