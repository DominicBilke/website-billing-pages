<?php
require('script/inc.php');
check_auth();

if (isset($_GET['export'])) {
    // Export as PDF
    $sql = "SELECT * FROM tour_billing ORDER BY date DESC";
    $result = mysqli_query($conn, $sql);
    $html = '<h2>Tour Billing Export</h2><table border="1" cellpadding="4"><thead><tr><th>Date</th><th>Tour</th><th>Project</th><th>Hours</th><th>Rate</th><th>Total</th><th>Description</th></tr></thead><tbody>';
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>';
        $html .= '<td>' . format_date($row['date']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['tour_name']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['project']) . '</td>';
        $html .= '<td>' . $row['hours'] . '</td>';
        $html .= '<td>' . format_currency($row['rate']) . '</td>';
        $html .= '<td>' . format_currency($row['total']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['description']) . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    generate_pdf($html, 'tour_billing_export');
    exit;
}

$page_title = 'Export Tour Billing';
ob_start();
?>
<div class="card">
    <div class="card-header"><h4>Export Tour Billing</h4></div>
    <div class="card-body">
        <p>Click the button below to export all tour billing entries as a PDF.</p>
        <a href="tour_export.php?export=1" class="btn btn-success export-pdf"><i class="fas fa-file-pdf"></i> Export to PDF</a>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once('templates/base.php'); 