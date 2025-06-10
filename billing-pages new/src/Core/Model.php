<?php
namespace BillingSystem\Core;

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        $this->db = Application::getInstance()->getDb();
    }

    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function create($data) {
        $fields = array_intersect_key($data, array_flip($this->fillable));
        $columns = implode(', ', array_keys($fields));
        $values = ':' . implode(', :', array_keys($fields));
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($values)";
        $this->db->query($sql);
        
        foreach ($fields as $key => $value) {
            $this->db->bind(":$key", $value);
        }
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, $data) {
        $fields = array_intersect_key($data, array_flip($this->fillable));
        $set = implode(', ', array_map(function($field) {
            return "$field = :$field";
        }, array_keys($fields)));
        
        $sql = "UPDATE {$this->table} SET $set WHERE {$this->primaryKey} = :id";
        $this->db->query($sql);
        
        $this->db->bind(':id', $id);
        foreach ($fields as $key => $value) {
            $this->db->bind(":$key", $value);
        }
        
        return $this->db->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function where($column, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE $column = :value";
        $this->db->query($sql);
        $this->db->bind(':value', $value);
        return $this->db->resultSet();
    }

    public function paginate($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM {$this->table} LIMIT :limit OFFSET :offset";
        $this->db->query($sql);
        $this->db->bind(':limit', $perPage);
        $this->db->bind(':offset', $offset);
        
        $items = $this->db->resultSet();
        
        // Get total count
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $this->db->query($sql);
        $total = $this->db->single()['total'];
        
        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }

    protected function hideAttributes($data) {
        if (is_array($data)) {
            foreach ($this->hidden as $attribute) {
                unset($data[$attribute]);
            }
        }
        return $data;
    }
} 