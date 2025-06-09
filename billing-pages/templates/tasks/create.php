<?php
$this->setPageTitle('Create Task');
$this->addBreadcrumb('Tasks', '/tasks.php');
$this->addBreadcrumb('Create Task', '/tasks/create.php');
?>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Create New Task</h5>
    </div>
    <div class="card-body">
        <form action="/tasks/store.php" method="POST" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-8">
                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                        <div class="invalid-feedback">
                            Please provide a title.
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>

                    <!-- Status and Priority -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority" required>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Due Date -->
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" required>
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
                                <option value="<?php echo $user['id']; ?>">
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
                            <option value="">Select Items</option>
                            <?php foreach ($related_items as $item): ?>
                                <option value="<?php echo $item['id']; ?>">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tags -->
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" class="form-control" id="tags" name="tags" placeholder="Enter tags separated by commas">
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Task
                </button>
                <a href="/tasks.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
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
</script> 