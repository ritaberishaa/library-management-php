<?php $title = 'Borrowings Management'; ?>


<!-- Filter Options -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search by student or book..." id="searchInput">
        </div>
    </div>
    <div class="col-md-3">
        <select class="form-select" id="statusFilter">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="returned">Returned</option>
            <option value="overdue">Overdue</option>
        </select>
    </div>
    <div class="col-md-3">
        <input type="date" class="form-control" id="dateFilter" placeholder="Filter by date">
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
            <i class="bi bi-x-circle me-1"></i>Clear
        </button>
    </div>
</div>

<!-- Borrowings Table -->
<div class="dashboard-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="borrowingsTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 py-3">Student</th>
                        <th class="border-0 py-3">Book</th>
                        <th class="border-0 py-3">Borrowed Date</th>
                        <th class="border-0 py-3">Due Date</th>
                        <th class="border-0 py-3">Status</th>
                        <th class="border-0 py-3">Penalty</th>
                        <th class="border-0 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($borrowings)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-list-ul text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">No borrowings found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($borrowings as $borrowing): ?>
                            <tr>
                                <td class="fw-semibold py-3"><?= htmlspecialchars($borrowing['student_name']) ?></td>
                                <td class="text-muted py-3"><?= htmlspecialchars($borrowing['book_title']) ?></td>
                                <td class="py-3"><?= date('M d, Y', strtotime($borrowing['borrowed_date'])) ?></td>
                                <td class="py-3"><?= date('M d, Y', strtotime($borrowing['due_date'])) ?></td>
                                <td class="py-3">
                                    <?php 
                                    $status = $borrowing['status'];
                                    $isOverdue = strtotime($borrowing['due_date']) < time() && $status === 'active';
                                    ?>
                                    <?php if ($isOverdue): ?>
                                        <span class="badge bg-danger">Overdue</span>
                                    <?php elseif ($status === 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Returned</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <?php if ($borrowing['penalty_amount'] > 0): ?>
                                        <span class="text-danger fw-semibold">€<?= number_format($borrowing['penalty_amount'], 2) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">€0.00</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <a href="/operator/borrowings/view?id=<?= $borrowing['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </a>
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
    const dateFilter = document.getElementById('dateFilter');
    const table = document.getElementById('borrowingsTable');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const dateValue = dateFilter.value;
        
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const student = row.cells[0].textContent.toLowerCase();
            const book = row.cells[1].textContent.toLowerCase();
            const status = row.cells[4].textContent.toLowerCase();
            const borrowedDate = row.cells[2].textContent;
            
            const matchesSearch = student.includes(searchTerm) || book.includes(searchTerm);
            const matchesStatus = !statusValue || status.includes(statusValue);
            const matchesDate = !dateValue || borrowedDate.includes(dateValue);
            
            if (matchesSearch && matchesStatus && matchesDate) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    function clearFilters() {
        searchInput.value = '';
        statusFilter.value = '';
        dateFilter.value = '';
        filterTable();
    }
    
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    dateFilter.addEventListener('change', filterTable);
    
    // Make clearFilters available globally
    window.clearFilters = clearFilters;
});
</script>
