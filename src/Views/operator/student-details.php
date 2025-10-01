<?php $title = 'Student Details'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><?= htmlspecialchars($student->full_name) ?> - Student Details</h4>
        <p class="text-muted mb-0">Student ID: <?= htmlspecialchars($student->username) ?></p>
    </div>
    <div class="btn-group">
        <a href="/operator/students/edit?id=<?= $student->id ?>" class="btn btn-outline-warning">
            <i class="bi bi-pencil me-2"></i>Edit Student
        </a>
        <a href="/operator/students/borrowings?id=<?= $student->id ?>" class="btn btn-outline-info">
            <i class="bi bi-list-ul me-2"></i>View Borrowings
        </a>
        <a href="/operator/students" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Students
        </a>
    </div>
</div>

<!-- Student Information -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-person me-2 text-primary"></i>
                    Student Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Full Name:</strong>
                            <p class="text-muted mb-0"><?= htmlspecialchars($student->full_name) ?></p>
                        </div>
                        <div class="mb-3">
                            <strong>Email:</strong>
                            <p class="text-muted mb-0"><?= htmlspecialchars($student->email) ?></p>
                        </div>
                        <div class="mb-3">
                            <strong>Student ID:</strong>
                            <p class="text-muted mb-0"><?= htmlspecialchars($student->username) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Status:</strong>
                            <p class="mb-0">
                                <span class="badge <?= $student->is_active ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $student->is_active ? 'Active' : 'Inactive' ?>
                                </span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <strong>Account Created:</strong>
                            <p class="text-muted mb-0"><?= date('M d, Y', strtotime($student->created_at)) ?></p>
                        </div>
                        <div class="mb-3">
                            <strong>Total Penalties:</strong>
                            <p class="text-muted mb-0">€<?= number_format($total_penalties, 2) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-graph-up me-2 text-info"></i>
                    Borrowing Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="p-3">
                            <h3 class="text-primary mb-1"><?= count($active_borrowings) ?></h3>
                            <p class="text-muted mb-0">Active</p>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="p-3">
                            <h3 class="text-warning mb-1"><?= count($overdue_books) ?></h3>
                            <p class="text-muted mb-0">Overdue</p>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="p-3">
                            <h3 class="text-info mb-1"><?= count($borrowings) ?></h3>
                            <p class="text-muted mb-0">Total</p>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="p-3">
                            <h3 class="text-success mb-1"><?= count(array_filter($borrowings, fn($b) => $b['status'] === 'returned')) ?></h3>
                            <p class="text-muted mb-0">Returned</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Active Borrowings -->
<?php if (!empty($active_borrowings)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-book me-2 text-primary"></i>
                    Active Borrowings
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
                                <th class="border-0 py-3">Status</th>
                                <th class="border-0 py-3">Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($active_borrowings as $borrowing): ?>
                                <tr>
                                    <td class="fw-semibold py-3"><?= htmlspecialchars($borrowing['title']) ?></td>
                                    <td class="text-muted py-3"><?= htmlspecialchars($borrowing['author']) ?></td>
                                    <td class="py-3"><?= date('M d, Y', strtotime($borrowing['borrow_date'])) ?></td>
                                    <td class="py-3"><?= date('M d, Y', strtotime($borrowing['due_date'])) ?></td>
                                    <td class="py-3">
                                        <?php if (strtotime($borrowing['due_date']) < time()): ?>
                                            <span class="badge bg-danger">Overdue</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Active</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3">€<?= number_format($borrowing['borrow_fee'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Overdue Books -->
<?php if (!empty($overdue_books)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle me-2 text-warning"></i>
                    Overdue Books
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 py-3">Book</th>
                                <th class="border-0 py-3">Author</th>
                                <th class="border-0 py-3">Due Date</th>
                                <th class="border-0 py-3">Days Overdue</th>
                                <th class="border-0 py-3">Penalty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($overdue_books as $book): ?>
                                <tr>
                                    <td class="fw-semibold py-3"><?= htmlspecialchars($book['title']) ?></td>
                                    <td class="text-muted py-3"><?= htmlspecialchars($book['author']) ?></td>
                                    <td class="py-3"><?= date('M d, Y', strtotime($book['due_date'])) ?></td>
                                    <td class="py-3">
                                        <span class="badge bg-danger"><?= $book['days_overdue'] ?> days</span>
                                    </td>
                                    <td class="fw-semibold text-danger py-3">€<?= number_format($book['penalty_amount'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Recent Borrowing History -->
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-list-ul me-2 text-info"></i>
                    Recent Borrowing History
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($borrowings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2 mb-0">No borrowing history found</p>
                    </div>
                <?php else: ?>
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
                                <?php foreach (array_slice($borrowings, 0, 10) as $borrowing): ?>
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
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
