<?php $title = 'Audit Logs'; ?>

<div class="d-flex justify-content-end align-items-center mb-4">
    <div class="btn-group">
        <button class="btn btn-outline-secondary" onclick="exportLogs()">
            <i class="bi bi-download me-2"></i>Export
        </button>
        <button class="btn btn-outline-danger" onclick="clearLogs()">
            <i class="bi bi-trash me-2"></i>Clear Logs
        </button>
    </div>
</div>

<!-- Filter Options -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search logs..." id="searchInput">
        </div>
    </div>
    <div class="col-md-2">
        <select class="form-select" id="actionFilter">
            <option value="">All Actions</option>
            <option value="login">Login</option>
            <option value="logout">Logout</option>
            <option value="create">Create</option>
            <option value="update">Update</option>
            <option value="delete">Delete</option>
            <option value="borrow">Borrow</option>
            <option value="return">Return</option>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" id="userFilter">
            <option value="">All Users</option>
            <?php foreach ($all_users as $user): ?>
                <option value="<?= htmlspecialchars($user->full_name) ?>"><?= htmlspecialchars($user->full_name) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <input type="date" class="form-control" id="dateFrom" placeholder="From Date">
    </div>
    <div class="col-md-2">
        <input type="date" class="form-control" id="dateTo" placeholder="To Date">
    </div>
    <div class="col-md-1">
        <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
            <i class="bi bi-x-circle"></i>
        </button>
    </div>
</div>

<!-- Audit Logs Table -->
<div class="dashboard-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="auditTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 py-3">Timestamp</th>
                        <th class="border-0 py-3">User</th>
                        <th class="border-0 py-3">Action</th>
                        <th class="border-0 py-3">Description</th>
                        <th class="border-0 py-3">IP Address</th>
                        <th class="border-0 py-3">Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($audit_logs)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-journal-text text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">No audit logs found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($audit_logs as $log): ?>
                            <tr>
                                <td class="py-3">
                                    <small class="text-muted"><?= date('M d, Y H:i:s', strtotime($log['created_at'])) ?></small>
                                </td>
                                <td class="fw-semibold py-3"><?= htmlspecialchars($log['user_name']) ?></td>
                                <td class="py-3">
                                    <?php
                                    $actionColors = [
                                        'login' => 'bg-success',
                                        'logout' => 'bg-secondary',
                                        'create' => 'bg-primary',
                                        'update' => 'bg-warning',
                                        'delete' => 'bg-danger',
                                        'borrow' => 'bg-info',
                                        'return' => 'bg-success'
                                    ];
                                    $actionColor = $actionColors[$log['action']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $actionColor ?>"><?= ucfirst($log['action']) ?></span>
                                </td>
                                <td class="text-muted py-3"><?= htmlspecialchars($log['description']) ?></td>
                                <td class="py-3">
                                    <code class="text-muted"><?= htmlspecialchars($log['ip_address']) ?></code>
                                </td>
                                <td class="py-3">
                                    <button class="btn btn-sm btn-outline-primary" onclick="showDetails('<?= htmlspecialchars($log['details'], ENT_QUOTES) ?>')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
    <nav aria-label="Audit logs pagination" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($pagination['current_page'] > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>">Previous</a>
                </li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Audit Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="detailsContent" class="bg-light p-3 rounded"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const actionFilter = document.getElementById('actionFilter');
    const userFilter = document.getElementById('userFilter');
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');
    const table = document.getElementById('auditTable');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const actionValue = actionFilter.value;
        const userValue = userFilter.value;
        const fromDate = dateFrom.value;
        const toDate = dateTo.value;
        
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const user = row.cells[1].textContent.toLowerCase();
            const action = row.cells[2].textContent.toLowerCase();
            const description = row.cells[3].textContent.toLowerCase();
            const timestamp = row.cells[0].textContent;
            
            const matchesSearch = user.includes(searchTerm) || description.includes(searchTerm);
            const matchesAction = !actionValue || action.includes(actionValue);
            const matchesUser = !userValue || user.includes(userValue);
            const matchesDate = !fromDate || !toDate || (timestamp >= fromDate && timestamp <= toDate);
            
            if (matchesSearch && matchesAction && matchesUser && matchesDate) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    function clearFilters() {
        searchInput.value = '';
        actionFilter.value = '';
        userFilter.value = '';
        dateFrom.value = '';
        dateTo.value = '';
        filterTable();
    }
    
    function showDetails(details) {
        document.getElementById('detailsContent').textContent = details;
        new bootstrap.Modal(document.getElementById('detailsModal')).show();
    }
    
    function exportLogs() {
        window.location.href = '/admin/export-audit-logs';
    }
    
    function clearLogs() {
        if (confirm('Are you sure you want to clear all audit logs? This action cannot be undone.')) {
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/clear-audit-logs';
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    searchInput.addEventListener('input', filterTable);
    actionFilter.addEventListener('change', filterTable);
    userFilter.addEventListener('change', filterTable);
    dateFrom.addEventListener('change', filterTable);
    dateTo.addEventListener('change', filterTable);
    
    // Make functions available globally
    window.clearFilters = clearFilters;
    window.showDetails = showDetails;
    window.exportLogs = exportLogs;
    window.clearLogs = clearLogs;
});
</script>
