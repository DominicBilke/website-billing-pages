<?php
$this->setPageTitle('Edit Task');
$this->addBreadcrumb('Tasks', '/tasks.php');
$this->addBreadcrumb('Edit Task', '/tasks/edit.php?id=' . $task['id']);
?>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Edit Task</h5>
    </div>
    <div class="card-body">
        <form action="/tasks/update.php" method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
            
            <div class="row">
                <div class="col-md-8">
                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                        <div class="invalid-feedback">
                            Please provide a title.
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($task['description']); ?></textarea>
                    </div>

                    <!-- Status and Priority -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" <?php echo $task['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="in_progress" <?php echo $task['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="completed" <?php echo $task['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority" required>
                                    <option value="low" <?php echo $task['priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                                    <option value="medium" <?php echo $task['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                    <option value="high" <?php echo $task['priority'] === 'high' ? 'selected' : ''; ?>>High</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Due Date -->
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo date('Y-m-d', strtotime($task['due_date'])); ?>" required>
                        <div class="invalid-feedback">
                            Please select a due date.
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Assigned To -->
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assigned To</label>
                        <select class="form-select" id="assigned_to" name="assigned_to" required>
                            <option value="">Select User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>" <?php echo $task['assigned_to'] == $user['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Please select a user.
                        </div>
                    </div>

                    <!-- Related Items -->
                    <div class="mb-3">
                        <label for="related_items" class="form-label">Related Items</label>
                        <select class="form-select" id="related_items" name="related_items[]" multiple>
                            <?php foreach ($related_items as $item): ?>
                                <option value="<?php echo $item['id']; ?>" <?php echo in_array($item['id'], $task['related_items']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tags -->
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" class="form-control" id="tags" name="tags" value="<?php echo htmlspecialchars(implode(', ', $task['tags'])); ?>" placeholder="Enter tags separated by commas">
                    </div>

                    <!-- Created and Updated Info -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <small class="text-muted">
                                Created: <?php echo date('M d, Y H:i', strtotime($task['created_at'])); ?><br>
                                Last Updated: <?php echo date('M d, Y H:i', strtotime($task['updated_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="/tasks.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="button" class="btn btn-danger float-end" onclick="confirmDelete(<?php echo $task['id']; ?>)">
                    <i class="fas fa-trash"></i> Delete Task
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Form validation
(function() {
    'use strict';
    
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
})();

// Initialize select2 for multiple select
$(document).ready(function() {
    $('#related_items').select2({
        placeholder: 'Select related items',
        allowClear: true
    });
    
    $('#tags').select2({
        tags: true,
        tokenSeparators: [',', ' '],
        placeholder: 'Enter tags'
    });
});

function confirmDelete(taskId) {
    if (confirm('Are you sure you want to delete this task? This action cannot be undone.')) {
        window.location.href = `/tasks/delete.php?id=${taskId}`;
    }
}
</script> 