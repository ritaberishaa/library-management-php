<?php $title = 'Operator Dashboard'; ?>

<!-- Welcome Section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-4">
                        <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="bi bi-gear-fill text-white" style="font-size: 1.75rem;"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="mb-2 fw-bold">Welcome back, <?= htmlspecialchars(App\Core\Auth::user()->full_name) ?>!</h2>
                        <p class="text-muted mb-0 fs-5">Manage books, borrowings, and students</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-5">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2.5rem;"><?= $total_books ?? 0 ?></h2>
                    <p class="mb-0 opacity-90 fs-6">Total Books</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-book text-white" style="font-size: 1.75rem;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2.5rem;"><?= $active_borrowings ?? 0 ?></h2>
                    <p class="mb-0 opacity-90 fs-6">Active Borrowings</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-list-ul text-white" style="font-size: 1.75rem;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-warning text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2.5rem;"><?= $overdue_books ?? 0 ?></h2>
                    <p class="mb-0 opacity-90 fs-6">Overdue Books</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-exclamation-triangle text-white" style="font-size: 1.75rem;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2.5rem;"><?= $total_students ?? 0 ?></h2>
                    <p class="mb-0 opacity-90 fs-6">Total Students</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-people text-white" style="font-size: 1.75rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Sections -->
<div class="row mb-5">
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-book me-2 text-primary"></i>
                    Recent Book Additions
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_books)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3 mb-0 fs-5">No recent book additions</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_books as $book): ?>
                            <div class="list-group-item border-0 px-0 py-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 fw-semibold fs-6"><?= htmlspecialchars($book['title']) ?></h6>
                                        <p class="mb-2 text-muted">by <?= htmlspecialchars($book['author']) ?></p>
                                        <small class="text-muted">
                                            Added: <?= date('M d, Y', strtotime($book['created_at'])) ?>
                                            | Copies: <?= $book['copies'] ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-list-ul me-2 text-info"></i>
                    Recent Borrowings
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_borrowings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-list-ul text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3 mb-0 fs-5">No recent borrowings</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_borrowings as $borrowing): ?>
                            <div class="list-group-item border-0 px-0 py-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 fw-semibold fs-6"><?= htmlspecialchars($borrowing['book_title']) ?></h6>
                                        <p class="mb-2 text-muted">by <?= htmlspecialchars($borrowing['student_name']) ?></p>
                                        <small class="text-muted">
                                            Borrowed: <?= date('M d, Y', strtotime($borrowing['borrowed_date'])) ?>
                                            | Due: <?= date('M d, Y', strtotime($borrowing['due_date'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Overdue Books Section -->
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle me-2 text-warning"></i>
                    Overdue Books
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($overdue_books_array)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3 mb-0 fs-5">No overdue books</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 py-3">Book</th>
                                    <th class="border-0 py-3">Student</th>
                                    <th class="border-0 py-3">Due Date</th>
                                    <th class="border-0 py-3">Days Overdue</th>
                                    <th class="border-0 py-3">Penalty</th>
                                    <th class="border-0 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($overdue_books_array as $book): ?>
                                    <tr>
                                        <td class="fw-semibold py-3"><?= htmlspecialchars($book['title']) ?></td>
                                        <td class="text-muted py-3"><?= htmlspecialchars($book['student_name']) ?></td>
                                        <td class="py-3"><?= date('M d, Y', strtotime($book['due_date'])) ?></td>
                                        <td class="py-3">
                                            <span class="badge bg-danger"><?= $book['days_overdue'] ?> days</span>
                                        </td>
                                        <td class="fw-semibold text-danger py-3">€<?= number_format($book['penalty_amount'], 2) ?></td>
                                        <td class="py-3">
                                            <a href="/books/return?id=<?= $book['id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-arrow-return-left me-1"></i>Return
                                            </a>
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
