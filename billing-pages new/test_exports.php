<?php
require('script/inc.php');

function test_pdf_export($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    
    if ($http_code == 200 && strpos($content_type, 'application/pdf') !== false) {
        echo "✅ PDF export from '$url' successful\n";
        return true;
    } else {
        echo "❌ PDF export from '$url' failed (HTTP Code: $http_code, Content-Type: $content_type)\n";
        return false;
    }
}

function test_chart_generation($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    
    if ($http_code == 200 && strpos($content_type, 'image/') !== false) {
        echo "✅ Chart generation from '$url' successful\n";
        return true;
    } else {
        echo "❌ Chart generation from '$url' failed (HTTP Code: $http_code, Content-Type: $content_type)\n";
        return false;
    }
}

// Run export tests
echo "Starting export and chart tests...\n\n";

// Test PDF exports
$pdf_exports = [
    'company_export.php',
    'tour_export.php',
    'work_export.php',
    'task_export.php',
    'money_export.php'
];

foreach ($pdf_exports as $export) {
    test_pdf_export($export);
}

// Test chart generation
$charts = [
    'company_evaluation_chart.php',
    'tour_evaluation_chart.php',
    'work_evaluation_chart.php',
    'task_evaluation_chart.php',
    'money_evaluation_chart.php'
];

foreach ($charts as $chart) {
    test_chart_generation($chart);
}

echo "\nExport and chart tests completed.\n"; 