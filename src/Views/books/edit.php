<?php $title = 'Edit Book'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Edit Book</h2>
    <div class="d-flex gap-2">
        <a href="/books/show?id=<?= $book['id'] ?>" class="btn btn-outline-info">
            <i class="bi bi-eye me-2"></i>View Book
        </a>
        <a href="/books" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Books
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-pencil me-2 text-warning"></i>
                    Edit Book Information
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/books/edit" id="bookForm">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="id" value="<?= $book['id'] ?>">
                    
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <label for="title" class="form-label fw-semibold">Book Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required 
                                   value="<?= htmlspecialchars($book['title']) ?>"
                                   placeholder="Enter book title">
                        </div>
                        <div class="col-md-4">
                            <label for="isbn" class="form-label fw-semibold">ISBN</label>
                            <input type="text" class="form-control" id="isbn" name="isbn" 
                                   value="<?= htmlspecialchars($book['isbn']) ?>"
                                   placeholder="978-0-123456-78-9">
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="author" class="form-label fw-semibold">Author *</label>
                            <input type="text" class="form-control" id="author" name="author" required 
                                   value="<?= htmlspecialchars($book['author']) ?>"
                                   placeholder="Enter author name">
                        </div>
                        <div class="col-md-6">
                            <label for="published_year" class="form-label fw-semibold">Published Year</label>
                            <input type="number" class="form-control" id="published_year" name="published_year" 
                                   value="<?= htmlspecialchars($book['published_year']) ?>"
                                   min="1000" max="<?= date('Y') ?>" placeholder="2023">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" 
                                  placeholder="Enter book description"><?= htmlspecialchars($book['description']) ?></textarea>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="copies_total" class="form-label fw-semibold">Total Copies *</label>
                            <input type="number" class="form-control" id="copies_total" name="copies_total" required 
                                   value="<?= $book['copies_total'] ?>"
                                   min="1" max="100">
                        </div>
                        <div class="col-md-3">
                            <label for="copies_available" class="form-label fw-semibold">Available Copies</label>
                            <input type="number" class="form-control" id="copies_available" 
                                   value="<?= $book['copies_available'] ?>"
                                   min="0" max="<?= $book['copies_total'] ?>" readonly>
                            <small class="text-muted">Auto-calculated based on current borrowings</small>
                        </div>
                        <div class="col-md-3">
                            <label for="borrow_fee" class="form-label fw-semibold">Borrow Fee (€)</label>
                            <input type="number" class="form-control" id="borrow_fee" name="borrow_fee" 
                                   value="<?= $book['borrow_fee'] ?>"
                                   min="0" step="0.01" placeholder="0.00">
                        </div>
                        <div class="col-md-3">
                            <label for="is_active" class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="is_active" name="is_active">
                                <option value="1" <?= $book['is_active'] ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= !$book['is_active'] ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle me-2"></i>Update Book
                        </button>
                        <a href="/books/show?id=<?= $book['id'] ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-info-circle me-2 text-info"></i>
                    Book Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Copies:</span>
                        <span class="fw-semibold"><?= $book['copies_total'] ?></span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Available:</span>
                        <span class="fw-semibold text-success"><?= $book['copies_available'] ?></span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Borrowed:</span>
                        <span class="fw-semibold text-warning"><?= $book['copies_total'] - $book['copies_available'] ?></span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Borrow Fee:</span>
                        <span class="fw-semibold text-success">€<?= number_format($book['borrow_fee'], 2) ?></span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Status:</span>
                        <span class="badge <?= $book['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $book['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="dashboard-card mt-4">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle me-2 text-warning"></i>
                    Important Notes
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <small>
                        <strong>Warning:</strong> Changing the total copies or available copies may affect active borrowings. 
                        Make sure to coordinate with the library staff before making changes.
                    </small>
                </div>
                
                <div class="alert alert-info">
                    <small>
                        <strong>Note:</strong> Available copies cannot exceed total copies. 
                        If you reduce total copies, available copies will be adjusted automatically.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bookForm');
    const copiesTotal = document.getElementById('copies_total');
    const copiesAvailable = document.getElementById('copies_available');
    
    // Available copies are calculated server-side based on current borrowings
    // No need to update them client-side
    
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Updating...';
        submitBtn.disabled = true;
        
        // Re-enable after 3 seconds if no redirect happens
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });
});
</script>
