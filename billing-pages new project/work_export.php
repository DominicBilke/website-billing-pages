<?php
require('script/inc.php');
check_auth();
require_once('tcpdf/tcpdf.php');

// Fetch all work billing entries
$sql = "SELECT * FROM work_billing ORDER BY date DESC";
$result = mysqli_query($conn, $sql);

$html = '<h2>Work Billing Export</h2>';
$html .= '<table border="1" cellpadding="4">
<thead>
<tr>
<th>Date</th>
<th>Work</th>
<th>Project</th>
<th>Hours</th>
<th>Rate</th>
<th>Total</th>
<th>Description</th>
</tr>
</thead>
<tbody>';

while($row = mysqli_fetch_assoc($result)) {
    $html .= '<tr>';
    $html .= '<td>' . format_date($row['date']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['work_name']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['project']) . '</td>';
    $html .= '<td>' . $row['hours'] . '</td>';
    $html .= '<td>' . format_currency($row['rate']) . '</td>';
    $html .= '<td>' . format_currency($row['total']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['description']) . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('work_billing_export.pdf', 'I');
exit; 