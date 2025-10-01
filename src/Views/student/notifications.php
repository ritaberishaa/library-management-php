<?php $title = 'Notifications'; ?>

<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-bell-fill me-2"></i>Notifications
                </h4>
                <div>
                    <span class="badge bg-primary me-2"><?= count($notifications) ?> Total</span>
                    <span class="badge bg-danger"><?= count(array_filter($notifications, fn($n) => !$n->is_read)) ?> Unread</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($notifications)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">No notifications yet</h5>
                    <p class="text-muted">You'll receive notifications about library updates and important announcements.</p>
                </div>
                <?php else: ?>
                <div class="list-group">
                    <?php foreach ($notifications as $notification): ?>
                    <div class="list-group-item list-group-item-action" 
                         onclick="markAsRead(<?= $notification->id ?>)">
                        <div class="d-flex w-100 justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-<?= $notification->type === 'success' ? 'check-circle' : ($notification->type === 'warning' ? 'exclamation-triangle' : 'info-circle') ?>-fill me-2 text-<?= $notification->type === 'success' ? 'success' : ($notification->type === 'warning' ? 'warning' : 'info') ?>"></i>
                                    <h6 class="mb-1 fw-bold"><?= htmlspecialchars($notification->title) ?></h6>
                                    <?php if (!$notification->is_read): ?>
                                    <span class="badge bg-primary ms-2">New</span>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-1"><?= htmlspecialchars($notification->message) ?></p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <?= date('M d, Y H:i', strtotime($notification->created_at)) ?>
                                </small>
                            </div>
                            <div class="ms-3">
                                <?php if (!$notification->is_read): ?>
                                <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); markAsRead(<?= $notification->id ?>)">
                                    <i class="bi bi-check"></i> Mark as Read
                                </button>
                                <?php else: ?>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Read
                                </span>
                                <?php endif; ?>
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