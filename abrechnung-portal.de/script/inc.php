<?php

    include 'script/inc_start.php';

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
?>