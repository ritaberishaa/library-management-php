<?php $title = 'Login'; ?>

<div class="container-fluid" style="background: var(--gray-50); min-height: 100vh;">
    <div class="row min-vh-100">
        <!-- Left side - Login Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-5">
            <div class="w-100" style="max-width: 400px;">
                <div class="text-center mb-5">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-book text-white" style="font-size: 2rem;"></i>
                    </div>
                    <h2 class="fw-bold text-gray-800">Welcome Back</h2>
                    <p class="text-muted">Sign in to your library account</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="/login">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            
                            <div class="mb-4">
                                <label for="username" class="form-label fw-semibold text-gray-700">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-person text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="username" name="username" placeholder="Enter your username" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold text-gray-700">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock text-muted"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="Enter your password" required>
                                </div>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center">
                            <p class="text-muted">Don't have an account? 
                                <a href="/register" class="text-primary fw-semibold text-decoration-none">Register here</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right side - Demo Accounts -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-5" style="background: white;">
            <div class="text-center w-100" style="max-width: 500px;">
                <h3 class="fw-bold mb-4 text-gray-800">Demo Accounts</h3>
                <p class="text-muted mb-5">Try the system with these pre-configured accounts</p>
                
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card border" style="border-color: var(--gray-200);">
                            <div class="card-body text-start">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="bi bi-shield-check text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-gray-800">Super Admin</h6>
                                        <small class="text-muted">Full system access</small>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-2 rounded" style="background: var(--gray-50);">
                                    <small class="text-muted">Username:</small> <code class="text-primary">admin</code><br>
                                    <small class="text-muted">Password:</small> <code class="text-primary">admin123</code>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="card border" style="border-color: var(--gray-200);">
                            <div class="card-body text-start">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="bi bi-gear-fill text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-gray-800">Operator</h6>
                                        <small class="text-muted">Book management</small>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-2 rounded" style="background: var(--gray-50);">
                                    <small class="text-muted">Username:</small> <code class="text-primary">operator</code><br>
                                    <small class="text-muted">Password:</small> <code class="text-primary">operator123</code>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="card border" style="border-color: var(--gray-200);">
                            <div class="card-body text-start">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="bi bi-person-check text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-gray-800">Student</h6>
                                        <small class="text-muted">Book borrowing</small>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-2 rounded" style="background: var(--gray-50);">
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
