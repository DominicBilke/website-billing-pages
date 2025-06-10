<?php
if($_SESSION['status'] == "admin") {
?>
<div class="container">
    <h2>Administration</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">User Management</h5>
                    <p class="card-text">Manage users, their roles, and permissions.</p>
                    <a href="index.php?menu1=Administration&menu2=UserManagement" class="btn btn-primary">Manage Users</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Domain Management</h5>
                    <p class="card-text">Manage domains and their settings.</p>
                    <a href="index.php?menu1=Administration&menu2=DomainManagement" class="btn btn-primary">Manage Domains</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">System Settings</h5>
                    <p class="card-text">Configure system-wide settings and preferences.</p>
                    <a href="index.php?menu1=Administration&menu2=SystemSettings" class="btn btn-primary">System Settings</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Backup & Restore</h5>
                    <p class="card-text">Manage system backups and restore points.</p>
                    <a href="index.php?menu1=Administration&menu2=BackupRestore" class="btn btn-primary">Backup & Restore</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
} else {
    echo "<div class='alert alert-danger'>You do not have permission to access the administration area.</div>";
}
?> 