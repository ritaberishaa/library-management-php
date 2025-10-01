<?php $title = 'Student Borrowings'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><?= htmlspecialchars($student->full_name) ?> - Borrowing History</h4>
        <p class="text-muted mb-0">Student ID: <?= htmlspecialchars($student->username) ?></p>
    </div>
    <a href="/operator/students" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Students
    </a>
</div>

<!-- Student Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2rem;"><?= count($active_borrowings) ?></h2>
                    <p class="mb-0 opacity-90 fs-6">Active Borrowings</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-book text-white" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card bg-warning text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2rem;"><?= count($overdue_books) ?></h2>
                    <p class="mb-0 opacity-90 fs-6">Overdue Books</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-exclamation-triangle text-white" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2rem;"><?= count($borrowings) ?></h2>
                    <p class="mb-0 opacity-90 fs-6">Total Borrowings</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-list-ul text-white" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2rem;"><?= count(array_filter($borrowings, fn($b) => $b['status'] === 'returned')) ?></h2>
                    <p class="mb-0 opacity-90 fs-6">Returned</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-check-circle text-white" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Borrowings Table -->
<div class="dashboard-card">
    <div class="card-header">
        <h5 class="d-flex align-items-center">
            <i class="bi bi-list-ul me-2 text-primary"></i>
            Borrowing History
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 py-3">Book</th>
                        <th class="border-0 py-3">Author</th>
                        <th class="border-0 py-3">Borrow Date</th>
                        <th class="border-0 py-3">Due Date</th>
                        <th class="border-0 py-3">Return Date</th>
                        <th class="border-0 py-3">Status</th>
                        <th class="border-0 py-3">Fee</th>
                        <th class="border-0 py-3">Penalty</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($borrowings)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">No borrowing history found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($borrowings as $borrowing): ?>
                            <tr>
                                <td class="fw-semibold py-3"><?= htmlspecialchars($borrowing['title']) ?></td>
                                <td class="text-muted py-3"><?= htmlspecialchars($borrowing['author']) ?></td>
                                <td class="py-3"><?= date('M d, Y', strtotime($borrowing['borrow_date'])) ?></td>
                                <td class="py-3"><?= date('M d, Y', strtotime($borrowing['due_date'])) ?></td>
                                <td class="py-3">
                                    <?php if ($borrowing['return_date']): ?>
                                        <?= date('M d, Y', strtotime($borrowing['return_date'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not returned</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <?php if ($borrowing['status'] === 'active'): ?>
                                        <?php if (strtotime($borrowing['due_date']) < time()): ?>
                                            <span class="badge bg-danger">Overdue</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Active</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-success">Returned</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">€<?= number_format($borrowing['borrow_fee'], 2) ?></td>
                                <td class="py-3">
                                    <?php if ($borrowing['penalty_amount'] > 0): ?>
                                        <span class="text-danger fw-semibold">€<?= number_format($borrowing['penalty_amount'], 2) ?></span>
                                        <?php if (!$borrowing['penalty_paid']): ?>
                                            <span class="badge bg-danger ms-1">Unpaid</span>
                                        <?php else: ?>
                                            <span class="badge bg-success ms-1">Paid</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">None</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
