<?php
// Database operations
function db_query($sql, $params = [], $types = '') {
    global $conn;
    
    try {
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            throw new Exception("Query preparation failed: " . mysqli_error($conn));
        }
        
        if (!empty($params)) {
            if (empty($types)) {
                $types = str_repeat('s', count($params));
            }
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Query execution failed: " . mysqli_stmt_error($stmt));
        }
        
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    } catch (Exception $e) {
        log_error($e->getMessage());
        return false;
    }
}

// Input validation
function validate_input($data, $type = 'string') {
    switch ($type) {
        case 'email':
            return filter_var($data, FILTER_VALIDATE_EMAIL);
        case 'int':
            return filter_var($data, FILTER_VALIDATE_INT);
        case 'float':
            return filter_var($data, FILTER_VALIDATE_FLOAT);
        case 'date':
            return validate_date($data);
        case 'url':
            return filter_var($data, FILTER_VALIDATE_URL);
        default:
            return !empty(trim($data));
    }
}

// Form handling
function generate_form_token() {
    if (!isset($_SESSION['form_token'])) {
        $_SESSION['form_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['form_token'];
}

function verify_form_token($token) {
    return isset($_SESSION['form_token']) && hash_equals($_SESSION['form_token'], $token);
}

// Session management
function start_secure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 1);
        session_start();
    }
}

function regenerate_session() {
    if (isset($_SESSION['last_regeneration'])) {
        $timeout = 30 * 60; // 30 minutes
        if (time() - $_SESSION['last_regeneration'] > $timeout) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    } else {
        $_SESSION['last_regeneration'] = time();
    }
}

// Error handling
function handle_error($message, $type = 'error') {
    log_error($message);
    if ($type === 'error') {
        throw new Exception($message);
    }
    return false;
}

// Logging
function log_activity($user_id, $action, $details = '') {
    global $conn;
    $sql = "INSERT INTO activity_log (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())";
    db_query($sql, [$user_id, $action, $details], 'iss');
}

// Pagination
function get_pagination_data($total_records, $current_page = 1, $per_page = 10) {
    $total_pages = ceil($total_records / $per_page);
    $current_page = max(1, min($current_page, $total_pages));
    $offset = ($current_page - 1) * $per_page;
    
    return [
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'offset' => $offset,
        'per_page' => $per_page,
        'total_records' => $total_records
    ];
}

// Search
function build_search_query($search, $fields, $table) {
    if (empty($search)) {
        return ['condition' => '', 'params' => []];
    }
    
    $conditions = [];
    $params = [];
    foreach ($fields as $field) {
        $conditions[] = "$field LIKE ?";
        $params[] = "%$search%";
    }
    
    return [
        'condition' => "WHERE " . implode(' OR ', $conditions),
        'params' => $params
    ];
}

// Export
function generate_export_data($table, $fields, $condition = '', $params = []) {
    $sql = "SELECT " . implode(', ', $fields) . " FROM $table $condition";
    $result = db_query($sql, $params);
    
    if (!$result) {
        return false;
    }
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    return $data;
}

// File handling
function handle_file_upload($file, $allowed_types, $max_size = 5242880) {
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new Exception('Invalid file parameters');
    }
    
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new Exception('File too large');
        case UPLOAD_ERR_PARTIAL:
            throw new Exception('File upload incomplete');
        case UPLOAD_ERR_NO_FILE:
            throw new Exception('No file uploaded');
        default:
            throw new Exception('Unknown upload error');
    }
    
    if ($file['size'] > $max_size) {
        throw new Exception('File too large');
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file['tmp_name']);
    
    if (!in_array($mime_type, $allowed_types)) {
        throw new Exception('Invalid file type');
    }
    
    return true;
}

// Date handling
function format_date_range($start_date, $end_date) {
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = $start->diff($end);
    
    return [
        'days' => $interval->days,
        'formatted' => $start->format(DATE_FORMAT) . ' - ' . $end->format(DATE_FORMAT)
    ];
}

// Currency handling
function format_currency_range($amount, $currency = 'EUR') {
    return [
        'amount' => $amount,
        'formatted' => number_format($amount, CURRENCY_DECIMALS, CURRENCY_DECIMAL_SEPARATOR, CURRENCY_THOUSANDS_SEPARATOR),
        'currency' => $currency
    ];
} 