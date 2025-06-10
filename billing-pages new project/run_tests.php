<?php
echo "Starting comprehensive system tests...\n\n";

// Run system tests
echo "=== Running System Tests ===\n";
include('test_system.php');
echo "\n";

// Run UI tests
echo "=== Running UI Tests ===\n";
include('test_ui.php');
echo "\n";

// Run export tests
echo "=== Running Export and Chart Tests ===\n";
include('test_exports.php');
echo "\n";

echo "All tests completed.\n";
echo "Please check the results above for any errors or warnings.\n";
echo "If you see any ❌ symbols, those components need attention.\n"; 