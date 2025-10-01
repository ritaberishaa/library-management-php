<?php $title = 'Create User'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="bi bi-person-plus me-2 text-primary"></i>Create New User
    </h2>
    <a href="/admin/users" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Users
    </a>
</div>

<!-- Create User Form -->
<div class="dashboard-card">
    <div class="card-body">
        <form method="POST" action="/admin/users/create">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="full_name" class="form-label">Full Name *</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="username" class="form-label">Username *</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="tel" class="form-control" id="phone" name="phone">
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="role" class="form-label">Role *</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="student">Student</option>
                        <option value="operator">Operator</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">Password *</label>
                    <input type="password" class="form-control" id="password" name="password" required minlength="6">
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check me-1"></i>Create User
                </button>
                <a href="/admin/users" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const roleSelect = document.getElementById('role');
    
    // Auto-generate username from email
    const emailInput = document.getElementById('email');
    const usernameInput = document.getElementById('username');
    
    emailInput.addEventListener('blur', function() {
        if (emailInput.value && !usernameInput.value) {
            const email = emailInput.value;
            const username = email.split('@')[0];
            usernameInput.value = username;
        }
    });
    
    // Form validation
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password');
        
        if (password.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long.');
            return;
        }
        
        if (confirmPassword && password !== confirmPassword.value) {
            e.preventDefault();
            alert('Passwords do not match.');
            return;
        }
    });
});
</script>
