<?php $title = 'Penalties'; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-exclamation-triangle"></i> Unpaid Penalties</h5>
            </div>
            <div class="card-body">
                <?php if (empty($penalty_borrowings)): ?>
                    <p class="text-muted">No unpaid penalties.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($penalty_borrowings as $borrowing): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= htmlspecialchars($borrowing['title']) ?></h6>
                                    <span class="badge bg-danger">€<?= number_format($borrowing['penalty_amount'], 2) ?></span>
                                </div>
                                <p class="mb-1">by <?= htmlspecialchars($borrowing['author']) ?></p>
                                <small>
                                    Due: <?= date('M d, Y', strtotime($borrowing['due_date'])) ?>
                                    | Returned: <?= $borrowing['return_date'] ? date('M d, Y', strtotime($borrowing['return_date'])) : 'Not returned' ?>
                                </small>
                                <div class="mt-2">
                                    <a href="/books/pay-penalty?id=<?= $borrowing['id'] ?>" class="btn btn-sm btn-success">
                                        <i class="bi bi-credit-card"></i> Pay Penalty
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-currency-euro"></i> Penalty Summary</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12">
                        <h2 class="text-danger">€<?= number_format($total_penalties, 2) ?></h2>
                        <p class="text-muted">Total Unpaid Penalties</p>
                    </div>
                </div>
                
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle"></i>
                    <strong>Important:</strong> You must pay all penalties before borrowing new books.
                </div>
                
                <?php if ($total_penalties > 0): ?>
                    <div class="d-grid">
                        <a href="/student/penalties" class="btn btn-danger">
                            <i class="bi bi-credit-card"></i> Pay All Penalties
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
