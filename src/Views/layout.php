<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Library Management System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-light: #dbeafe;
            --secondary-color: #6b7280;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --light-bg: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --sidebar-bg: #ffffff;
            --sidebar-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            --border-radius: 0.5rem;
            --transition: all 0.2s ease-in-out;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--gray-50);
            color: var(--gray-800);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            min-height: 100vh;
            background: var(--sidebar-bg);
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
            border-right: 1px solid var(--gray-200);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: var(--transition);
            width: 280px;
            padding: 1.5rem 0;
        }

        .sidebar .nav-link {
            color: var(--gray-600);
            padding: 0.875rem 1.5rem;
            margin: 0.125rem 1rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border: none;
            background: none;
        }

        .sidebar .nav-link:hover {
            background: var(--primary-light);
            color: var(--primary-color);
        }

        .sidebar .nav-link.active {
            background: var(--primary-color);
            color: white;
            font-weight: 600;
        }

        .main-content {
            min-height: 100vh;
            margin-left: 280px;
            background: var(--gray-50);
            transition: var(--transition);
            padding: 0;
        }

        .content-wrapper {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            background: white;
        }

        .card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn {
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition);
            border: 1px solid transparent;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
        }

        .btn-success {
            background: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }

        .btn-warning {
            background: var(--warning-color);
            border-color: var(--warning-color);
            color: white;
        }

        .btn-danger {
            background: var(--danger-color);
            border-color: var(--danger-color);
            color: white;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .notification-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .hero-section {
            background: white;
            color: var(--gray-800);
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .feature-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            border: 1px solid var(--gray-200);
        }

        .feature-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1.5rem;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stats-card {
            background: white;
            color: var(--gray-800);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
            border: 1px solid var(--gray-200);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            height: 100%;
        }

        .stats-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 0;
            margin-bottom: 2rem;
            transition: var(--transition);
            border: 1px solid var(--gray-200);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .dashboard-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .dashboard-card .card-header {
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            padding: 1.25rem 1.5rem;
            margin: 0;
        }

        .dashboard-card .card-header h5 {
            margin: 0;
            font-weight: 600;
            color: var(--gray-800);
            font-size: 1.1rem;
        }

        .dashboard-card .card-body {
            padding: 1.5rem;
        }

        .table {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            background: white;
        }

        .table thead th {
            background: var(--gray-50);
            color: var(--gray-700);
            border: none;
            font-weight: 600;
            padding: 1rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .table tbody tr {
            transition: var(--transition);
            border-bottom: 1px solid var(--gray-100);
        }

        .table tbody tr:hover {
            background: var(--gray-50);
        }

        .badge {
            border-radius: 0.375rem;
            padding: 0.25rem 0.75rem;
            font-weight: 500;
            font-size: 0.75rem;
        }

        .alert {
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .form-control {
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-300);
            transition: var(--transition);
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <?php if (App\Core\Auth::check()): ?>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4><i class="bi bi-book"></i> Library System</h4>
                        <small class="text-muted">Welcome, <?= htmlspecialchars(App\Core\Auth::user()->full_name) ?></small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <?php if (App\Core\Auth::isStudent()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/student/dashboard' ? 'active' : '' ?>" href="/student/dashboard">
                                    <i class="bi bi-house"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/student/books' ? 'active' : '' ?>" href="/student/books">
                                    <i class="bi bi-book"></i> Browse Books
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/student/borrowings' ? 'active' : '' ?>" href="/student/borrowings">
                                    <i class="bi bi-list-ul"></i> My Borrowings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/student/penalties' ? 'active' : '' ?>" href="/student/penalties">
                                    <i class="bi bi-exclamation-triangle"></i> Penalties
                                </a>
                            </li>
                        <?php elseif (App\Core\Auth::isOperator()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/operator/dashboard' ? 'active' : '' ?>" href="/operator/dashboard">
                                    <i class="bi bi-house"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/operator/books' ? 'active' : '' ?>" href="/operator/books">
                                    <i class="bi bi-book"></i> Manage Books
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/operator/borrowings' ? 'active' : '' ?>" href="/operator/borrowings">
                                    <i class="bi bi-list-ul"></i> Borrowings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/operator/students' ? 'active' : '' ?>" href="/operator/students">
                                    <i class="bi bi-people"></i> Students
                                </a>
                            </li>
                        <?php elseif (App\Core\Auth::isSuperAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/admin/dashboard' ? 'active' : '' ?>" href="/admin/dashboard">
                                    <i class="bi bi-house"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/admin/users' ? 'active' : '' ?>" href="/admin/users">
                                    <i class="bi bi-people"></i> Manage Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/admin/settings' ? 'active' : '' ?>" href="/admin/settings">
                                    <i class="bi bi-gear"></i> Settings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/admin/audit-logs' ? 'active' : '' ?>" href="/admin/audit-logs">
                                    <i class="bi bi-journal-text"></i> Audit Logs
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="/change-password">
                                <i class="bi bi-key"></i> Change Password
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/logout">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                <div class="content-wrapper">
                    <!-- Top Navigation Bar -->
                    <div class="d-flex justify-content-between align-items-center py-4 mb-4 bg-white rounded-3 shadow-sm px-4">
                        <div>
                            <h1 class="h3 mb-0 fw-semibold text-gray-800"><?= $title ?? 'Dashboard' ?></h1>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <a href="/notifications" class="btn btn-outline-secondary position-relative">
                                <i class="bi bi-bell"></i>
                                <?php 
                                $notification_count = App\Models\Notification::getUnreadCount(App\Core\Auth::user()->id);
                                if ($notification_count > 0): 
                                ?>
                                    <span class="notification-badge"><?= $notification_count ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>

                <!-- Flash Messages -->
                <?php if (isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['flash_success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['flash_success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['flash_error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>

                    <?= $content ?? '' ?>
                </div>
            </main>
        </div>
    </div>
    <?php else: ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <!-- Flash Messages -->
                <?php if (isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['flash_success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['flash_success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['flash_error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>

                <?= $content ?? '' ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced UI interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Add smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add loading states to buttons
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Loading...';
                        submitBtn.disabled = true;
                    }
                });
            });

            // Add hover effects to cards
            document.querySelectorAll('.card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Add click effects to buttons
            document.querySelectorAll('.btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });
        });
    </script>
</body>
</html>
