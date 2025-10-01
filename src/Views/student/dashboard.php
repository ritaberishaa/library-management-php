<?php $title = 'Student Dashboard'; ?>

<script>
function markAsRead(notificationId) {
    fetch('/student/notifications/mark-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: notificationId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<!-- Welcome Section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-4">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="bi bi-person-fill text-white" style="font-size: 1.75rem;"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="mb-2 fw-bold">Welcome back, <?= htmlspecialchars(App\Core\Auth::user()->full_name) ?>!</h2>
                        <p class="text-muted mb-0 fs-5">Here's your library activity overview</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notifications Section -->
<?php if (!empty($notifications)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-bell-fill me-2"></i>Notifications
                    <?php if (count(array_filter($notifications, fn($n) => !$n->is_read)) > 0): ?>
                        <span class="badge bg-danger ms-2"><?= count(array_filter($notifications, fn($n) => !$n->is_read)) ?></span>
                    <?php endif; ?>
                </h5>
                <a href="/student/notifications" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php foreach (array_slice($notifications, 0, 3) as $notification): ?>
                <div class="alert alert-<?= $notification->type === 'success' ? 'success' : ($notification->type === 'warning' ? 'warning' : 'info') ?> alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-<?= $notification->type === 'success' ? 'check-circle' : ($notification->type === 'warning' ? 'exclamation-triangle' : 'info-circle') ?>-fill me-2 mt-1"></i>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-1"><?= htmlspecialchars($notification->title) ?></h6>
                            <p class="mb-1"><?= htmlspecialchars($notification->message) ?></p>
                            <small class="text-muted"><?= date('M d, Y H:i', strtotime($notification->created_at)) ?></small>
                        </div>
                        <?php if (!$notification->is_read): ?>
                        <button type="button" class="btn-close" onclick="markAsRead(<?= $notification->id ?>)"></button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row mb-5">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2.5rem;"><?= count($active_borrowings) ?></h2>
                    <p class="mb-0 opacity-90 fs-6">Active Borrowings</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-book text-white" style="font-size: 1.75rem;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-warning text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2.5rem;"><?= count($overdue_books) ?></h2>
                    <p class="mb-0 opacity-90 fs-6">Overdue Books</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-exclamation-triangle text-white" style="font-size: 1.75rem;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-danger text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2.5rem;">€<?= number_format($total_penalties, 2) ?></h2>
                    <p class="mb-0 opacity-90 fs-6">Unpaid Penalties</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-currency-euro text-white" style="font-size: 1.75rem;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2.5rem;"><?= count($notifications) ?></h2>
                    <p class="mb-0 opacity-90 fs-6">Notifications</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-bell text-white" style="font-size: 1.75rem;"></i>
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
                    Active Borrowings
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($active_borrowings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3 mb-0 fs-5">No active borrowings</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($active_borrowings as $borrowing): ?>
                            <div class="list-group-item border-0 px-0 py-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 fw-semibold fs-6"><?= htmlspecialchars($borrowing['title']) ?></h6>
                                        <p class="mb-2 text-muted">by <?= htmlspecialchars($borrowing['author']) ?></p>
                                        <small class="text-muted">
                                            Due: <?= date('M d, Y', strtotime($borrowing['due_date'])) ?>
                                            <?php if (strtotime($borrowing['due_date']) < time()): ?>
                                                <span class="badge bg-danger ms-2">Overdue</span>
                                            <?php endif; ?>
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
                    <i class="bi bi-bell me-2 text-info"></i>
                    Recent Notifications
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($notifications)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-bell text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3 mb-0 fs-5">No notifications</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($notifications, 0, 5) as $notification): ?>
                            <div class="list-group-item border-0 px-0 py-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 fw-semibold fs-6"><?= htmlspecialchars($notification->title) ?></h6>
                                        <p class="mb-2 text-muted"><?= htmlspecialchars($notification->message) ?></p>
                                        <small class="text-muted"><?= date('M d, Y', strtotime($notification->created_at)) ?></small>
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
                <?php if (empty($overdue_books)): ?>
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
                                    <th class="border-0 py-3">Author</th>
                                    <th class="border-0 py-3">Due Date</th>
                                    <th class="border-0 py-3">Days Overdue</th>
                                    <th class="border-0 py-3">Penalty</th>
                                    <th class="border-0 py-3">Action</th>
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
