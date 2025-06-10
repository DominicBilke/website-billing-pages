<?php
require('script/inc.php');

// Get all task billing entries
$sql = "SELECT * FROM task_billing ORDER BY date";
$result = mysqli_query($conn, $sql);

// Create PDF
require_once('tcpdf/tcpdf.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Billing System');
$pdf->SetTitle('Task Billing Export');

$pdf->AddPage();

$pdf->SetFont('helvetica', '', 12);

// Title
$pdf->Cell(0, 10, 'Task Billing Export', 0, 1, 'C');
$pdf->Ln(10);

// Table header
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(25, 7, 'Date', 1, 0, 'C', true);
$pdf->Cell(40, 7, 'Task', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Project', 1, 0, 'C', true);
$pdf->Cell(20, 7, 'Hours', 1, 0, 'C', true);
$pdf->Cell(20, 7, 'Rate', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'Total', 1, 0, 'C', true);
$pdf->Cell(0, 7, 'Description', 1, 1, 'C', true);

// Table data
while($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(25, 6, date('Y-m-d', strtotime($row['date'])), 1);
    $pdf->Cell(40, 6, $row['task_name'], 1);
    $pdf->Cell(30, 6, $row['project'], 1);
    $pdf->Cell(20, 6, $row['hours'], 1);
    $pdf->Cell(20, 6, number_format($row['rate'], 2), 1);
    $pdf->Cell(25, 6, number_format($row['total'], 2), 1);
    $pdf->Cell(0, 6, $row['description'], 1);
    $pdf->Ln();
}

$pdf->Output('task_billing_export.pdf', 'I'); 