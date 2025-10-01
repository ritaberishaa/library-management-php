<?php $title = 'Edit User'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="bi bi-person-gear me-2 text-primary"></i>Edit User
    </h2>
    <a href="/admin/users" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Users
    </a>
</div>

<!-- Edit User Form -->
<div class="dashboard-card">
    <div class="card-body">
        <form method="POST" action="/admin/users/edit">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="full_name" class="form-label">Full Name *</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" 
                           value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="username" class="form-label">Username *</label>
                    <input type="text" class="form-control" id="username" name="username" 
                           value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="tel" class="form-control" id="phone" name="phone" 
                           value="<?= htmlspecialchars($user['phone']) ?>">
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="role" class="form-label">Role *</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                        <option value="operator" <?= $user['role'] === 'operator' ? 'selected' : '' ?>>Operator</option>
                        <option value="super_admin" <?= $user['role'] === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                               <?= $user['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">
                            Active User
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check me-1"></i>Update User
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
    
    // Form validation
    form.addEventListener('submit', function(e) {
        const fullName = document.getElementById('full_name').value.trim();
        const email = document.getElementById('email').value.trim();
        const username = document.getElementById('username').value.trim();
        
        if (!fullName || !email || !username) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return;
        }
        
        if (!email.includes('@')) {
            e.preventDefault();
            alert('Please enter a valid email address.');
            return;
        }
    });
});
</script>
