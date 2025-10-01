<?php $title = 'Browse Books'; ?>

<div class="row mb-3">
    <div class="col-md-6">
        <form method="GET" action="/student/books">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search books..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <?php if (empty($books)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No books found.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($books as $book): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($book->title) ?></h5>
                        <p class="card-text">
                            <strong>Author:</strong> <?= htmlspecialchars($book->author) ?><br>
                            <?php if ($book->isbn): ?>
                                <strong>ISBN:</strong> <?= htmlspecialchars($book->isbn) ?><br>
                            <?php endif; ?>
                            <?php if ($book->published_year): ?>
                                <strong>Year:</strong> <?= $book->published_year ?><br>
                            <?php endif; ?>
                            <strong>Available:</strong> <?= $book->copies_available ?> / <?= $book->copies_total ?><br>
                            <strong>Borrow Fee:</strong> €<?= number_format($book->borrow_fee, 2) ?>
                        </p>
                        <?php if ($book->description): ?>
                            <p class="card-text">
                                <small class="text-muted"><?= htmlspecialchars(substr($book->description, 0, 100)) ?>...</small>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <?php if ($book->canBorrow()): ?>
                            <a href="/books/borrow?id=<?= $book->id ?>" class="btn btn-primary">
                                <i class="bi bi-book"></i> Borrow Book
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>
                                <i class="bi bi-x-circle"></i> Not Available
                            </button>
                        <?php endif; ?>
                        <a href="/books/show?id=<?= $book->id ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
