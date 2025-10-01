<?php $title = 'Manage Books'; ?>

<div class="d-flex justify-content-end align-items-center mb-4">
    <a href="/operator/books/create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add New Book
    </a>
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
        <select class="form-select" id="statusFilter">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" id="authorFilter">
            <option value="">All Authors</option>
            <!-- Authors will be populated dynamically -->
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" id="stockFilter">
            <option value="">All Stock</option>
            <option value="available">Available</option>
            <option value="unavailable">Unavailable</option>
            <option value="low_stock">Low Stock</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
            <i class="bi bi-x-circle me-1"></i>Clear
        </button>
    </div>
</div>

<!-- Books Table -->
<div class="dashboard-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="booksTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 py-3">Title</th>
                        <th class="border-0 py-3">Author</th>
                        <th class="border-0 py-3">ISBN</th>
                        <th class="border-0 py-3">Copies</th>
                        <th class="border-0 py-3">Available</th>
                        <th class="border-0 py-3">Borrow Fee</th>
                        <th class="border-0 py-3">Status</th>
                        <th class="border-0 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($books)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">No books found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td class="fw-semibold py-3"><?= htmlspecialchars($book['title']) ?></td>
                                <td class="text-muted py-3"><?= htmlspecialchars($book['author']) ?></td>
                                <td class="py-3"><?= htmlspecialchars($book['isbn']) ?></td>
                                <td class="py-3"><?= $book['copies'] ?></td>
                                <td class="py-3"><?= $book['available_copies'] ?></td>
                                <td class="py-3">€<?= number_format($book['borrow_fee'], 2) ?></td>
                                <td class="py-3">
                                    <span class="badge <?= $book['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $book['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="btn-group" role="group">
                                        <a href="/operator/books/edit?id=<?= $book['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="/operator/books/delete?id=<?= $book['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this book?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const authorFilter = document.getElementById('authorFilter');
    const stockFilter = document.getElementById('stockFilter');
    const table = document.getElementById('booksTable');
    
    // Populate author filter with unique authors
    function populateAuthorFilter() {
        const authors = new Set();
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const author = row.cells[1].textContent.trim();
            if (author) {
                authors.add(author);
            }
        });
        
        // Clear existing options except "All Authors"
        authorFilter.innerHTML = '<option value="">All Authors</option>';
        
        // Add unique authors to dropdown
        Array.from(authors).sort().forEach(author => {
            const option = document.createElement('option');
            option.value = author;
            option.textContent = author;
            authorFilter.appendChild(option);
        });
    }
    
    // Initialize author filter
    populateAuthorFilter();
    
    // Ensure all books are visible on initial load
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        row.style.display = '';
    });
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const authorValue = authorFilter.value;
        const stockValue = stockFilter.value;
        
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const title = row.cells[0].textContent.toLowerCase();
            const author = row.cells[1].textContent.toLowerCase();
            const status = row.cells[6].textContent.toLowerCase();
            const available = parseInt(row.cells[4].textContent);
            const total = parseInt(row.cells[3].textContent);
            
            const matchesSearch = title.includes(searchTerm) || author.includes(searchTerm);
            const matchesStatus = !statusValue || status.includes(statusValue);
            const matchesAuthor = !authorValue || author === authorValue.toLowerCase();
            const matchesStock = !stockValue || 
                (stockValue === 'available' && available > 0) ||
                (stockValue === 'unavailable' && available === 0) ||
                (stockValue === 'low_stock' && available <= 2 && available > 0);
            
            if (matchesSearch && matchesStatus && matchesAuthor && matchesStock) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    function clearFilters() {
        searchInput.value = '';
        statusFilter.value = '';
        authorFilter.value = '';
        stockFilter.value = '';
        filterTable();
    }
    
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    authorFilter.addEventListener('change', filterTable);
    stockFilter.addEventListener('change', filterTable);
    
    // Make clearFilters available globally
    window.clearFilters = clearFilters;
});
</script>
