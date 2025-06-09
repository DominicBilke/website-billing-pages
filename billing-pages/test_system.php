<?php
require('script/inc.php');

function test_database_connection() {
    global $conn;
    if ($conn) {
        echo "✅ Database connection successful\n";
        return true;
    } else {
        echo "❌ Database connection failed\n";
        return false;
    }
}

function test_table_exists($table_name) {
    global $conn;
    $sql = "SHOW TABLES LIKE '$table_name'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        echo "✅ Table '$table_name' exists\n";
        return true;
    } else {
        echo "❌ Table '$table_name' does not exist\n";
        return false;
    }
}

function test_table_structure($table_name) {
    global $conn;
    $sql = "DESCRIBE $table_name";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "✅ Table '$table_name' structure is valid\n";
        return true;
    } else {
        echo "❌ Table '$table_name' structure is invalid\n";
        return false;
    }
}

function test_insert_record($table_name, $data) {
    global $conn;
    $columns = implode(', ', array_keys($data));
    $values = "'" . implode("', '", array_map('addslashes', $data)) . "'";
    $sql = "INSERT INTO $table_name ($columns) VALUES ($values)";
    
    if (mysqli_query($conn, $sql)) {
        $id = mysqli_insert_id($conn);
        echo "✅ Successfully inserted record into '$table_name' (ID: $id)\n";
        return $id;
    } else {
        echo "❌ Failed to insert record into '$table_name': " . mysqli_error($conn) . "\n";
        return false;
    }
}

function test_update_record($table_name, $id, $data) {
    global $conn;
    $updates = [];
    foreach ($data as $key => $value) {
        $updates[] = "$key = '" . addslashes($value) . "'";
    }
    $sql = "UPDATE $table_name SET " . implode(', ', $updates) . " WHERE id = $id";
    
    if (mysqli_query($conn, $sql)) {
        echo "✅ Successfully updated record in '$table_name' (ID: $id)\n";
        return true;
    } else {
        echo "❌ Failed to update record in '$table_name': " . mysqli_error($conn) . "\n";
        return false;
    }
}

function test_delete_record($table_name, $id) {
    global $conn;
    $sql = "DELETE FROM $table_name WHERE id = $id";
    
    if (mysqli_query($conn, $sql)) {
        echo "✅ Successfully deleted record from '$table_name' (ID: $id)\n";
        return true;
    } else {
        echo "❌ Failed to delete record from '$table_name': " . mysqli_error($conn) . "\n";
        return false;
    }
}

// Run tests
echo "Starting system tests...\n\n";

// Test database connection
if (!test_database_connection()) {
    die("Cannot proceed with tests due to database connection failure.\n");
}

// Test all billing tables
$tables = [
    'company_billing',
    'tour_billing',
    'work_billing',
    'task_billing',
    'money_billing'
];

foreach ($tables as $table) {
    echo "\nTesting $table:\n";
    if (test_table_exists($table)) {
        test_table_structure($table);
        
        // Test CRUD operations
        $test_data = [
            'company_billing' => [
                'company_name' => 'Test Company',
                'project' => 'Test Project',
                'date' => date('Y-m-d'),
                'hours' => 10,
                'rate' => 100,
                'total' => 1000,
                'description' => 'Test description'
            ],
            'tour_billing' => [
                'tour_name' => 'Test Tour',
                'project' => 'Test Project',
                'date' => date('Y-m-d'),
                'hours' => 8,
                'rate' => 80,
                'total' => 640,
                'description' => 'Test description'
            ],
            'work_billing' => [
                'work_name' => 'Test Work',
                'project' => 'Test Project',
                'date' => date('Y-m-d'),
                'hours' => 5,
                'rate' => 50,
                'total' => 250,
                'description' => 'Test description'
            ],
            'task_billing' => [
                'task_name' => 'Test Task',
                'project' => 'Test Project',
                'date' => date('Y-m-d'),
                'hours' => 3,
                'rate' => 30,
                'total' => 90,
                'description' => 'Test description'
            ],
            'money_billing' => [
                'money_name' => 'Test Money',
                'project' => 'Test Project',
                'date' => date('Y-m-d'),
                'amount' => 500,
                'description' => 'Test description'
            ]
        ];
        
        $id = test_insert_record($table, $test_data[$table]);
        if ($id) {
            test_update_record($table, $id, $test_data[$table]);
            test_delete_record($table, $id);
        }
    }
}

echo "\nSystem tests completed.\n"; 