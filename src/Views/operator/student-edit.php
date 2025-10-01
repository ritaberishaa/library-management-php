<?php $title = 'Edit Student'; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="d-flex align-items-center">
                    <i class="bi bi-person-gear me-2 text-primary"></i>
                    Edit Student Information
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/operator/students/edit?id=<?= $student->id ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?= htmlspecialchars($student->full_name) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($student->email) ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="student_id" class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="student_id" name="student_id" 
                                   value="<?= htmlspecialchars($student->username) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       <?= $student->is_active ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    Active Account
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/operator/students" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Students
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Student
                        </button>
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
                    Student Information
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Student ID:</strong>
                    <span class="text-muted"><?= htmlspecialchars($student->username) ?></span>
                </div>
                <div class="mb-3">
                    <strong>Email:</strong>
                    <span class="text-muted"><?= htmlspecialchars($student->email) ?></span>
                </div>
                <div class="mb-3">
                    <strong>Status:</strong>
                    <span class="badge <?= $student->is_active ? 'bg-success' : 'bg-secondary' ?>">
                        <?= $student->is_active ? 'Active' : 'Inactive' ?>
                    </span>
                </div>
                <div class="mb-3">
                    <strong>Created:</strong>
                    <span class="text-muted"><?= date('M d, Y', strtotime($student->created_at)) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
