<?php $title = 'Notifications'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="bi bi-bell me-2 text-primary"></i>Notifications
    </h2>
    <div>
        <a href="/admin/notifications/read-all" class="btn btn-outline-primary">
            <i class="bi bi-check-all me-1"></i>Mark All Read
        </a>
    </div>
</div>

<!-- Notifications List -->
<div class="dashboard-card">
    <div class="card-body">
        <?php if (empty($notifications)): ?>
            <div class="text-center py-5">
                <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0 fs-5">No notifications</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notifications as $notification): ?>
                    <div class="list-group-item border-0 px-0 py-3 <?= $notification['is_read'] ? '' : 'bg-light' ?>">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <?php if ($notification['type'] === 'info'): ?>
                                    <i class="bi bi-info-circle text-info fs-4"></i>
                                <?php elseif ($notification['type'] === 'success'): ?>
                                    <i class="bi bi-check-circle text-success fs-4"></i>
                                <?php elseif ($notification['type'] === 'warning'): ?>
                                    <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                                <?php else: ?>
                                    <i class="bi bi-bell text-primary fs-4"></i>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 fw-bold">
                                            <?= htmlspecialchars($notification['title']) ?>
                                            <?php if (!$notification['is_read']): ?>
                                                <span class="badge bg-primary ms-2">New</span>
                                            <?php endif; ?>
                                        </h6>
                                        <p class="mb-2 text-muted"><?= htmlspecialchars($notification['message']) ?></p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= date('M d, Y H:i', strtotime($notification['created_at'])) ?>
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <?php if (!$notification['is_read']): ?>
                                            <a href="/admin/notifications/read?id=<?= $notification['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-check me-1"></i>Mark Read
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-success">Read</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Statistics -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <div class="card-body">
                <i class="bi bi-bell text-primary fs-1"></i>
                <h4 class="mt-2 mb-1"><?= count($notifications) ?></h4>
                <p class="text-muted mb-0">Total Notifications</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <div class="card-body">
                <i class="bi bi-bell-fill text-warning fs-1"></i>
                <h4 class="mt-2 mb-1"><?= count(array_filter($notifications, fn($n) => !$n['is_read'])) ?></h4>
                <p class="text-muted mb-0">Unread</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <div class="card-body">
                <i class="bi bi-check-circle text-success fs-1"></i>
                <h4 class="mt-2 mb-1"><?= count(array_filter($notifications, fn($n) => $n['is_read'])) ?></h4>
                <p class="text-muted mb-0">Read</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <div class="card-body">
                <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                <h4 class="mt-2 mb-1"><?= count(array_filter($notifications, fn($n) => $n['type'] === 'warning')) ?></h4>
                <p class="text-muted mb-0">Alerts</p>
            </div>
        </div>
    </div>
</div>
