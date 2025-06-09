<?php
require('script/inc.php');

// Get summary data
$sql = "SELECT money_name, SUM(amount) as total_amount FROM money_billing GROUP BY money_name ORDER BY total_amount DESC";
$result = mysqli_query($conn, $sql);

$money_names = [];
$amounts = [];
while($row = mysqli_fetch_assoc($result)) {
    $money_names[] = $row['money_name'];
    $amounts[] = $row['total_amount'];
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
$graph->xaxis->SetTickLabels($money_names);
$graph->title->Set('Total Billed per Money Name');

$barplot = new BarPlot($amounts);
$barplot->SetFillColor('green');
$graph->Add($barplot);

$graph->xaxis->SetLabelAngle(30);
$graph->Stroke(); 