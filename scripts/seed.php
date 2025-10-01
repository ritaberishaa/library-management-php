<?php
require __DIR__ . '/../vendor/autoload.php';

// Load core files
require __DIR__ . '/../src/Core/Database.php';
require __DIR__ . '/../src/Models/User.php';
require __DIR__ . '/../src/Models/Book.php';

use App\Core\Database;
use App\Models\User;
use App\Models\Book;

// Initialize database
Database::init([
    'driver' => 'sqlite',
    'sqlite' => __DIR__ . '/../database.sqlite'
]);

echo "🌱 Seeding database with sample data...\n";

// Create sample users
$users = [
    [
        'username' => 'operator',
        'email' => 'operator@library.com',
        'password' => 'operator123',
        'role' => 'operator',
        'full_name' => 'Library Operator',
        'phone' => '+1234567890'
    ],
    [
        'username' => 'student',
        'email' => 'student@library.com',
        'password' => 'student123',
        'role' => 'student',
        'full_name' => 'John Student',
        'phone' => '+1234567891'
    ],
    [
        'username' => 'student2',
        'email' => 'student2@library.com',
        'password' => 'student123',
        'role' => 'student',
        'full_name' => 'Jane Student',
        'phone' => '+1234567892'
    ]
];

foreach ($users as $userData) {
    $user = new User();
    $user->username = $userData['username'];
    $user->email = $userData['email'];
    $user->setPasswordHash(password_hash($userData['password'], PASSWORD_DEFAULT));
    $user->role = $userData['role'];
    $user->full_name = $userData['full_name'];
    $user->phone = $userData['phone'];
    $user->is_active = true;
    
    if ($user->save()) {
        echo "✅ Created user: {$user->username}\n";
    } else {
        echo "❌ Failed to create user: {$user->username}\n";
    }
}

// Create sample books
$books = [
    [
        'title' => 'The Great Gatsby',
        'author' => 'F. Scott Fitzgerald',
        'isbn' => '9780743273565',
        'published_year' => 1925,
        'description' => 'A classic American novel about the Jazz Age.',
        'copies_total' => 3,
        'copies_available' => 3,
        'borrow_fee' => 1.00
    ],
    [
        'title' => 'To Kill a Mockingbird',
        'author' => 'Harper Lee',
        'isbn' => '9780061120084',
        'published_year' => 1960,
        'description' => 'A novel about racial injustice and childhood innocence.',
        'copies_total' => 2,
        'copies_available' => 2,
        'borrow_fee' => 1.00
    ],
    [
        'title' => '1984',
        'author' => 'George Orwell',
        'isbn' => '9780451524935',
        'published_year' => 1949,
        'description' => 'A dystopian social science fiction novel.',
        'copies_total' => 4,
        'copies_available' => 4,
        'borrow_fee' => 1.00
    ],
    [
        'title' => 'Pride and Prejudice',
        'author' => 'Jane Austen',
        'isbn' => '9780141439518',
        'published_year' => 1813,
        'description' => 'A romantic novel of manners.',
        'copies_total' => 2,
        'copies_available' => 2,
        'borrow_fee' => 1.00
    ],
    [
        'title' => 'The Catcher in the Rye',
        'author' => 'J.D. Salinger',
        'isbn' => '9780316769174',
        'published_year' => 1951,
        'description' => 'A coming-of-age story about teenage rebellion.',
        'copies_total' => 3,
        'copies_available' => 3,
        'borrow_fee' => 1.00
    ],
    [
        'title' => 'Lord of the Flies',
        'author' => 'William Golding',
        'isbn' => '9780571056866',
        'published_year' => 1954,
        'description' => 'A novel about British boys stranded on an uninhabited island.',
        'copies_total' => 2,
        'copies_available' => 2,
        'borrow_fee' => 1.00
    ],
    [
        'title' => 'The Hobbit',
        'author' => 'J.R.R. Tolkien',
        'isbn' => '9780547928227',
        'published_year' => 1937,
        'description' => 'A fantasy novel about a hobbit\'s unexpected journey.',
        'copies_total' => 3,
        'copies_available' => 3,
        'borrow_fee' => 1.00
    ],
    [
        'title' => 'Animal Farm',
        'author' => 'George Orwell',
        'isbn' => '9780451526342',
        'published_year' => 1945,
        'description' => 'An allegorical novella about farm animals who rebel.',
        'copies_total' => 2,
        'copies_available' => 2,
        'borrow_fee' => 1.00
    ]
];

foreach ($books as $bookData) {
    $book = new Book();
    $book->title = $bookData['title'];
    $book->author = $bookData['author'];
    $book->isbn = $bookData['isbn'];
    $book->published_year = $bookData['published_year'];
    $book->description = $bookData['description'];
    $book->copies_total = $bookData['copies_total'];
    $book->copies_available = $bookData['copies_available'];
    $book->borrow_fee = $bookData['borrow_fee'];
    $book->is_active = true;
    
    if ($book->save()) {
        echo "✅ Created book: {$book->title}\n";
    } else {
        echo "❌ Failed to create book: {$book->title}\n";
    }
}

echo "\n🎉 Database seeding completed!\n";
echo "\nDemo accounts created:\n";
echo "- Super Admin: admin / admin123\n";
echo "- Operator: operator / operator123\n";
echo "- Student: student / student123\n";
echo "- Student 2: student2 / student123\n";
echo "\nYou can now login and test the system!\n";
