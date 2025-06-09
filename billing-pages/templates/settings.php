<?php
$this->setPageTitle('Settings');
$this->addBreadcrumb('Settings', '/settings.php');
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Settings</h1>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Settings Navigation -->
                    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" 
                                    id="general-tab" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#general" 
                                    type="button" 
                                    role="tab">
                                <i class="fas fa-cog"></i> General
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" 
                                    id="notifications-tab" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#notifications" 
                                    type="button" 
                                    role="tab">
                                <i class="fas fa-bell"></i> Notifications
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" 
                                    id="security-tab" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#security" 
                                    type="button" 
                                    role="tab">
                                <i class="fas fa-shield-alt"></i> Security
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" 
                                    id="appearance-tab" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#appearance" 
                                    type="button" 
                                    role="tab">
                                <i class="fas fa-paint-brush"></i> Appearance
                            </button>
                        </li>
                    </ul>

                    <!-- Settings Content -->
                    <div class="tab-content" id="settingsTabsContent">
                        <!-- General Settings -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <form action="/settings/general.php" method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="language" class="form-label">Language</label>
                                    <select class="form-select" id="language" name="language" required>
                                        <option value="en" <?php echo $settings['language'] === 'en' ? 'selected' : ''; ?>>English</option>
                                        <option value="de" <?php echo $settings['language'] === 'de' ? 'selected' : ''; ?>>German</option>
                                        <option value="fr" <?php echo $settings['language'] === 'fr' ? 'selected' : ''; ?>>French</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="timezone" class="form-label">Timezone</label>
                                    <select class="form-select" id="timezone" name="timezone" required>
                                        <?php foreach ($timezones as $timezone): ?>
                                            <option value="<?php echo $timezone; ?>" 
                                                    <?php echo $settings['timezone'] === $timezone ? 'selected' : ''; ?>>
                                                <?php echo $timezone; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="date_format" class="form-label">Date Format</label>
                                    <select class="form-select" id="date_format" name="date_format" required>
                                        <option value="Y-m-d" <?php echo $settings['date_format'] === 'Y-m-d' ? 'selected' : ''; ?>>YYYY-MM-DD</option>
                                        <option value="d.m.Y" <?php echo $settings['date_format'] === 'd.m.Y' ? 'selected' : ''; ?>>DD.MM.YYYY</option>
                                        <option value="m/d/Y" <?php echo $settings['date_format'] === 'm/d/Y' ? 'selected' : ''; ?>>MM/DD/YYYY</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="time_format" class="form-label">Time Format</label>
                                    <select class="form-select" id="time_format" name="time_format" required>
                                        <option value="H:i" <?php echo $settings['time_format'] === 'H:i' ? 'selected' : ''; ?>>24-hour</option>
                                        <option value="h:i A" <?php echo $settings['time_format'] === 'h:i A' ? 'selected' : ''; ?>>12-hour</option>
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Notification Settings -->
                        <div class="tab-pane fade" id="notifications" role="tabpanel">
                            <form action="/settings/notifications.php" method="POST">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="email_notifications" 
                                               name="email_notifications" 
                                               <?php echo $settings['email_notifications'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="email_notifications">
                                            Email Notifications
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="task_notifications" 
                                               name="task_notifications" 
                                               <?php echo $settings['task_notifications'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="task_notifications">
                                            Task Notifications
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="message_notifications" 
                                               name="message_notifications" 
                                               <?php echo $settings['message_notifications'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="message_notifications">
                                            Message Notifications
                                        </label>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Security Settings -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <form action="/settings/security.php" method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="two_factor" 
                                               name="two_factor" 
                                               <?php echo $settings['two_factor'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="two_factor">
                                            Two-Factor Authentication
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="session_timeout" 
                                               name="session_timeout" 
                                               <?php echo $settings['session_timeout'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="session_timeout">
                                            Session Timeout
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="session_duration" class="form-label">Session Duration (minutes)</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="session_duration" 
                                           name="session_duration" 
                                           value="<?php echo $settings['session_duration']; ?>" 
                                           min="5" 
                                           max="1440">
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Appearance Settings -->
                        <div class="tab-pane fade" id="appearance" role="tabpanel">
                            <form action="/settings/appearance.php" method="POST">
                                <div class="mb-3">
                                    <label for="theme" class="form-label">Theme</label>
                                    <select class="form-select" id="theme" name="theme">
                                        <option value="light" <?php echo $settings['theme'] === 'light' ? 'selected' : ''; ?>>Light</option>
                                        <option value="dark" <?php echo $settings['theme'] === 'dark' ? 'selected' : ''; ?>>Dark</option>
                                        <option value="system" <?php echo $settings['theme'] === 'system' ? 'selected' : ''; ?>>System</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="font_size" class="form-label">Font Size</label>
                                    <select class="form-select" id="font_size" name="font_size">
                                        <option value="small" <?php echo $settings['font_size'] === 'small' ? 'selected' : ''; ?>>Small</option>
                                        <option value="medium" <?php echo $settings['font_size'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                        <option value="large" <?php echo $settings['font_size'] === 'large' ? 'selected' : ''; ?>>Large</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="color_scheme" class="form-label">Color Scheme</label>
                                    <select class="form-select" id="color_scheme" name="color_scheme">
                                        <option value="blue" <?php echo $settings['color_scheme'] === 'blue' ? 'selected' : ''; ?>>Blue</option>
                                        <option value="green" <?php echo $settings['color_scheme'] === 'green' ? 'selected' : ''; ?>>Green</option>
                                        <option value="purple" <?php echo $settings['color_scheme'] === 'purple' ? 'selected' : ''; ?>>Purple</option>
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

// Theme preview
document.getElementById('theme').addEventListener('change', function() {
    document.body.setAttribute('data-theme', this.value);
});

// Font size preview
document.getElementById('font_size').addEventListener('change', function() {
    document.body.setAttribute('data-font-size', this.value);
});

// Color scheme preview
document.getElementById('color_scheme').addEventListener('change', function() {
    document.body.setAttribute('data-color-scheme', this.value);
});
</script> 