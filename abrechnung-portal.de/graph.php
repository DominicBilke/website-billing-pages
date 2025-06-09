<?php
session_start();
require_once ('jpgraph/src/jpgraph.php');
require_once ('jpgraph/src/jpgraph_bar.php');

    if(isset($_GET['monat'])) {
      $monat = $_GET['monat'];
      $inc_monat = 'monat='.$_GET['monat'];
      if($monat == 'alle') $monat = "";
      else $monat = "`monat` = '".$monat."'"; }
    else {
      	$monat = "";
	$inc_monat = ''; }

    if(isset($_GET['benutzer'])) {
      $benutzer = $_GET['benutzer'];
      $inc_benutzer = 'benutzer='.$_GET['benutzer'];
      if($benutzer == 'alle') $benutzer = "";
      else $benutzer = "`verteiler` = '".$benutzer."'"; }
    else {
      	$benutzer = "";
	$inc_benutzer = ''; }

    if(isset($_GET['projekt'])) {
      $projekt = $_GET['projekt'];
      $inc_projekt = 'projekt='.$_GET['projekt'];
      if($projekt == 'alle') $projekt = "";
      else $projekt = "`projekt` = '".$projekt."'"; }
    else {
      	$projekt = "";
	$inc_projekt = ''; }
$benutzer_id = "`benutzer_id`=".$_SESSION['id'];
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

$pdo = new PDO('mysql:host=localhost;dbname='.$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpsw']);

       $sql = "SELECT * FROM tourdaten".$monat_benutzer;
       $i = 0;
       foreach ($pdo->query($sql) as $row) {
		$geld[$i] = floatval($row['gebiet']);
		$kategorie[$i] = ($i+1).': '.$row['pause'];
                $i++;
	}

  
// Set the basic parameters of the graph
$graph = new Graph(800,$i*15+200,'auto');
$graph->SetScale('textlin');
 
// Rotate graph 90 degrees and set margin
$graph->Set90AndMargin(50,20,50,30);
 
// Nice shadow
$graph->SetShadow();
 
// Setup title
$graph->title->Set('Darstellung der Kategorien und des Betrages');
 
// Setup X-axis
$graph->xaxis->SetTickLabels($kategorie);
 
// Some extra margin looks nicer
$graph->xaxis->SetLabelMargin(10);
 
// Label align for X-axis
$graph->xaxis->SetLabelAlign('right','center');
 
// Add some grace to y-axis so the bars doesn't go
// all the way to the end of the plot area
$graph->yaxis->scale->SetGrace(20);
 
// We don't want to display Y-axis
//$graph->yaxis->Hide();
 
// Now create a bar pot
$bplot = new BarPlot($geld);
$bplot->SetFillColor('orange');
$bplot->SetShadow();
 
//You can change the width of the bars if you like
//$bplot->SetWidth(0.5);
 
// We want to display the value of each bar at the top
$bplot->value->Show();
$bplot->value->SetFont(FF_ARIAL,FS_BOLD,12);
$bplot->value->SetAlign('left','center');
$bplot->value->SetColor('black','darkred');
$bplot->value->SetFormat('%.1f mkr');
 
// Add the bar to the graph
$graph->Add($bplot);
 
// .. and stroke the graph
$graph->Stroke();
?>