<?php
require('script/inc.php');

// Get summary data
$sql = "SELECT work_name, SUM(total) as total_billed FROM work_billing GROUP BY work_name ORDER BY total_billed DESC";
$result = mysqli_query($conn, $sql);

$works = [];
$totals = [];
while($row = mysqli_fetch_assoc($result)) {
    $works[] = $row['work_name'];
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
$graph->xaxis->SetTickLabels($works);
$graph->title->Set('Total Billed per Work');

$barplot = new BarPlot($totals);
$barplot->SetFillColor('green');
$graph->Add($barplot);

$graph->xaxis->SetLabelAngle(30);
$graph->Stroke(); 