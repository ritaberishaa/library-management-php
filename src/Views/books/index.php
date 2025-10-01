<?php $title = 'Books'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Books</h2>
    <?php if (App\Core\Auth::canManageBooks()): ?>
        <a href="/books/create" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add New Book
        </a>
    <?php endif; ?>
</div>

<!-- Search and Filter -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search books..." id="searchInput">
        </div>
    </div>
    <div class="col-md-2">
        <select class="form-select" id="authorFilter">
            <option value="">All Authors</option>
            <!-- Authors will be populated dynamically -->
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" id="statusFilter">
            <option value="">All Status</option>
            <option value="available">Available</option>
            <option value="unavailable">Unavailable</option>
            <option value="low_stock">Low Stock</option>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" id="yearFilter">
            <option value="">All Years</option>
            <!-- Years will be populated dynamically -->
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
            <i class="bi bi-x-circle me-1"></i>Clear
        </button>
    </div>
</div>

<!-- Books Grid -->
<div class="row" id="booksGrid">
    <?php if (empty($books)): ?>
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3 mb-0 fs-5">No books found</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($books as $book): ?>
            <div class="col-lg-4 col-md-6 mb-4 book-card" 
                 data-title="<?= strtolower(htmlspecialchars($book['title'])) ?>"
                 data-author="<?= strtolower(htmlspecialchars($book['author'])) ?>"
                 data-status="<?= $book['copies_available'] > 0 ? 'available' : 'unavailable' ?>"
                 data-low-stock="<?= $book['copies_available'] <= 2 ? 'low_stock' : '' ?>"
                 data-year="<?= $book['published_year'] ?? '' ?>">
                <div class="dashboard-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h5 class="card-title fw-bold mb-1"><?= htmlspecialchars($book['title']) ?></h5>
                                <p class="text-muted mb-2">by <?= htmlspecialchars($book['author']) ?></p>
                            </div>
                            <div class="text-end">
                                <span class="badge <?= $book['copies_available'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $book['copies_available'] > 0 ? 'Available' : 'Unavailable' ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Copies:</small>
                                <div class="fw-semibold"><?= $book['copies_available'] ?>/<?= $book['copies_total'] ?></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Fee:</small>
                                <div class="fw-semibold text-success">€<?= number_format($book['borrow_fee'], 2) ?></div>
                            </div>
                        </div>
                        
                        <?php if ($book['isbn']): ?>
                            <div class="mb-3">
                                <small class="text-muted">ISBN:</small>
                                <div class="small"><?= htmlspecialchars($book['isbn']) ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($book['published_year']): ?>
                            <div class="mb-3">
                                <small class="text-muted">Published:</small>
                                <div class="small"><?= $book['published_year'] ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($book['description']): ?>
                            <div class="mb-3">
                                <p class="small text-muted"><?= htmlspecialchars(substr($book['description'], 0, 100)) ?><?= strlen($book['description']) > 100 ? '...' : '' ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex gap-2">
                            <a href="/books/show?id=<?= $book['id'] ?>" class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            
                            <?php if (App\Core\Auth::isStudent() && $book['copies_available'] > 0): ?>
                                <a href="/books/borrow?id=<?= $book['id'] ?>" class="btn btn-sm btn-success flex-fill">
                                    <i class="bi bi-book me-1"></i>Borrow
                                </a>
                            <?php elseif (App\Core\Auth::isStudent()): ?>
                                <button class="btn btn-sm btn-secondary flex-fill" disabled>
                                    <i class="bi bi-x-circle me-1"></i>Unavailable
                                </button>
                            <?php endif; ?>
                            
                            <?php if (App\Core\Auth::canManageBooks()): ?>
                                <a href="/books/edit?id=<?= $book['id'] ?>" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const authorFilter = document.getElementById('authorFilter');
    const statusFilter = document.getElementById('statusFilter');
    const yearFilter = document.getElementById('yearFilter');
    const booksGrid = document.getElementById('booksGrid');
    const bookCards = document.querySelectorAll('.book-card');
    
    // Populate author filter
    const authors = [...new Set(Array.from(bookCards).map(card => card.dataset.author))];
    authors.sort().forEach(author => {
        const option = document.createElement('option');
        option.value = author;
        option.textContent = author.charAt(0).toUpperCase() + author.slice(1);
        authorFilter.appendChild(option);
    });
    
    // Populate year filter
    const years = [...new Set(Array.from(bookCards).map(card => card.dataset.year).filter(year => year))];
    years.sort((a, b) => b - a).forEach(year => {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        yearFilter.appendChild(option);
    });
    
    function filterBooks() {
        const searchTerm = searchInput.value.toLowerCase();
        const authorValue = authorFilter.value;
        const statusValue = statusFilter.value;
        const yearValue = yearFilter.value;
        
        bookCards.forEach(card => {
            const title = card.dataset.title;
            const author = card.dataset.author;
            const status = card.dataset.status;
            const lowStock = card.dataset.lowStock;
            const year = card.dataset.year;
            
            const matchesSearch = title.includes(searchTerm) || author.includes(searchTerm);
            const matchesAuthor = !authorValue || author === authorValue;
            const matchesStatus = !statusValue || 
                (statusValue === 'available' && status === 'available') ||
                (statusValue === 'unavailable' && status === 'unavailable') ||
                (statusValue === 'low_stock' && lowStock === 'low_stock');
            const matchesYear = !yearValue || year === yearValue;
            
            if (matchesSearch && matchesAuthor && matchesStatus && matchesYear) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    function clearFilters() {
        searchInput.value = '';
        authorFilter.value = '';
        statusFilter.value = '';
        yearFilter.value = '';
        filterBooks();
    }
    
    searchInput.addEventListener('input', filterBooks);
    authorFilter.addEventListener('change', filterBooks);
    statusFilter.addEventListener('change', filterBooks);
    yearFilter.addEventListener('change', filterBooks);
    
    // Make clearFilters available globally
    window.clearFilters = clearFilters;
});
</script>
