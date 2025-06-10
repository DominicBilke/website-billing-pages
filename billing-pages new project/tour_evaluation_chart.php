<?php
require('script/inc.php');

// Get summary data
$sql = "SELECT tour_name, SUM(total) as total_billed FROM tour_billing GROUP BY tour_name ORDER BY total_billed DESC";
$result = mysqli_query($conn, $sql);

$tours = [];
$totals = [];
while($row = mysqli_fetch_assoc($result)) {
    $tours[] = $row['tour_name'];
    $totals[] = $row['total_billed'];
}

if (!file_exists('jpgraph/jpgraph.php')) {
    header('Content-Type: image/png');
    $im = imagecreatetruecolor(600, 200);
    $bg = imagecolorallocate($im, 255, 255, 255);
    $text = imagecolorallocate($im, 0, 0, 0);
    imagefilledrectangle($im, 0, 0, 600, 200, $bg);
    imagestring($im, 5, 10, 90, 'JPGraph not installed', $text);
    imagepng($im);
    imagedestroy($im);
    exit;
}

require_once('jpgraph/jpgraph.php');
require_once('jpgraph/jpgraph_bar.php');

$graph = new Graph(700,400);
$graph->SetScale('textlin');
$graph->xaxis->SetTickLabels($tours);
$graph->title->Set('Total Billed per Tour');

$barplot = new BarPlot($totals);
$barplot->SetFillColor('blue');
$graph->Add($barplot);

$graph->xaxis->SetLabelAngle(30);
$graph->Stroke(); 