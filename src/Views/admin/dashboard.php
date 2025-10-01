<?php $title = 'Admin Dashboard'; ?>

<!-- Welcome Section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-4">
                        <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="bi bi-shield-check text-white" style="font-size: 1.75rem;"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="mb-2 fw-bold">Welcome back, <?= htmlspecialchars(App\Core\Auth::user()->full_name) ?>!</h2>
                        <p class="text-muted mb-0 fs-5">Full system administration and management</p>
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
                    <h2 class="mb-2 fw-bold" style="font-size: 2.5rem;"><?= $total_users ?? 0 ?></h2>
                    <p class="mb-0 opacity-90 fs-6">Total Users</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-people text-white" style="font-size: 1.75rem;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-info text-white">
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
        <div class="stats-card bg-warning text-white">
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
        <div class="stats-card bg-danger text-white">
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
</div>

<!-- Content Sections -->
<div class="row mb-5">
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-people me-2 text-primary"></i>
                    Recent User Activity
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_users)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3 mb-0 fs-5">No recent user activity</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_users as $user): ?>
                            <div class="list-group-item border-0 px-0 py-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 fw-semibold fs-6"><?= htmlspecialchars($user['full_name']) ?></h6>
                                        <p class="mb-2 text-muted"><?= htmlspecialchars($user['email']) ?></p>
                                        <small class="text-muted">
                                            Role: <?= ucfirst($user['role']) ?> | 
                                            Joined: <?= date('M d, Y', strtotime($user['created_at'])) ?>
                                        </small>
                                    </div>
                                    <span class="badge <?= $user['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
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
                    <i class="bi bi-journal-text me-2 text-info"></i>
                    System Activity
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_activity)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-journal-text text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3 mb-0 fs-5">No recent activity</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_activity as $activity): ?>
                            <div class="list-group-item border-0 px-0 py-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 fw-semibold fs-6"><?= htmlspecialchars($activity['action']) ?></h6>
                                        <p class="mb-2 text-muted"><?= htmlspecialchars($activity['description']) ?></p>
                                        <small class="text-muted">
                                            <?= date('M d, Y H:i', strtotime($activity['created_at'])) ?>
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

<!-- System Overview -->
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-graph-up me-2 text-success"></i>
                    System Overview
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-4">
                        <div class="p-3">
                            <i class="bi bi-people-fill text-primary" style="font-size: 2.5rem;"></i>
                            <h4 class="mt-2 mb-1"><?= $students_count ?? 0 ?></h4>
                            <p class="text-muted mb-0">Students</p>
                        </div>
                    </div>
                    <div class="col-md-3 text-center mb-4">
                        <div class="p-3">
                            <i class="bi bi-gear-fill text-warning" style="font-size: 2.5rem;"></i>
                            <h4 class="mt-2 mb-1"><?= $operators_count ?? 0 ?></h4>
                            <p class="text-muted mb-0">Operators</p>
                        </div>
                    </div>
                    <div class="col-md-3 text-center mb-4">
                        <div class="p-3">
                            <i class="bi bi-book-fill text-info" style="font-size: 2.5rem;"></i>
                            <h4 class="mt-2 mb-1"><?= $available_books ?? 0 ?></h4>
                            <p class="text-muted mb-0">Available Books</p>
                        </div>
                    </div>
                    <div class="col-md-3 text-center mb-4">
                        <div class="p-3">
                            <i class="bi bi-currency-euro text-success" style="font-size: 2.5rem;"></i>
                            <h4 class="mt-2 mb-1">€<?= number_format($total_revenue ?? 0, 2) ?></h4>
                            <p class="text-muted mb-0">Total Revenue</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
