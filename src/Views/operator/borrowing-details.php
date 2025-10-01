<?php $title = 'Borrowing Details'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Borrowing Details</h2>
    <a href="/operator/borrowings" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Borrowings
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-book me-2 text-primary"></i>
                    Borrowing Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Student Information</h6>
                        <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($student->full_name) ?></p>
                        <p class="mb-1"><strong>Username:</strong> <?= htmlspecialchars($student->username) ?></p>
                        <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($student->email) ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Book Information</h6>
                        <p class="mb-1"><strong>Title:</strong> <?= htmlspecialchars($book->title) ?></p>
                        <p class="mb-1"><strong>Author:</strong> <?= htmlspecialchars($book->author) ?></p>
                        <p class="mb-1"><strong>ISBN:</strong> <?= htmlspecialchars($book->isbn) ?></p>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Borrowing Details</h6>
                        <p class="mb-1"><strong>Borrowed Date:</strong> <?= date('M d, Y', strtotime($borrowing->borrow_date)) ?></p>
                        <p class="mb-1"><strong>Due Date:</strong> <?= date('M d, Y', strtotime($borrowing->due_date)) ?></p>
                        <p class="mb-1"><strong>Status:</strong> 
                            <?php 
                            $isOverdue = strtotime($borrowing->due_date) < time() && $borrowing->status === 'active';
                            if ($isOverdue): ?>
                                <span class="badge bg-danger">Overdue</span>
                            <?php elseif ($borrowing->status === 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Returned</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Financial Information</h6>
                        <p class="mb-1"><strong>Borrow Fee:</strong> €<?= number_format($borrowing->borrow_fee, 2) ?></p>
                        <p class="mb-1"><strong>Penalty Amount:</strong> 
                            <?php if ($borrowing->penalty_amount > 0): ?>
                                <span class="text-danger fw-semibold">€<?= number_format($borrowing->penalty_amount, 2) ?></span>
                            <?php else: ?>
                                <span class="text-muted">€0.00</span>
                            <?php endif; ?>
                        </p>
                        <p class="mb-1"><strong>Penalty Paid:</strong> 
                            <?php if ($borrowing->penalty_paid): ?>
                                <span class="badge bg-success">Yes</span>
                            <?php else: ?>
                                <span class="badge bg-warning">No</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                
                <?php if ($borrowing->return_date): ?>
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-muted mb-2">Return Information</h6>
                        <p class="mb-1"><strong>Return Date:</strong> <?= date('M d, Y', strtotime($borrowing->return_date)) ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-info-circle me-2 text-info"></i>
                    Additional Information
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Borrowing ID:</strong>
                    <span class="text-muted">#<?= $borrowing->id ?></span>
                </div>
                <div class="mb-3">
                    <strong>Created:</strong>
                    <span class="text-muted"><?= date('M d, Y H:i', strtotime($borrowing->created_at)) ?></span>
                </div>
                <div class="mb-3">
                    <strong>Last Updated:</strong>
                    <span class="text-muted"><?= date('M d, Y H:i', strtotime($borrowing->updated_at)) ?></span>
                </div>
                
                <?php if ($isOverdue): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Overdue Notice:</strong> This book is overdue. Penalties may apply.
                </div>
                <?php endif; ?>
                
                <?php if ($borrowing->penalty_amount > 0 && !$borrowing->penalty_paid): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-currency-euro me-2"></i>
                    <strong>Outstanding Penalty:</strong> €<?= number_format($borrowing->penalty_amount, 2) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
