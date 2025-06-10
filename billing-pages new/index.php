<?php
require_once 'script/config.php';
require_once 'script/auth.php';
require_once 'script/template.php';
require_once 'script/database.php';

// Check if user is logged in
$auth->requireLogin();

// Get statistics
$db = Database::getInstance();

// Get total tasks
$result = $db->query("SELECT COUNT(*) as total FROM tasks");
$totalTasks = $result->fetch_assoc()['total'];

// Get total money entries
$result = $db->query("SELECT COUNT(*) as total FROM money");
$totalMoney = $result->fetch_assoc()['total'];

// Get total work entries
$result = $db->query("SELECT COUNT(*) as total FROM work");
$totalWork = $result->fetch_assoc()['total'];

// Get recent activities
$result = $db->query("
    SELECT a.*, u.username 
    FROM activity_log a 
    JOIN users u ON a.user_id = u.id 
    ORDER BY a.created_at DESC 
    LIMIT 5
");
$recentActivities = [];
while ($row = $result->fetch_assoc()) {
    $recentActivities[] = $row;
}

// Set page title
$template->setPageTitle('Dashboard');

// Add breadcrumbs
$template->addBreadcrumb('Dashboard');

// Start output buffering
ob_start();
?>

<div class="row">
    <!-- Statistics Cards -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-tasks fa-3x text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="card-title mb-0">Tasks</h5>
                        <h2 class="mt-2 mb-0"><?php echo $totalTasks; ?></h2>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="task_overview.php" class="text-decoration-none">
                    View all tasks <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-money-bill fa-3x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="card-title mb-0">Money Entries</h5>
                        <h2 class="mt-2 mb-0"><?php echo $totalMoney; ?></h2>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="money_overview.php" class="text-decoration-none">
                    View all money entries <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-briefcase fa-3x text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="card-title mb-0">Work Entries</h5>
                        <h2 class="mt-2 mb-0"><?php echo $totalWork; ?></h2>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="work_overview.php" class="text-decoration-none">
                    View all work entries <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <a href="task_insert.php" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i> Add Task
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="money_insert.php" class="btn btn-success w-100">
                            <i class="fas fa-plus me-2"></i> Add Money Entry
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="work_insert.php" class="btn btn-info w-100">
                            <i class="fas fa-plus me-2"></i> Add Work Entry
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="task_evaluation.php" class="btn btn-secondary w-100">
                            <i class="fas fa-chart-bar me-2"></i> View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activities -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Activities</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recentActivities)): ?>
                    <p class="text-muted mb-0">No recent activities.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentActivities as $activity): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-user-circle fa-2x text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($activity['username']); ?></h6>
                                        <p class="mb-1"><?php echo htmlspecialchars($activity['action']); ?></p>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Get the content and render the template
$content = ob_get_clean();
$template->setContent($content);
echo $template->render();
?> 