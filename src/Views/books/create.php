<?php $title = 'Add New Book'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Add New Book</h2>
    <a href="/books" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Books
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-plus-circle me-2 text-success"></i>
                    Book Information
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/operator/books/create" id="bookForm">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <label for="title" class="form-label fw-semibold">Book Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required 
                                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                                   placeholder="Enter book title">
                        </div>
                        <div class="col-md-4">
                            <label for="isbn" class="form-label fw-semibold">ISBN</label>
                            <input type="text" class="form-control" id="isbn" name="isbn" 
                                   value="<?= htmlspecialchars($_POST['isbn'] ?? '') ?>"
                                   placeholder="978-0-123456-78-9">
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="author" class="form-label fw-semibold">Author *</label>
                            <input type="text" class="form-control" id="author" name="author" required 
                                   value="<?= htmlspecialchars($_POST['author'] ?? '') ?>"
                                   placeholder="Enter author name">
                        </div>
                        <div class="col-md-6">
                            <label for="published_year" class="form-label fw-semibold">Published Year</label>
                            <input type="number" class="form-control" id="published_year" name="published_year" 
                                   value="<?= htmlspecialchars($_POST['published_year'] ?? '') ?>"
                                   min="1000" max="<?= date('Y') ?>" placeholder="2023">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" 
                                  placeholder="Enter book description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="copies_total" class="form-label fw-semibold">Total Copies *</label>
                            <input type="number" class="form-control" id="copies_total" name="copies_total" required 
                                   value="<?= htmlspecialchars($_POST['copies_total'] ?? '1') ?>"
                                   min="1" max="100">
                        </div>
                        <div class="col-md-4">
                            <label for="borrow_fee" class="form-label fw-semibold">Borrow Fee (€)</label>
                            <input type="number" class="form-control" id="borrow_fee" name="borrow_fee" 
                                   value="<?= htmlspecialchars($_POST['borrow_fee'] ?? \App\Models\Book::getDefaultBorrowFee()) ?>"
                                   min="0" step="0.01" placeholder="0.00">
                        </div>
                        <div class="col-md-4">
                            <label for="is_active" class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="is_active" name="is_active">
                                <option value="1" <?= ($_POST['is_active'] ?? '1') == '1' ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= ($_POST['is_active'] ?? '1') == '0' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Add Book
                        </button>
                        <a href="/books" class="btn btn-outline-secondary">
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
                    Guidelines
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="fw-semibold">Required Fields</h6>
                    <ul class="text-muted small">
                        <li>Book Title</li>
                        <li>Author</li>
                        <li>Total Copies</li>
                    </ul>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-semibold">Optional Fields</h6>
                    <ul class="text-muted small">
                        <li>ISBN (for identification)</li>
                        <li>Published Year</li>
                        <li>Description</li>
                        <li>Borrow Fee (default: €0.00)</li>
                    </ul>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-semibold">Tips</h6>
                    <ul class="text-muted small">
                        <li>Use clear, descriptive titles</li>
                        <li>Include full author names</li>
                        <li>Set appropriate borrow fees</li>
                        <li>Add copies based on demand</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bookForm');
    
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Adding...';
        submitBtn.disabled = true;
        
        // Re-enable after 3 seconds if no redirect happens
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });
});
</script>
