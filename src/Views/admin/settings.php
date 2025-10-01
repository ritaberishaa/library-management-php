<?php $title = 'System Settings'; ?>

<div class="d-flex justify-content-end align-items-center mb-4">
    <button class="btn btn-success" onclick="saveSettings()">
        <i class="bi bi-check-circle me-2"></i>Save Settings
    </button>
</div>

<!-- Settings Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-gear me-2 text-primary"></i>
                    General Settings
                </h5>
            </div>
            <div class="card-body">
                <form id="settingsForm">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="library_name" class="form-label">Library Name</label>
                            <input type="text" class="form-control" id="library_name" name="library_name" value="<?= htmlspecialchars($settings['library_name'] ?? 'Library Management System') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="library_email" class="form-label">Library Email</label>
                            <input type="email" class="form-control" id="library_email" name="library_email" value="<?= htmlspecialchars($settings['library_email'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="max_borrow_days" class="form-label">Maximum Borrow Days</label>
                            <input type="number" class="form-control" id="max_borrow_days" name="max_borrow_days" value="<?= $settings['max_borrow_days'] ?? 14 ?>" min="1" max="365">
                        </div>
                        <div class="col-md-6">
                            <label for="max_books_per_student" class="form-label">Max Books Per Student</label>
                            <input type="number" class="form-control" id="max_books_per_student" name="max_books_per_student" value="<?= $settings['max_books_per_student'] ?? 5 ?>" min="1" max="20">
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="penalty_per_day" class="form-label">Penalty Per Day (€)</label>
                            <input type="number" class="form-control" id="penalty_per_day" name="penalty_per_day" value="<?= $settings['penalty_per_day'] ?? 0.50 ?>" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label for="default_borrow_fee" class="form-label">Default Borrow Fee (€)</label>
                            <input type="number" class="form-control" id="default_borrow_fee" name="default_borrow_fee" value="<?= $settings['default_borrow_fee'] ?? 0.00 ?>" min="0" step="0.01">
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="library_phone" class="form-label">Library Phone</label>
                            <input type="tel" class="form-control" id="library_phone" name="library_phone" value="<?= htmlspecialchars($settings['library_phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="library_address" class="form-label">Library Address</label>
                            <input type="text" class="form-control" id="library_address" name="library_address" value="<?= htmlspecialchars($settings['library_address'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="library_description" class="form-label">Library Description</label>
                            <textarea class="form-control" id="library_description" name="library_description" rows="3"><?= htmlspecialchars($settings['library_description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-info-circle me-2 text-info"></i>
                    System Information
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>PHP Version:</strong>
                    <span class="text-muted"><?= PHP_VERSION ?></span>
                </div>
                <div class="mb-3">
                    <strong>Database:</strong>
                    <span class="text-muted">SQLite</span>
                </div>
                <div class="mb-3">
                    <strong>Last Updated:</strong>
                    <span class="text-muted"><?= date('M d, Y H:i') ?></span>
                </div>
                <div class="mb-3">
                    <strong>Total Users:</strong>
                    <span class="text-muted"><?= $total_users ?? 0 ?></span>
                </div>
                <div class="mb-3">
                    <strong>Total Books:</strong>
                    <span class="text-muted"><?= $total_books ?? 0 ?></span>
                </div>
            </div>
        </div>
        
        <div class="dashboard-card mt-4">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-shield-check me-2 text-success"></i>
                    Security Settings
                </h5>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="require_password_change" name="require_password_change" <?= ($settings['require_password_change'] ?? false) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="require_password_change">
                        Require Password Change
                    </label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="enable_audit_log" name="enable_audit_log" <?= ($settings['enable_audit_log'] ?? true) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="enable_audit_log">
                        Enable Audit Logging
                    </label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="enable_notifications" name="enable_notifications" <?= ($settings['enable_notifications'] ?? true) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="enable_notifications">
                        Enable Notifications
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function saveSettings() {
    const form = document.getElementById('settingsForm');
    const formData = new FormData(form);
    
    // Add checkbox values
    formData.append('require_password_change', document.getElementById('require_password_change').checked);
    formData.append('enable_audit_log', document.getElementById('enable_audit_log').checked);
    formData.append('enable_notifications', document.getElementById('enable_notifications').checked);
    
    // Show loading state
    const saveBtn = document.querySelector('button[onclick="saveSettings()"]');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';
    saveBtn.disabled = true;
    
    // Send data to server
    fetch('/admin/settings', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            saveBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Saved!';
            saveBtn.classList.remove('btn-success');
            saveBtn.classList.add('btn-primary');
            
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error('Failed to save settings');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        saveBtn.innerHTML = '<i class="bi bi-x-circle me-2"></i>Error!';
        saveBtn.classList.remove('btn-success');
        saveBtn.classList.add('btn-danger');
        
        setTimeout(() => {
            saveBtn.innerHTML = originalText;
            saveBtn.classList.remove('btn-danger');
            saveBtn.classList.add('btn-success');
            saveBtn.disabled = false;
        }, 2000);
    });
}
</script>
