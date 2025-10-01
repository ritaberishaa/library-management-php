<?php
/**
 * Library Management System Setup Script
 * Run this script to initialize the database and create default data
 */

require __DIR__ . '/vendor/autoload.php';

// Load required files
require __DIR__ . '/src/Core/Database.php';
require __DIR__ . '/src/Models/User.php';
require __DIR__ . '/src/Models/Book.php';
require __DIR__ . '/src/Models/Borrowing.php';
require __DIR__ . '/src/Models/Notification.php';
require __DIR__ . '/src/Models/AuditLog.php';

use App\Core\Database;
use App\Models\User;
use App\Models\Book;
use App\Models\Notification;

echo "🚀 Setting up Library Management System...\n\n";

// Initialize database
Database::init([
    'driver' => 'sqlite',
    'sqlite' => __DIR__ . '/database.sqlite'
]);

echo "✅ Database initialized\n";

// Create database schema
$schema = file_get_contents(__DIR__ . '/sql/sqlite_schema.sql');
$statements = explode(';', $schema);

foreach ($statements as $statement) {
    $statement = trim($statement);
    if (!empty($statement)) {
        Database::pdo()->exec($statement);
    }
}

echo "✅ Database schema created\n";

// Create default admin user
$admin = new User([
    'username' => 'admin',
    'email' => 'admin@library.com',
    'role' => 'super_admin',
    'full_name' => 'System Administrator',
    'is_active' => true
]);
$admin->setPasswordHash(password_hash('admin123', PASSWORD_DEFAULT));
$admin->save();

echo "✅ Default admin user created (username: admin, password: admin123)\n";

// Create sample books
$sample_books = [
    [
        'title' => 'Introduction to PHP',
        'author' => 'John Smith',
        'isbn' => '978-1234567890',
        'published_year' => 2023,
        'description' => 'A comprehensive guide to PHP programming',
        'copies_total' => 5,
        'copies_available' => 5,
        'borrow_fee' => 1.00
    ],
    [
        'title' => 'Web Development with HTML5',
        'author' => 'Jane Doe',
        'isbn' => '978-0987654321',
        'published_year' => 2022,
        'description' => 'Modern web development techniques',
        'copies_total' => 3,
        'copies_available' => 3,
        'borrow_fee' => 1.50
    ],
    [
        'title' => 'Database Design Fundamentals',
        'author' => 'Mike Johnson',
        'isbn' => '978-1122334455',
        'published_year' => 2023,
        'description' => 'Essential database design principles',
        'copies_total' => 4,
        'copies_available' => 4,
        'borrow_fee' => 2.00
    ]
];

foreach ($sample_books as $book_data) {
    $book = new Book($book_data);
    $book->save();
}

echo "✅ Sample books created\n";

// Create welcome notification for admin
$notification = new Notification([
    'user_id' => $admin->id,
    'title' => 'Welcome to Library Management System',
    'message' => 'Your library management system is now ready to use!',
    'type' => 'system'
]);
$notification->save();

echo "✅ Welcome notification created\n";

echo "\n🎉 Setup completed successfully!\n\n";
echo "📋 Next steps:\n";
echo "1. Start your web server\n";
echo "2. Navigate to: http://localhost/library-management-php/public/\n";
echo "3. Login with: admin / admin123\n";
echo "4. Start managing your library!\n\n";
echo "📚 Features available:\n";
echo "- User management (Students, Operators, Admins)\n";
echo "- Book inventory management\n";
echo "- Borrowing system with due dates\n";
echo "- Penalty calculation\n";
echo "- Audit logging\n";
echo "- Notification system\n";
echo "- System settings\n\n";
echo "Happy managing! 📖\n";
