<?php $title = 'Book Details'; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-book me-2 text-primary"></i>
                    Book Details
                </h5>
            </div>
            <div class="card-body">
                <?php if ($book): ?>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="text-center">
                                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px;">
                                    <i class="bi bi-book text-white" style="font-size: 3rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h2 class="mb-3 fw-bold"><?= htmlspecialchars($book['title']) ?></h2>
                            <p class="text-muted mb-3 fs-5">by <?= htmlspecialchars($book['author']) ?></p>
                            
                            <div class="row mb-4">
                                <div class="col-sm-6">
                                    <strong>ISBN:</strong>
                                    <span class="text-muted"><?= htmlspecialchars($book['isbn'] ?? 'N/A') ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Published Year:</strong>
                                    <span class="text-muted"><?= htmlspecialchars($book['published_year'] ?? 'N/A') ?></span>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-sm-6">
                                    <strong>Total Copies:</strong>
                                    <span class="badge bg-info"><?= $book['copies_total'] ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Available Copies:</strong>
                                    <span class="badge bg-success"><?= $book['copies_available'] ?></span>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-sm-6">
                                    <strong>Borrow Fee:</strong>
                                    <span class="text-success fw-semibold">€<?= number_format($book['borrow_fee'], 2) ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Status:</strong>
                                    <span class="badge <?= $book['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $book['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if ($book['description']): ?>
                                <div class="mb-4">
                                    <strong>Description:</strong>
                                    <p class="text-muted mt-2"><?= htmlspecialchars($book['description']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3 mb-0 fs-5">Book not found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-list-ul me-2 text-info"></i>
                    Actions
                </h5>
            </div>
            <div class="card-body">
                <?php if ($book): ?>
                    <div class="d-grid gap-2">
                        <?php if (App\Core\Auth::isStudent() && $book['copies_available'] > 0): ?>
                            <a href="/books/borrow?id=<?= $book['id'] ?>" class="btn btn-primary">
                                <i class="bi bi-book me-2"></i>Borrow Book
                            </a>
                        <?php elseif (App\Core\Auth::isStudent()): ?>
                            <button class="btn btn-secondary" disabled>
                                <i class="bi bi-x-circle me-2"></i>Not Available
                            </button>
                        <?php endif; ?>
                        
                        <?php if (App\Core\Auth::canManageBooks()): ?>
                            <a href="/books/edit?id=<?= $book['id'] ?>" class="btn btn-warning">
                                <i class="bi bi-pencil me-2"></i>Edit Book
                            </a>
                            <a href="/books/delete?id=<?= $book['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this book?')">
                                <i class="bi bi-trash me-2"></i>Delete Book
                            </a>
                        <?php endif; ?>
                        
                        <?php 
                        $backUrl = '/books';
                        if (App\Core\Auth::isStudent()) {
                            $backUrl = '/student/books';
                        } elseif (App\Core\Auth::isOperator()) {
                            $backUrl = '/operator/books';
                        }
                        ?>
                        <a href="<?= $backUrl ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Books
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($book && isset($recent_borrowings) && !empty($recent_borrowings)): ?>
            <div class="dashboard-card mt-4">
                <div class="card-header">
                    <h5 class="d-flex align-items-center">
                        <i class="bi bi-clock-history me-2 text-warning"></i>
                        Recent Borrowings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_borrowings as $borrowing): ?>
                            <div class="list-group-item border-0 px-0 py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold"><?= htmlspecialchars($borrowing['student_name']) ?></h6>
                                        <small class="text-muted">
                                            Borrowed: <?= $borrowing['borrowed_date'] ? date('M d, Y', strtotime($borrowing['borrowed_date'])) : 'Unknown' ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            Due: <?= date('M d, Y', strtotime($borrowing['due_date'])) ?>
                                        </small>
                                    </div>
                                    <span class="badge <?= $borrowing['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ucfirst($borrowing['status']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
