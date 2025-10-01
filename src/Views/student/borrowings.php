<?php $title = 'My Borrowings'; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-list-ul"></i> Borrowing History</h5>
            </div>
            <div class="card-body">
                <?php if (empty($borrowings)): ?>
                    <p class="text-muted">No borrowing history.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Book</th>
                                    <th>Author</th>
                                    <th>Borrow Date</th>
                                    <th>Due Date</th>
                                    <th>Return Date</th>
                                    <th>Status</th>
                                    <th>Fee</th>
                                    <th>Penalty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($borrowings as $borrowing): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($borrowing['title']) ?></td>
                                        <td><?= htmlspecialchars($borrowing['author']) ?></td>
                                        <td><?= date('M d, Y', strtotime($borrowing['borrow_date'])) ?></td>
                                        <td><?= date('M d, Y', strtotime($borrowing['due_date'])) ?></td>
                                        <td>
                                            <?php if ($borrowing['return_date']): ?>
                                                <?= date('M d, Y', strtotime($borrowing['return_date'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Not returned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($borrowing['status'] === 'active'): ?>
                                                <?php if (strtotime($borrowing['due_date']) < time()): ?>
                                                    <span class="badge bg-danger">Overdue</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Active</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-success">Returned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>€<?= number_format($borrowing['borrow_fee'], 2) ?></td>
                                        <td>
                                            <?php if ($borrowing['penalty_amount'] > 0): ?>
                                                €<?= number_format($borrowing['penalty_amount'], 2) ?>
                                                <?php if (!$borrowing['penalty_paid']): ?>
                                                    <span class="badge bg-danger">Unpaid</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Paid</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">None</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($borrowing['status'] === 'active'): ?>
                                                <a href="/books/return?id=<?= $borrowing['id'] ?>" class="btn btn-sm btn-primary">Return</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
