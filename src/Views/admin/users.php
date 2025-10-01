<?php $title = 'Manage Users'; ?>

<div class="d-flex justify-content-end align-items-center mb-4">
    <a href="/admin/users/create" class="btn btn-primary">
        <i class="bi bi-person-plus me-2"></i>Add New User
    </a>
</div>

<!-- Search and Filter -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search users..." id="searchInput">
        </div>
    </div>
    <div class="col-md-3">
        <select class="form-select" id="roleFilter">
            <option value="">All Roles</option>
            <option value="student">Student</option>
            <option value="operator">Operator</option>
            <option value="super_admin">Super Admin</option>
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-select" id="statusFilter">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
            <i class="bi bi-x-circle me-1"></i>Clear
        </button>
    </div>
</div>

<!-- Users Table -->
<div class="dashboard-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 py-3">Name</th>
                        <th class="border-0 py-3">Email</th>
                        <th class="border-0 py-3">Role</th>
                        <th class="border-0 py-3">Student ID</th>
                        <th class="border-0 py-3">Last Login</th>
                        <th class="border-0 py-3">Status</th>
                        <th class="border-0 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">No users found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="fw-semibold py-3"><?= htmlspecialchars($user['full_name']) ?></td>
                                <td class="text-muted py-3"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="py-3">
                                    <?php
                                    $roleColors = [
                                        'super_admin' => 'bg-danger',
                                        'operator' => 'bg-warning',
                                        'student' => 'bg-info'
                                    ];
                                    $roleColor = $roleColors[$user['role']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $roleColor ?>"><?= ucfirst(str_replace('_', ' ', $user['role'])) ?></span>
                                </td>
                                <td class="py-3"><?= htmlspecialchars($user['student_id'] ?? 'N/A') ?></td>
                                <td class="py-3">
                                    <?php if ($user['last_login']): ?>
                                        <?= date('M d, Y H:i', strtotime($user['last_login'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Never</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <span class="badge <?= $user['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="btn-group" role="group">
                                        <a href="/admin/users/view?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/admin/users/edit?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($user['id'] != App\Core\Auth::user()->id): ?>
                                            <a href="/admin/users/delete?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    const table = document.getElementById('usersTable');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const roleValue = roleFilter.value;
        const statusValue = statusFilter.value;
        
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const name = row.cells[0].textContent.toLowerCase();
            const email = row.cells[1].textContent.toLowerCase();
            const role = row.cells[2].textContent.toLowerCase();
            const status = row.cells[5].textContent.toLowerCase();
            
            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesRole = !roleValue || role.includes(roleValue);
            const matchesStatus = !statusValue || status.includes(statusValue);
            
            if (matchesSearch && matchesRole && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    function clearFilters() {
        searchInput.value = '';
        roleFilter.value = '';
        statusFilter.value = '';
        filterTable();
    }
    
    searchInput.addEventListener('input', filterTable);
    roleFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
    
    // Make clearFilters available globally
    window.clearFilters = clearFilters;
});
</script>
