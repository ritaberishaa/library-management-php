<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use App\Models\Notification;
use App\Models\AuditLog;

class AdminController extends Controller
{
    public function dashboard()
    {
        Auth::requireRole('super_admin');
        
        // Get basic stats
        $total_users = count(User::getAll());
        $total_books = count(Book::getAll());
        $active_borrowings = count(array_filter(Borrowing::getAll(), fn($b) => $b->status === 'active'));
        $overdue_books = count(Borrowing::getOverdue());
        
        // Get recent users (last 5)
        $recent_users = array_slice(User::getAll(), 0, 5);
        $recent_users_array = array_map(function($user) {
            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
                'is_active' => $user->is_active,
                'created_at' => $user->created_at
            ];
        }, $recent_users);
        
        // Get recent activity (mock data for now)
        $recent_activity = [
            [
                'action' => 'User Login',
                'description' => 'Admin logged into the system',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'action' => 'Book Added',
                'description' => 'New book "Introduction to PHP" was added',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ]
        ];
        
        // System overview stats
        $students_count = count(User::getByRole('student'));
        $operators_count = count(User::getByRole('operator'));
        $available_books = array_sum(array_map(fn($book) => $book->copies_available, Book::getAll()));
        $total_revenue = 0; // This would be calculated from actual data
        
