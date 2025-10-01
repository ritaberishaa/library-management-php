<?php $title = 'Students Management'; ?>


<!-- Search and Filter -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search students..." id="searchInput">
        </div>
    </div>
    <div class="col-md-2">
        <select class="form-select" id="statusFilter">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" id="penaltyFilter">
            <option value="">All Students</option>
            <option value="with_penalties">With Penalties</option>
            <option value="no_penalties">No Penalties</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
            <i class="bi bi-x-circle me-1"></i>Clear
        </button>
    </div>
</div>

<!-- Students Table -->
<div class="dashboard-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="studentsTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 py-3">Name</th>
                        <th class="border-0 py-3">Email</th>
                        <th class="border-0 py-3">Student ID</th>
                        <th class="border-0 py-3">Active Borrowings</th>
                        <th class="border-0 py-3">Overdue Books</th>
                        <th class="border-0 py-3">Total Penalties</th>
                        <th class="border-0 py-3">Status</th>
                        <th class="border-0 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">No students found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td class="fw-semibold py-3"><?= htmlspecialchars($student['full_name']) ?></td>
                                <td class="text-muted py-3"><?= htmlspecialchars($student['email']) ?></td>
                                <td class="py-3"><?= htmlspecialchars($student['student_id']) ?></td>
                                <td class="py-3">
                                    <span class="badge bg-info"><?= $student['active_borrowings'] ?></span>
                                </td>
                                <td class="py-3">
                                    <?php if ($student['overdue_books'] > 0): ?>
                                        <span class="badge bg-danger"><?= $student['overdue_books'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <?php if ($student['total_penalties'] > 0): ?>
                                        <span class="text-danger fw-semibold">€<?= number_format($student['total_penalties'], 2) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">€0.00</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <span class="badge <?= $student['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $student['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="btn-group" role="group">
                                        <a href="/operator/students/view?id=<?= $student['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/operator/students/edit?id=<?= $student['id'] ?>" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="/operator/students/borrowings?id=<?= $student['id'] ?>" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-list-ul"></i>
                                        </a>
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
    const statusFilter = document.getElementById('statusFilter');
    const penaltyFilter = document.getElementById('penaltyFilter');
    const table = document.getElementById('studentsTable');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const penaltyValue = penaltyFilter.value;
        
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const name = row.cells[0].textContent.toLowerCase();
            const email = row.cells[1].textContent.toLowerCase();
            const status = row.cells[6].textContent.toLowerCase();
            const penalties = row.cells[5].textContent;
            
            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesStatus = !statusValue || status.includes(statusValue);
            const matchesPenalty = !penaltyValue || 
                (penaltyValue === 'with_penalties' && penalties.includes('€') && !penalties.includes('€0.00')) ||
                (penaltyValue === 'no_penalties' && penalties.includes('€0.00'));
            
            if (matchesSearch && matchesStatus && matchesPenalty) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    function clearFilters() {
        searchInput.value = '';
        statusFilter.value = '';
        penaltyFilter.value = '';
        filterTable();
    }
    
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    penaltyFilter.addEventListener('change', filterTable);
    
    // Make clearFilters available globally
    window.clearFilters = clearFilters;
});
</script>
