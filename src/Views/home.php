<?php $title = 'Home'; ?>

<div class="hero-section">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <div class="mb-4">
                    <i class="bi bi-book text-primary" style="font-size: 3rem;"></i>
                </div>
                <h1 class="display-4 fw-bold mb-4 text-gray-800">
                    Library Management System
                </h1>
                <p class="lead mb-5 text-gray-600">A comprehensive library management solution with role-based access control</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="/login" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Get Started
                    </a>
                    <a href="#features" class="btn btn-outline-primary btn-lg px-4">
                        <i class="bi bi-info-circle me-2"></i>Learn More
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row g-4 mb-5">
        <div class="col-lg-4 col-md-6">
            <div class="feature-card">
                <div class="feature-icon" style="background: var(--primary-color);">
                    <i class="bi bi-person-check"></i>
                </div>
                <h4 class="fw-bold mb-3">Students</h4>
                <p class="text-muted mb-4">Browse books, borrow items, and manage your account with ease</p>
                <a href="/login" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login as Student
                </a>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="feature-card">
                <div class="feature-icon" style="background: var(--warning-color);">
                    <i class="bi bi-gear-fill"></i>
                </div>
                <h4 class="fw-bold mb-3">Operators</h4>
                <p class="text-muted mb-4">Manage books, process borrowings, and handle returns efficiently</p>
                <a href="/login" class="btn btn-warning w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login as Operator
                </a>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="feature-card">
                <div class="feature-icon" style="background: var(--danger-color);">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h4 class="fw-bold mb-3">Administrators</h4>
                <p class="text-muted mb-4">Full system control, user management, and advanced settings</p>
                <a href="/login" class="btn btn-danger w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login as Admin
                </a>
            </div>
        </div>
    </div>
    
    <div id="features" class="row g-4 mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: var(--gray-50); border-bottom: 1px solid var(--gray-200);">
                    <h4 class="mb-0 text-gray-800">
                        <i class="bi bi-star-fill me-2 text-primary"></i>System Features
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary rounded p-2">
                                        <i class="bi bi-person-check text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fw-bold text-gray-800">Student Features</h5>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Search and browse books</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Borrow books with automatic due dates</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>View borrowing history</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Pay penalties for overdue books</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Receive notifications</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning rounded p-2">
                                        <i class="bi bi-gear-fill text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fw-bold text-gray-800">Management Features</h5>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Inventory management</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>User account management</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Penalty calculation and tracking</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Low stock notifications</li>
                                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>System audit logs</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: var(--gray-50); border-bottom: 1px solid var(--gray-200);">
                    <h4 class="mb-0 text-gray-800">
                        <i class="bi bi-key-fill me-2 text-primary"></i>Demo Accounts
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-4 col-md-6">
                            <div class="text-center p-4 border rounded" style="background: white; border-color: var(--gray-200);">
                                <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                    <i class="bi bi-shield-check text-white"></i>
                                </div>
                                <h5 class="fw-bold text-gray-800">Super Admin</h5>
                                <p class="text-muted mb-3">Full system access</p>
                                <div class="bg-gray-50 p-3 rounded" style="background: var(--gray-50);">
                                    <small class="text-muted">Username:</small> <code class="text-primary">admin</code><br>
                                    <small class="text-muted">Password:</small> <code class="text-primary">admin123</code>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="text-center p-4 border rounded" style="background: white; border-color: var(--gray-200);">
                                <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                    <i class="bi bi-gear-fill text-white"></i>
                                </div>
                                <h5 class="fw-bold text-gray-800">Operator</h5>
                                <p class="text-muted mb-3">Book management</p>
                                <div class="bg-gray-50 p-3 rounded" style="background: var(--gray-50);">
                                    <small class="text-muted">Username:</small> <code class="text-primary">operator</code><br>
                                    <small class="text-muted">Password:</small> <code class="text-primary">operator123</code>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="text-center p-4 border rounded" style="background: white; border-color: var(--gray-200);">
                                <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                    <i class="bi bi-person-check text-white"></i>
                                </div>
                                <h5 class="fw-bold text-gray-800">Student</h5>
                                <p class="text-muted mb-3">Book borrowing</p>
                                <div class="bg-gray-50 p-3 rounded" style="background: var(--gray-50);">
                                    <small class="text-muted">Username:</small> <code class="text-primary">student</code><br>
                                    <small class="text-muted">Password:</small> <code class="text-primary">student123</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