        $this->view('admin/dashboard', [
            'total_users' => $total_users,
            'total_books' => $total_books,
            'active_borrowings' => $active_borrowings,
            'overdue_books' => $overdue_books,
            'recent_users' => $recent_users_array,
            'recent_activity' => $recent_activity,
            'students_count' => $students_count,
            'operators_count' => $operators_count,
            'available_books' => $available_books,
            'total_revenue' => $total_revenue
        ]);
    }

    public function users()
    {
        Auth::requireRole('super_admin');
        
        $search = $_GET['search'] ?? '';
        $userObjects = $search ? 
            array_filter(User::getAll(), fn($u) => 
                stripos($u->full_name, $search) !== false || 
                stripos($u->email, $search) !== false
            ) : 
            User::getAll();
        
        // Convert User objects to arrays for the view
        $users = array_map(function($user) {
            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
                'student_id' => $user->username,
                'last_login' => null, // This would need to be tracked
                'is_active' => $user->is_active
            ];
        }, $userObjects);
        
        $this->view('admin/users', [
            'users' => $users,
            'search' => $search
        ]);
    }

    public function settings()
    {
        Auth::requireRole('super_admin');
        
        // Get settings from database
        $settings = $this->getSystemSettings();
        
        $total_users = count(User::getAll());
        $total_books = count(Book::getAll());
        
        $this->view('admin/settings', [
            'settings' => $settings,
            'total_users' => $total_users,
            'total_books' => $total_books
        ]);
    }

    public function auditLogs()
    {
        Auth::requireRole('super_admin');
        
        // Get pagination parameters
        $page = (int) ($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Get filter parameters
        $search = $_GET['search'] ?? '';
        $action_filter = $_GET['action'] ?? '';
        $user_filter = $_GET['user'] ?? '';
        $date_from = $_GET['date_from'] ?? '';
        $date_to = $_GET['date_to'] ?? '';
        
        // Get audit logs based on filters
        if (!empty($search)) {
            $audit_logs = AuditLog::search($search, $limit, $offset);
            $total_count = count(AuditLog::search($search, 1000, 0)); // Get total for pagination
        } elseif (!empty($action_filter)) {
            $audit_logs = AuditLog::getByAction($action_filter, $limit, $offset);
            $total_count = count(AuditLog::getByAction($action_filter, 1000, 0));
        } elseif (!empty($date_from) && !empty($date_to)) {
            $audit_logs = AuditLog::getByDateRange($date_from, $date_to, $limit, $offset);
            $total_count = count(AuditLog::getByDateRange($date_from, $date_to, 1000, 0));
        } else {
            $audit_logs = AuditLog::getAll($limit, $offset);
            $total_count = AuditLog::getTotalCount();
        }
        
        // Process logs to add description and format data
        $processed_logs = [];
        foreach ($audit_logs as $log) {
            $old_values = $log['old_values'] ? json_decode($log['old_values'], true) : null;
            $new_values = $log['new_values'] ? json_decode($log['new_values'], true) : null;
            
            $processed_logs[] = [
                'id' => $log['id'],
                'user_name' => $log['user_name'] ?? 'System',
                'action' => $log['action'],
                'description' => AuditLog::getDescription($log['action'], $log['table_name'], $old_values, $new_values),
                'ip_address' => $log['ip_address'] ?? '127.0.0.1',
                'details' => json_encode([
                    'old_values' => $old_values,
                    'new_values' => $new_values,
                    'table_name' => $log['table_name'],
                    'record_id' => $log['record_id']
                ]),
                'created_at' => $log['created_at']
            ];
        }
        
        // Calculate pagination
        $total_pages = ceil($total_count / $limit);
        $pagination = [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_count' => $total_count,
            'has_prev' => $page > 1,
            'has_next' => $page < $total_pages
        ];
        
        // Get all users for the filter dropdown
        $all_users = User::getAll();
        
        $this->view('admin/audit-logs', [
            'audit_logs' => $processed_logs,
            'pagination' => $pagination,
            'all_users' => $all_users
        ]);
    }

    public function exportAuditLogs()
    {
        Auth::requireRole('super_admin');
        
        $csv_data = AuditLog::exportToCSV();
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="audit_logs_' . date('Y-m-d_H-i-s') . '.csv"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo $csv_data;
        exit;
    }

    public function clearAuditLogs()
    {
        Auth::requireRole('super_admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (AuditLog::clearAll()) {
                $this->setFlash('success', 'All audit logs have been cleared successfully.');
            } else {
                $this->setFlash('error', 'Failed to clear audit logs. Please try again.');
            }
        }
        
        $this->redirect('/admin/audit-logs');
    }

    private function logAuditEvent(string $action, string $table_name, ?int $record_id = null, ?array $old_values = null, ?array $new_values = null): void
    {
        $user_id = Auth::user() ? Auth::user()->id : null;
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        
        AuditLog::create([
            'user_id' => $user_id,
            'action' => $action,
            'table_name' => $table_name,
            'record_id' => $record_id,
            'old_values' => $old_values ? json_encode($old_values) : null,
            'new_values' => $new_values ? json_encode($new_values) : null,
            'ip_address' => $ip_address
        ]);
    }
    
    public function updateSettings()
    {
        Auth::requireRole('super_admin');
        
        // Update settings in database
        $settings = [
            'library_name' => $_POST['library_name'] ?? '',
            'library_email' => $_POST['library_email'] ?? '',
            'max_borrow_days' => (int)($_POST['max_borrow_days'] ?? 14),
            'max_books_per_student' => (int)($_POST['max_books_per_student'] ?? 5),
            'penalty_per_day' => (float)($_POST['penalty_per_day'] ?? 0.5),
            'default_borrow_fee' => (float)($_POST['default_borrow_fee'] ?? 0),
            'library_phone' => $_POST['library_phone'] ?? '',
            'library_address' => $_POST['library_address'] ?? '',
            'library_description' => $_POST['library_description'] ?? ''
        ];
        
        $this->saveSystemSettings($settings);
        
        // Create notification for all students about settings change
        \App\Models\Notification::createForAllStudents(
            'System Settings Updated',
            'Library settings have been updated. Please check the new borrowing limits and policies.',
            'system'
        );
        
        $this->setFlash('success', 'Settings updated successfully.');
        $this->redirect('/admin/settings');
    }
    
    private function getSystemSettings(): array
    {
        $stmt = \App\Core\Database::pdo()->prepare("SELECT setting_key, setting_value FROM system_settings");
        $stmt->execute();
        $dbSettings = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        
        return [
            'library_name' => $dbSettings['library_name'] ?? 'Library Management System',
            'library_email' => $dbSettings['library_email'] ?? 'admin@library.com',
            'max_borrow_days' => (int)($dbSettings['borrowing_duration'] ?? 14),
            'max_books_per_student' => (int)($dbSettings['borrowing_limit'] ?? 5),
            'penalty_per_day' => (float)($dbSettings['penalty_rate'] ?? 0.5),
            'default_borrow_fee' => (float)($dbSettings['borrow_fee'] ?? 0),
            'library_phone' => $dbSettings['library_phone'] ?? '+1 234 567 8900',
            'library_address' => $dbSettings['library_address'] ?? '123 Library Street, City, State',
            'library_description' => $dbSettings['library_description'] ?? 'A modern library management system',
            'require_password_change' => (bool)($dbSettings['require_password_change'] ?? false),
            'enable_audit_log' => (bool)($dbSettings['enable_audit_log'] ?? true),
            'enable_notifications' => (bool)($dbSettings['enable_notifications'] ?? true)
        ];
    }
    
    private function saveSystemSettings(array $settings): void
    {
        $pdo = \App\Core\Database::pdo();
        
        // Map form fields to database keys
        $settingMappings = [
            'library_name' => 'library_name',
            'library_email' => 'library_email', 
            'max_borrow_days' => 'borrowing_duration',
            'max_books_per_student' => 'borrowing_limit',
            'penalty_per_day' => 'penalty_rate',
            'default_borrow_fee' => 'borrow_fee',
            'library_phone' => 'library_phone',
            'library_address' => 'library_address',
            'library_description' => 'library_description'
        ];
        
        foreach ($settingMappings as $formKey => $dbKey) {
            $value = $settings[$formKey] ?? '';
            
            // Check if setting exists, if not create it
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM system_settings WHERE setting_key = ?");
            $checkStmt->execute([$dbKey]);
            $exists = $checkStmt->fetchColumn() > 0;
            
            if ($exists) {
                // Update existing setting
                $stmt = $pdo->prepare("UPDATE system_settings SET setting_value = ?, updated_at = datetime('now') WHERE setting_key = ?");
                $stmt->execute([$value, $dbKey]);
            } else {
                // Insert new setting
                $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value, updated_at) VALUES (?, ?, datetime('now'))");
                $stmt->execute([$dbKey, $value]);
            }
        }
    }
    
    public function notifications()
    {
        Auth::requireRole('super_admin');
        
        $user_id = Auth::user()->id;
        $notifications = \App\Models\Notification::getByUser($user_id);
        
        // Convert Notification objects to arrays for the view
        $notificationsArray = array_map(function($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'is_read' => $notification->is_read,
                'created_at' => $notification->created_at
            ];
        }, $notifications);
        
        $this->view('admin/notifications', [
            'notifications' => $notificationsArray
        ]);
    }
    
    public function markNotificationRead()
    {
        Auth::requireRole('super_admin');
        
        $id = $_GET['id'] ?? 0;
        $notification = \App\Models\Notification::find($id);
        
        if (!$notification) {
            $this->setFlash('error', 'Notification not found.');
            $this->redirect('/admin/notifications');
            return;
        }
        
        if ($notification->markAsRead()) {
            $this->setFlash('success', 'Notification marked as read.');
        } else {
            $this->setFlash('error', 'Failed to mark notification as read.');
        }
        
        $this->redirect('/admin/notifications');
    }
    
    public function markAllNotificationsRead()
    {
        Auth::requireRole('super_admin');
        
        $user_id = Auth::user()->id;
        
        if (\App\Models\Notification::markAllAsRead($user_id)) {
            $this->setFlash('success', 'All notifications marked as read.');
        } else {
            $this->setFlash('error', 'Failed to mark all notifications as read.');
        }
        
        $this->redirect('/admin/notifications');
    }
    
    public function createUser()
    {
        Auth::requireRole('super_admin');
        
        $this->view('admin/user-create', []);
    }
    
    public function storeUser()
    {
        Auth::requireRole('super_admin');
        
        $full_name = $_POST['full_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'student';
        
        // Validate role
        if (!in_array($role, ['student', 'operator', 'super_admin'])) {
            $this->setFlash('error', 'Invalid role selected.');
            $this->redirect('/admin/users/create');
            return;
        }
        $phone = $_POST['phone'] ?? '';
        
        if (empty($full_name) || empty($email) || empty($username) || empty($password)) {
            $this->setFlash('error', 'All fields are required.');
            $this->redirect('/admin/users/create');
            return;
        }
        
        // Check if user already exists
        $existingUserByEmail = User::findByEmail($email);
        $existingUserByUsername = User::findByUsername($username);
        
        if ($existingUserByEmail) {
            $this->setFlash('error', 'User with this email already exists.');
            $this->redirect('/admin/users/create');
            return;
        }
        
        if ($existingUserByUsername) {
            $this->setFlash('error', 'User with this username already exists.');
            $this->redirect('/admin/users/create');
            return;
        }
        
        $user = new User([
            'full_name' => $full_name,
            'email' => $email,
            'username' => $username,
            'role' => $role,
            'phone' => $phone,
            'is_active' => true
        ]);
        
        $user->setPasswordHash(password_hash($password, PASSWORD_DEFAULT));
        
        
        if ($user->save() && $user->id > 0) {
            // Log audit event
            $this->logAuditEvent('create', 'users', $user->id, null, [
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'full_name' => $user->full_name
            ]);
            
            // Create notification for the new user
            $notification = new \App\Models\Notification([
                'user_id' => $user->id,
                'title' => 'Welcome to Library System',
                'message' => "Your account has been created. You can now access the library system with your credentials.",
                'type' => 'system'
            ]);
            $notification->save();
            
            $this->setFlash('success', 'User created successfully.');
            $this->redirect('/admin/users');
        } else {
            $this->setFlash('error', 'Failed to create user. Please try again.');
            $this->redirect('/admin/users/create');
        }
    }
    
    public function editUser()
    {
        Auth::requireRole('super_admin');
        
        $id = $_GET['id'] ?? 0;
        $user = User::find($id);
        
        if (!$user) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('/admin/users');
            return;
        }
        
        $this->view('admin/user-edit', [
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $user->role,
                'phone' => $user->phone,
                'is_active' => $user->is_active
            ]
        ]);
    }
    
    public function updateUser()
    {
        Auth::requireRole('super_admin');
        
        $id = $_POST['id'] ?? 0;
        $user = User::find($id);
        
        if (!$user) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('/admin/users');
            return;
        }
        
        // Store old values for audit log
        $old_values = [
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'full_name' => $user->full_name,
            'phone' => $user->phone,
            'is_active' => $user->is_active
        ];
        
        $user->full_name = $_POST['full_name'] ?? $user->full_name;
        $user->email = $_POST['email'] ?? $user->email;
        $user->username = $_POST['username'] ?? $user->username;
        $user->role = $_POST['role'] ?? $user->role;
        $user->phone = $_POST['phone'] ?? $user->phone;
        $user->is_active = isset($_POST['is_active']) ? (bool)$_POST['is_active'] : $user->is_active;
        
        if ($user->save()) {
            // Log audit event
            $this->logAuditEvent('update', 'users', $user->id, $old_values, [
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'full_name' => $user->full_name,
                'phone' => $user->phone,
                'is_active' => $user->is_active
            ]);
            
            $this->setFlash('success', 'User updated successfully.');
            $this->redirect('/admin/users');
        } else {
            $this->setFlash('error', 'Failed to update user. Please try again.');
            $this->redirect('/admin/users/edit?id=' . $id);
        }
    }
    
    public function deleteUser()
    {
        Auth::requireRole('super_admin');
        
        $id = $_GET['id'] ?? 0;
        $user = User::find($id);
        
        if (!$user) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('/admin/users');
            return;
        }
        
        if ($user->id === Auth::user()->id) {
            $this->setFlash('error', 'You cannot delete your own account.');
            $this->redirect('/admin/users');
            return;
        }
        
        if ($user->delete()) {
            // Log audit event
            $this->logAuditEvent('delete', 'users', $user->id, [
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'full_name' => $user->full_name
            ], null);
            
            $this->setFlash('success', 'User deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete user. Please try again.');
        }
        
        $this->redirect('/admin/users');
    }
}