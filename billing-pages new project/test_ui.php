<?php
require('script/inc.php');

function test_page_load($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        echo "✅ Page '$url' loaded successfully\n";
        return true;
    } else {
        echo "❌ Page '$url' failed to load (HTTP Code: $http_code)\n";
        return false;
    }
}

function test_form_submission($url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200 || $http_code == 302) {
        echo "✅ Form submission to '$url' successful\n";
        return true;
    } else {
        echo "❌ Form submission to '$url' failed (HTTP Code: $http_code)\n";
        return false;
    }
}

// Run UI tests
echo "Starting UI tests...\n\n";

// Test main pages
$pages = [
    'index.php',
    'login.php',
    'administration.php',
    'company_insert.php',
    'company_overview.php',
    'company_evaluation.php',
    'tour_insert.php',
    'tour_overview.php',
    'tour_evaluation.php',
    'work_insert.php',
    'work_overview.php',
    'work_evaluation.php',
    'task_insert.php',
    'task_overview.php',
    'task_evaluation.php',
    'money_insert.php',
    'money_overview.php',
    'money_evaluation.php'
];

foreach ($pages as $page) {
    test_page_load($page);
}

// Test form submissions
$forms = [
    'company_insert.php' => [
        'company_name' => 'Test Company',
        'project' => 'Test Project',
        'date' => date('Y-m-d'),
        'hours' => 10,
        'rate' => 100,
        'description' => 'Test description'
    ],
    'tour_insert.php' => [
        'tour_name' => 'Test Tour',
        'project' => 'Test Project',
        'date' => date('Y-m-d'),
        'hours' => 8,
        'rate' => 80,
        'description' => 'Test description'
    ],
    'work_insert.php' => [
        'work_name' => 'Test Work',
        'project' => 'Test Project',
        'date' => date('Y-m-d'),
        'hours' => 5,
        'rate' => 50,
        'description' => 'Test description'
    ],
    'task_insert.php' => [
        'task_name' => 'Test Task',
        'project' => 'Test Project',
        'date' => date('Y-m-d'),
        'hours' => 3,
        'rate' => 30,
        'description' => 'Test description'
    ],
    'money_insert.php' => [
        'money_name' => 'Test Money',
        'project' => 'Test Project',
        'date' => date('Y-m-d'),
        'amount' => 500,
        'description' => 'Test description'
    ]
];

foreach ($forms as $page => $data) {
    test_form_submission($page, $data);
}

echo "\nUI tests completed.\n"; 