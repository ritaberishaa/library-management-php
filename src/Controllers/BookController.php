<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;

class BookController extends Controller
{
    public function index()
    {
        $search = $_GET['search'] ?? '';
        $bookObjects = $search ? Book::search($search) : Book::getAll();
        
        // Convert Book objects to arrays for the view
        $books = array_map(function($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'published_year' => $book->published_year,
                'description' => $book->description,
                'copies_total' => $book->copies_total,
                'copies_available' => $book->copies_available,
                'borrow_fee' => $book->borrow_fee,
                'is_active' => $book->is_active,
                'created_at' => $book->created_at
            ];
        }, $bookObjects);
        
        // Check user role and redirect to appropriate view
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'student') {
                $this->redirect('/student/books');
            } elseif ($user->role === 'operator') {
                $this->redirect('/operator/books');
            } elseif ($user->role === 'admin') {
                $this->redirect('/admin/dashboard');
            }
        }
        
        $this->view('books/index', [
            'books' => $books,
            'search' => $search
        ]);
    }

    public function show()
    {
        Auth::requireAuth();
        
        $id = $_GET['id'] ?? 0;
        $bookObject = Book::find($id);
        
        if (!$bookObject) {
            $this->setFlash('error', 'Book not found.');
            $this->redirect('/books');
            return;
        }
        
        // Convert Book object to array for the view
        $book = [
            'id' => $bookObject->id,
            'title' => $bookObject->title,
            'author' => $bookObject->author,
            'isbn' => $bookObject->isbn,
            'published_year' => $bookObject->published_year,
            'description' => $bookObject->description,
            'copies_total' => $bookObject->copies_total,
            'copies_available' => $bookObject->copies_available,
            'borrow_fee' => $bookObject->borrow_fee,
            'is_active' => $bookObject->is_active,
            'created_at' => $bookObject->created_at
        ];
        
        // Get recent borrowings for this book
        $recent_borrowings = [];
        if (Auth::canManageBooks()) {
            $borrowingObjects = Borrowing::getByBook($id);
            $recent_borrowings = array_map(function($borrowing) {
                return [
                    'id' => $borrowing->id,
                    'student_name' => $borrowing->student_name,
                    'borrowed_date' => $borrowing->borrow_date,
                    'due_date' => $borrowing->due_date,
                    'status' => $borrowing->status
                ];
            }, array_slice($borrowingObjects, 0, 5));
        }
        
        $this->view('books/show', [
            'book' => $book,
            'recent_borrowings' => $recent_borrowings
        ]);
    }

    public function create()
    {
        Auth::requireAuth();
        
        $user = Auth::user();
        if (!in_array($user->role, ['operator', 'super_admin'])) {
            header('Location: /unauthorized');
            exit;
        }
        $this->view('books/create');
    }

    public function store()
    {
        Auth::requireAuth();
        
        $user = Auth::user();
        if (!in_array($user->role, ['operator', 'super_admin'])) {
            header('Location: /unauthorized');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/books/create');
            return;
        }
        
        $title = $_POST['title'] ?? '';
        $author = $_POST['author'] ?? '';
        $isbn = $_POST['isbn'] ?? null;
        $published_year = $_POST['published_year'] ? (int)$_POST['published_year'] : null;
        $description = $_POST['description'] ?? null;
        $copies_total = (int)($_POST['copies_total'] ?? 1);
        $borrow_fee = (float)($_POST['borrow_fee'] ?? Book::getDefaultBorrowFee());
        $is_active = (bool)($_POST['is_active'] ?? true);
        
        if (empty($title) || empty($author)) {
            $this->setFlash('error', 'Title and author are required.');
            $this->redirect('/books/create');
            return;
        }
        
        $book = new Book([
            'title' => $title,
            'author' => $author,
            'isbn' => $isbn,
            'published_year' => $published_year,
            'description' => $description,
            'copies_total' => $copies_total,
            'copies_available' => $copies_total,
            'borrow_fee' => $borrow_fee,
            'is_active' => $is_active
        ]);
        
        if ($book->save()) {
            $this->setFlash('success', "Book '{$title}' added successfully.");
            $this->redirect('/books');
        } else {
            $this->setFlash('error', 'Failed to add book. Please try again.');
            $this->redirect('/books/create');
        }
    }

    public function edit()
    {
        Auth::requireAuth();
        
        $user = Auth::user();
        if (!in_array($user->role, ['operator', 'super_admin'])) {
            header('Location: /unauthorized');
            exit;
        }
        
        $id = $_GET['id'] ?? 0;
        $bookObject = Book::find($id);
        
        if (!$bookObject) {
            $this->setFlash('error', 'Book not found.');
            $this->redirect('/books');
            return;
        }
        
        // Convert Book object to array for the view
        $book = [
            'id' => $bookObject->id,
            'title' => $bookObject->title,
            'author' => $bookObject->author,
            'isbn' => $bookObject->isbn,
            'published_year' => $bookObject->published_year,
            'description' => $bookObject->description,
            'copies_total' => $bookObject->copies_total,
            'copies_available' => $bookObject->copies_available,
            'borrow_fee' => $bookObject->borrow_fee,
            'is_active' => $bookObject->is_active,
            'created_at' => $bookObject->created_at
        ];
        
        $this->view('books/edit', [
            'book' => $book
        ]);
    }

    public function update()
    {
        Auth::requireAuth();
        
        $user = Auth::user();
        if (!in_array($user->role, ['operator', 'super_admin'])) {
            header('Location: /unauthorized');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/books');
            return;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $book = Book::find($id);
        
        if (!$book) {
            $this->setFlash('error', 'Book not found.');
            $this->redirect('/books');
            return;
        }
        
        $book->title = $_POST['title'] ?? $book->title;
        $book->author = $_POST['author'] ?? $book->author;
        $book->isbn = $_POST['isbn'] ?: null;
        $book->published_year = $_POST['published_year'] ? (int)$_POST['published_year'] : null;
        $book->description = $_POST['description'] ?: null;
        $book->copies_total = (int)($_POST['copies_total'] ?? $book->copies_total);
        $book->copies_available = (int)($_POST['copies_available'] ?? $book->copies_available);
        $book->borrow_fee = (float)($_POST['borrow_fee'] ?? $book->borrow_fee);
        $book->is_active = (bool)($_POST['is_active'] ?? $book->is_active);
        
        // Ensure available copies don't exceed total copies
        if ($book->copies_available > $book->copies_total) {
            $book->copies_available = $book->copies_total;
        }
        
        if ($book->save()) {
            $this->setFlash('success', "Book '{$book->title}' updated successfully.");
            $this->redirect('/books/show?id=' . $book->id);
        } else {
            $this->setFlash('error', 'Failed to update book. Please try again.');
            $this->redirect('/books/edit?id=' . $book->id);
        }
    }

    public function delete()
    {
        Auth::requireAuth();
        
        $user = Auth::user();
        if (!in_array($user->role, ['operator', 'super_admin'])) {
            header('Location: /unauthorized');
            exit;
        }
        
        $id = $_GET['id'] ?? 0;
        $book = Book::find($id);
        
        if (!$book) {
            $this->setFlash('error', 'Book not found.');
            $this->redirect('/books');
            return;
        }
        
        // Check if book has active borrowings
        $active_borrowings = Borrowing::getActiveByBook($id);
        if (!empty($active_borrowings)) {
            $this->setFlash('error', 'Cannot delete book with active borrowings.');
            $this->redirect('/books');
            return;
        }
        
        if ($book->delete()) {
            $this->setFlash('success', "Book '{$book->title}' deleted successfully.");
        } else {
            $this->setFlash('error', 'Failed to delete book. Please try again.');
        }
        
        $this->redirect('/books');
    }

    public function borrow()
    {
        Auth::requireRole('student');
        
        $id = $_GET['id'] ?? 0;
        $book = Book::find($id);
        
        if (!$book) {
            $this->setFlash('error', 'Book not found.');
            $this->redirect('/books');
            return;
        }
        
        if (!$book->canBorrow()) {
            $this->setFlash('error', 'Book is not available for borrowing.');
            $this->redirect('/books/show?id=' . $id);
            return;
        }
        
        $student = Auth::user();
        
        // Check if student already has this book borrowed
        $existing_borrowings = Borrowing::getActiveByBook($book->id);
        foreach ($existing_borrowings as $borrowing) {
            if ($borrowing->student_id == $student->id) {
                $this->setFlash('error', 'You have already borrowed this book.');
                $this->redirect('/books/show?id=' . $id);
                return;
            }
        }
        
        // Check borrowing limit
        $active_borrowings = $student->getActiveBorrowings();
        $borrowing_limit = $this->getBorrowingLimit();
        
        if (count($active_borrowings) >= $borrowing_limit) {
            $this->setFlash('error', "You have reached the maximum borrowing limit of {$borrowing_limit} books.");
            $this->redirect('/books/show?id=' . $id);
            return;
        }
        
        // Create borrowing record
        $borrowing_duration = $this->getBorrowingDuration();
        $borrowing = new Borrowing([
            'book_id' => $book->id,
            'student_id' => $student->id,
            'borrow_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime("+{$borrowing_duration} days")),
            'borrow_fee' => $book->borrow_fee,
            'status' => 'active'
        ]);
        
        if ($borrowing->save()) {
            if ($book->borrow()) {
                $this->setFlash('success', "Book '{$book->title}' borrowed successfully. Due date: " . date('M d, Y', strtotime($borrowing->due_date)));
                $this->redirect('/student/borrowings');
            } else {
                // Rollback borrowing record if book borrow fails
                $borrowing->delete();
                $this->setFlash('error', 'Failed to borrow book. Please try again.');
                $this->redirect('/books/show?id=' . $id);
            }
        } else {
            $this->setFlash('error', 'Failed to create borrowing record. Please try again.');
            $this->redirect('/books/show?id=' . $id);
        }
    }
    
    private function getBorrowingLimit(): int
    {
        $stmt = \App\Core\Database::pdo()->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'borrowing_limit'");
        $stmt->execute();
        return (int) $stmt->fetchColumn() ?: 3;
    }
    
    private function getBorrowingDuration(): int
    {
        $stmt = \App\Core\Database::pdo()->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'borrowing_duration'");
        $stmt->execute();
        return (int) $stmt->fetchColumn() ?: 14;
    }

    public function return()
    {
        Auth::requireAuth();
        
        $user = Auth::user();
        if (!in_array($user->role, ['student', 'operator'])) {
            header('Location: /unauthorized');
            exit;
        }
        
        $id = $_GET['id'] ?? 0;
        $borrowing = Borrowing::find($id);
        
        if (!$borrowing) {
            $this->setFlash('error', 'Borrowing record not found.');
            $this->redirect('/books');
            return;
        }
        
        if ($borrowing->status !== 'active') {
            $this->setFlash('error', 'This book has already been returned.');
            $this->redirect('/books');
            return;
        }
        
        // Check if student is returning their own book (for students)
        if (Auth::isStudent() && $borrowing->student_id !== Auth::user()->id) {
            $this->setFlash('error', 'You can only return your own books.');
            $this->redirect('/student/borrowings');
            return;
        }
        
        $book = $borrowing->getBook();
        
        if ($borrowing->return() && $book->return()) {
            // Check for low stock and create notification for operators
            if ($book->copies_available <= Book::getLowStockThreshold()) {
                \App\Models\Notification::createForAllStudents(
                    'Low Stock Alert',
                    "Book '{$book->title}' is running low on stock. Only {$book->copies_available} copies available.",
                    'low_stock'
                );
            }
            
            $this->setFlash('success', "Book '{$book->title}' returned successfully.");
            
            if (Auth::isStudent()) {
                $this->redirect('/student/borrowings');
            } else {
                $this->redirect('/operator/borrowings');
            }
        } else {
            $this->setFlash('error', 'Failed to return book. Please try again.');
            $this->redirect('/books');
        }
    }

    public function payPenalty()
    {
        Auth::requireAuth();
        
        $user = Auth::user();
        if (!in_array($user->role, ['student', 'operator'])) {
            header('Location: /unauthorized');
            exit;
        }
        
        $id = $_GET['id'] ?? 0;
        $borrowing = Borrowing::find($id);
        
        if (!$borrowing) {
            $this->setFlash('error', 'Borrowing record not found.');
            $this->redirect('/books');
            return;
        }
        
        if ($borrowing->penalty_paid) {
            $this->setFlash('error', 'Penalty has already been paid.');
            $this->redirect('/books');
            return;
        }
        
        // Check if student is paying their own penalty (for students)
        if (Auth::isStudent() && $borrowing->student_id !== Auth::user()->id) {
            $this->setFlash('error', 'You can only pay penalties for your own books.');
            $this->redirect('/student/penalties');
            return;
        }
        
        if ($borrowing->payPenalty()) {
            $this->setFlash('success', 'Penalty payment processed successfully.');
            
            if (Auth::isStudent()) {
                $this->redirect('/student/penalties');
            } else {
                $this->redirect('/operator/borrowings');
            }
        } else {
            $this->setFlash('error', 'Failed to process penalty payment. Please try again.');
            $this->redirect('/books');
        }
    }
}