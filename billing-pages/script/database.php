<?php
require_once 'config.php';

class Database {
    private static $instance = null;
    private $conn = null;
    
    private function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            log_error($e->getMessage());
            die("Database connection failed. Please try again later.");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function query($sql, $params = [], $types = '') {
        try {
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Query preparation failed: " . $this->conn->error);
            }
            
            if (!empty($params)) {
                if (empty($types)) {
                    $types = str_repeat('s', count($params));
                }
                $stmt->bind_param($types, ...$params);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Query execution failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            log_error($e->getMessage());
            return false;
        }
    }
    
    public function insert($table, $data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        
        $sql = "INSERT INTO $table (" . implode(',', $fields) . ") VALUES ($placeholders)";
        
        return $this->query($sql, $values);
    }
    
    public function update($table, $data, $where, $where_params = []) {
        $fields = array_keys($data);
        $values = array_values($data);
        
        $set = implode('=?,', $fields) . '=?';
        $sql = "UPDATE $table SET $set WHERE $where";
        
        $params = array_merge($values, $where_params);
        
        return $this->query($sql, $params);
    }
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->query($sql, $params);
    }
    
    public function select($table, $fields = '*', $where = '', $params = [], $order = '', $limit = '') {
        $sql = "SELECT $fields FROM $table";
        
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        
        if (!empty($order)) {
            $sql .= " ORDER BY $order";
        }
        
        if (!empty($limit)) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->query($sql, $params);
    }
    
    public function beginTransaction() {
        $this->conn->begin_transaction();
    }
    
    public function commit() {
        $this->conn->commit();
    }
    
    public function rollback() {
        $this->conn->rollback();
    }
    
    public function escape($value) {
        return $this->conn->real_escape_string($value);
    }
    
    public function getLastInsertId() {
        return $this->conn->insert_id;
    }
    
    public function getAffectedRows() {
        return $this->conn->affected_rows;
    }
    
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Initialize database connection
$db = Database::getInstance();
$conn = $db->getConnection(); 