<!DOCTYPE html>
<html>
<head lang="de">
  <title>Abrechnung-Portal.de</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="bootstrap-5.2.0-beta1/css/bootstrap.min.css" rel="stylesheet">
  <script src="bootstrap-5.2.0-beta1/js/bootstrap.bundle.min.js"></script> 
  <script src="GM_Utils/GPX2GM.js"></script>
		<style>
			.map {width:100%;height:100vh;}
			@media screen and (min-width:700px) {
				.map { display:inline-block; width: 72%; width:calc(75% - 25px); height:95vh; height:calc(100vh - 10px); margin:0; padding:0 }
			}
		</style>
</head>
<body>
<div class="container mt-3">

<?php 

$gpx_str = $_GET['gpx_str'];
if($gpx_str) { echo '
  <h1 class="h3">Kartenansicht über alle ausgewählten Touren</h1>
  <div class="map gpxview:'.$gpx_str.':OSMDE" style="width:1024px;height:1024px;">
    <noscript><p>Zum Anzeigen der Karte wird Javascript benötigt.</p></noscript>
  </div>';
 }
?>
</div>
</body>
</html>