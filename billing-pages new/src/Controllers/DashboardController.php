<?php
namespace BillingSystem\Controllers;

use BillingSystem\Core\Controller;

class DashboardController extends Controller {
    public function index() {
        $this->requireLogin();

        // Get statistics
        $stats = [
            'total_tasks' => $this->getTotalTasks(),
            'total_money_entries' => $this->getTotalMoneyEntries(),
            'total_work_entries' => $this->getTotalWorkEntries()
        ];

        // Get recent activities
        $activities = $this->getRecentActivities();

        return $this->render('dashboard/index.php', [
            'stats' => $stats,
            'activities' => $activities
        ]);
    }

    private function getTotalTasks() {
        $sql = "SELECT COUNT(*) as total FROM tasks";
        $this->db->query($sql);
        return $this->db->single()['total'];
    }

    private function getTotalMoneyEntries() {
        $sql = "SELECT COUNT(*) as total FROM money_entries";
        $this->db->query($sql);
        return $this->db->single()['total'];
    }

    private function getTotalWorkEntries() {
        $sql = "SELECT COUNT(*) as total FROM work_entries";
        $this->db->query($sql);
        return $this->db->single()['total'];
    }

    private function getRecentActivities() {
        $sql = "SELECT a.*, u.username 
                FROM activity_log a 
                LEFT JOIN users u ON a.user_id = u.id 
                ORDER BY a.created_at DESC 
                LIMIT 10";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
} 