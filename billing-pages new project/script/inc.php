<?php
session_start();

// Load configuration
require_once(__DIR__ . '/../config.php');

// Database connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set character set
mysqli_set_charset($conn, "utf8");

// Common functions
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

function check_auth() {
    if (!isset($_SESSION['id'])) {
        header("Location: login.php");
        exit();
    }
}

function check_admin() {
    if (!isset($_SESSION['status']) || $_SESSION['status'] != 'admin') {
        header("Location: index.php");
        exit();
    }
}

function format_date($date) {
    return date(DATE_FORMAT, strtotime($date));
}

function format_currency($amount) {
    return number_format($amount, CURRENCY_DECIMALS, CURRENCY_DECIMAL_SEPARATOR, CURRENCY_THOUSANDS_SEPARATOR);
}

function generate_pdf($html, $filename) {
    require_once(__DIR__ . '/../tcpdf/tcpdf.php');
    
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor(SITE_NAME);
    $pdf->SetTitle($filename);
    
    $pdf->setHeaderFont(Array('helvetica', '', 12));
    $pdf->setFooterFont(Array('helvetica', '', 8));
    
    $pdf->SetDefaultMonospacedFont('courier');
    
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    
    $pdf->AddPage();
    
    $pdf->writeHTML($html, true, false, true, false, '');
    
    $pdf->Output($filename . '.pdf', 'I');
}

function generate_graph($data, $labels, $type = 'bar', $title = '') {
    if (!file_exists(__DIR__ . '/../jpgraph/jpgraph.php')) {
        return false;
    }

    require_once(__DIR__ . '/../jpgraph/jpgraph.php');
    require_once(__DIR__ . '/../jpgraph/jpgraph_bar.php');
    require_once(__DIR__ . '/../jpgraph/jpgraph_line.php');
    require_once(__DIR__ . '/../jpgraph/jpgraph_pie.php');
    
    $graph = new Graph(800, 600);
    $graph->SetScale('textlin');
    
    $graph->title->Set($title);
    $graph->xaxis->SetTickLabels($labels);
    
    switch ($type) {
        case 'bar':
            $plot = new BarPlot($data);
            $plot->SetFillColor('blue');
            break;
        case 'line':
            $plot = new LinePlot($data);
            $plot->SetColor('red');
            break;
        case 'pie':
            $plot = new PiePlot($data);
            break;
        default:
            $plot = new BarPlot($data);
            $plot->SetFillColor('blue');
    }
    
    $graph->Add($plot);
    $graph->Stroke();
}

function get_total_billed($table) {
    global $conn;
    $sql = "SELECT SUM(total) as total FROM $table";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function get_billing_summary($table) {
    global $conn;
    $name_field = str_replace('_billing', '_name', $table);
    $sql = "SELECT $name_field, SUM(total) as total_amount, COUNT(*) as entry_count 
            FROM $table 
            GROUP BY $name_field 
            ORDER BY total_amount DESC";
    $result = mysqli_query($conn, $sql);
    $summary = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $summary[] = $row;
    }
    return $summary;
}

function validate_date($date) {
    $d = DateTime::createFromFormat(DATE_FORMAT, $date);
    return $d && $d->format(DATE_FORMAT) === $date;
}

function validate_numeric($value) {
    return is_numeric($value) && $value >= 0;
}

function get_pagination($total_records, $records_per_page = RECORDS_PER_PAGE, $current_page = 1) {
    $total_pages = ceil($total_records / $records_per_page);
    $offset = ($current_page - 1) * $records_per_page;
    
    return [
        'total_pages' => $total_pages,
        'offset' => $offset,
        'current_page' => $current_page
    ];
}

function get_search_condition($search, $fields) {
    if (empty($search)) {
        return '';
    }
    
    $conditions = [];
    foreach ($fields as $field) {
        $conditions[] = "$field LIKE '%" . sanitize_input($search) . "%'";
    }
    
    return "WHERE " . implode(' OR ', $conditions);
}

function display_message($type, $message) {
    return "<div class='alert alert-$type'>$message</div>";
}

function redirect_with_message($url, $type, $message) {
    header("Location: $url?$type=" . urlencode($message));
    exit();
}

function log_error($message) {
    error_log(date(DATETIME_FORMAT) . " - " . $message . "\n", 3, __DIR__ . '/../logs/error.log');
}

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?> 