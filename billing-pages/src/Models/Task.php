<?php
namespace BillingSystem\Models;

use BillingSystem\Core\Model;

class Task extends Model {
    protected $table = 'tasks';
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'assigned_to',
        'created_by'
    ];

    public function getActiveTasks() {
        $sql = "SELECT t.*, u.username as assigned_to_name 
                FROM tasks t 
                LEFT JOIN users u ON t.assigned_to = u.id 
                WHERE t.status != 'completed' 
                ORDER BY t.due_date ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getOverdueTasks() {
        $sql = "SELECT t.*, u.username as assigned_to_name 
                FROM tasks t 
                LEFT JOIN users u ON t.assigned_to = u.id 
                WHERE t.status != 'completed' 
                AND t.due_date < CURDATE() 
                ORDER BY t.due_date ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getTasksByUser($userId) {
        $sql = "SELECT t.*, u.username as assigned_to_name 
                FROM tasks t 
                LEFT JOIN users u ON t.assigned_to = u.id 
                WHERE t.assigned_to = :user_id 
                ORDER BY t.due_date ASC";
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    public function getTasksByStatus($status) {
        $sql = "SELECT t.*, u.username as assigned_to_name 
                FROM tasks t 
                LEFT JOIN users u ON t.assigned_to = u.id 
                WHERE t.status = :status 
                ORDER BY t.due_date ASC";
        $this->db->query($sql);
        $this->db->bind(':status', $status);
        return $this->db->resultSet();
    }

    public function searchTasks($query) {
        $sql = "SELECT t.*, u.username as assigned_to_name 
                FROM tasks t 
                LEFT JOIN users u ON t.assigned_to = u.id 
                WHERE t.title LIKE :query 
                OR t.description LIKE :query 
                ORDER BY t.due_date ASC";
        $this->db->query($sql);
        $this->db->bind(':query', "%$query%");
        return $this->db->resultSet();
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE tasks SET status = :status WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    public function assignTask($id, $userId) {
        $sql = "UPDATE tasks SET assigned_to = :user_id WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }
} 