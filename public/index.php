<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Start session
session_start();

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require __DIR__ . '/../src/Core/Router.php';
require __DIR__ . '/../src/Core/Controller.php';
require __DIR__ . '/../src/Core/Database.php';
require __DIR__ . '/../src/Core/Auth.php';
require __DIR__ . '/../src/Core/Middleware.php';

// Load all models
require __DIR__ . '/../src/Models/User.php';
require __DIR__ . '/../src/Models/Book.php';
require __DIR__ . '/../src/Models/Borrowing.php';
require __DIR__ . '/../src/Models/Notification.php';
require __DIR__ . '/../src/Models/AuditLog.php';

// Load all controllers
require __DIR__ . '/../src/Controllers/HomeController.php';
require __DIR__ . '/../src/Controllers/AuthController.php';
require __DIR__ . '/../src/Controllers/BookController.php';
require __DIR__ . '/../src/Controllers/StudentController.php';
require __DIR__ . '/../src/Controllers/OperatorController.php';
require __DIR__ . '/../src/Controllers/AdminController.php';

App\Core\Database::init([
  'driver' => 'sqlite',
  'sqlite' => __DIR__ . '/../database.sqlite'
]);

$router = new App\Core\Router();

// Public routes
$router->get('/', 'HomeController@index');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');
$router->get('/unauthorized', 'AuthController@unauthorized');

// Student routes
$router->get('/student/dashboard', 'StudentController@dashboard');
$router->get('/student/books', 'StudentController@books');
$router->get('/student/borrowings', 'StudentController@borrowings');
$router->get('/student/penalties', 'StudentController@penalties');
$router->get('/student/notifications', 'StudentController@notifications');
$router->post('/student/notifications/mark-read', 'StudentController@markNotificationRead');
$router->get('/student/notifications/read-all', 'StudentController@markAllNotificationsRead');

// Operator routes
$router->get('/operator/dashboard', 'OperatorController@dashboard');
$router->get('/operator/books', 'OperatorController@books');
$router->get('/operator/borrowings', 'OperatorController@borrowings');
$router->get('/operator/students', 'OperatorController@students');
$router->get('/operator/students/view', 'OperatorController@viewStudent');
$router->get('/operator/students/edit', 'OperatorController@editStudent');
$router->post('/operator/students/edit', 'OperatorController@editStudent');
$router->get('/operator/students/borrowings', 'OperatorController@studentBorrowings');
$router->get('/operator/student-details', 'OperatorController@studentDetails');
$router->get('/operator/process-return', 'OperatorController@processReturn');
$router->get('/operator/process-penalty-payment', 'OperatorController@processPenaltyPayment');
$router->get('/operator/notifications', 'OperatorController@notifications');
$router->get('/operator/notifications/read', 'OperatorController@markNotificationRead');
$router->post('/operator/notifications/mark-read', 'OperatorController@markNotificationRead');
$router->get('/operator/notifications/read-all', 'OperatorController@markAllNotificationsRead');
$router->get('/operator/books/create', 'OperatorController@createBook');
$router->post('/operator/books/create', 'OperatorController@storeBook');
$router->get('/operator/books/edit', 'OperatorController@editBook');
$router->post('/operator/books/edit', 'OperatorController@updateBook');
$router->get('/operator/books/delete', 'OperatorController@deleteBook');
$router->get('/operator/borrowings/view', 'OperatorController@viewBorrowing');

// Admin routes
$router->get('/admin/dashboard', 'AdminController@dashboard');
$router->get('/admin/users', 'AdminController@users');
$router->get('/admin/users/create', 'AdminController@createUser');
$router->post('/admin/users/create', 'AdminController@storeUser');
$router->get('/admin/users/edit', 'AdminController@editUser');
$router->post('/admin/users/edit', 'AdminController@updateUser');
$router->get('/admin/users/delete', 'AdminController@deleteUser');
$router->get('/admin/settings', 'AdminController@settings');
$router->post('/admin/settings', 'AdminController@updateSettings');
$router->get('/admin/audit-logs', 'AdminController@auditLogs');
$router->get('/admin/export-audit-logs', 'AdminController@exportAuditLogs');
$router->post('/admin/clear-audit-logs', 'AdminController@clearAuditLogs');
$router->get('/admin/notifications', 'AdminController@notifications');
$router->get('/admin/notifications/read', 'AdminController@markNotificationRead');
$router->get('/admin/notifications/read-all', 'AdminController@markAllNotificationsRead');

// Book management routes
$router->get('/books', 'BookController@index');
$router->get('/books/show', 'BookController@show');
$router->get('/books/create', 'BookController@create');
$router->post('/books/create', 'BookController@store');
$router->get('/books/edit', 'BookController@edit');
$router->post('/books/edit', 'BookController@update');
$router->get('/books/delete', 'BookController@delete');
$router->get('/books/borrow', 'BookController@borrow');
$router->get('/books/return', 'BookController@return');
$router->get('/books/pay-penalty', 'BookController@payPenalty');

// Notifications route (redirects based on user role)
$router->get('/notifications', 'AuthController@notifications');

// Auth routes
$router->get('/change-password', 'AuthController@showChangePassword');
$router->post('/change-password', 'AuthController@changePassword');

$router->dispatch();
