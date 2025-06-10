<?php
$this->setPageTitle('Dashboard');
$this->addBreadcrumb('Dashboard', '/');
?>

<div class="row">
    <!-- Statistics Cards -->
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <h5 class="card-title">Total Tasks</h5>
                <h2 class="stat-number"><?php echo $stats['total_tasks']; ?></h2>
                <a href="/tasks.php" class="btn btn-link">View All Tasks</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <h5 class="card-title">Money Entries</h5>
                <h2 class="stat-number"><?php echo $stats['total_money_entries']; ?></h2>
                <a href="/money.php" class="btn btn-link">View All Entries</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <h5 class="card-title">Work Entries</h5>
                <h2 class="stat-number"><?php echo $stats['total_work_entries']; ?></h2>
                <a href="/work.php" class="btn btn-link">View All Entries</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Quick Actions -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="/tasks/create.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus-circle"></i> Add New Task
                    </a>
                    <a href="/money/create.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-money-bill"></i> Add Money Entry
                    </a>
                    <a href="/work/create.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-clock"></i> Add Work Entry
                    </a>
                    <a href="/reports.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-bar"></i> View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Activities</h5>
            </div>
            <div class="card-body">
                <?php if (empty($activities)): ?>
                    <p class="text-muted">No recent activities.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($activities as $activity): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($activity['action']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo date('M d, Y H:i', strtotime($activity['created_at'])); ?>
                                    </small>
                                </div>
                                <p class="mb-1">
                                    <?php echo htmlspecialchars($activity['description']); ?>
                                </p>
                                <small class="text-muted">
                                    By <?php echo htmlspecialchars($activity['username']); ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Task Status Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="taskStatusChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Monthly Work Hours</h5>
            </div>
            <div class="card-body">
                <canvas id="workHoursChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Task Status Chart
    const taskStatusCtx = document.getElementById('taskStatusChart').getContext('2d');
    new Chart(taskStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'In Progress', 'Completed'],
            datasets: [{
                data: [12, 19, 3],
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Work Hours Chart
    const workHoursCtx = document.getElementById('workHoursChart').getContext('2d');
    new Chart(workHoursCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Work Hours',
                data: [65, 59, 80, 81, 56, 55],
                borderColor: '#007bff',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script> 