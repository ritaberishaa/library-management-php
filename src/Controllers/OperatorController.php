<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use App\Models\Notification;

class OperatorController extends Controller
{
    public function dashboard()
    {
        Auth::requireRole('operator');
        
        // Get basic stats
        $total_books = count(Book::getAll());
        $active_borrowings = count(array_filter(Borrowing::getAll(), fn($b) => $b->status === 'active'));
        $overdue_books = count(Borrowing::getOverdue());
        $total_students = count(User::getByRole('student'));
        
        // Get recent books (last 5)
        $recent_books = array_slice(Book::getAll(), 0, 5);
        $recent_books_array = array_map(function($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'copies' => $book->copies_total,
                'created_at' => $book->created_at
            ];
        }, $recent_books);
        
        // Get recent borrowings (last 5)
        $recent_borrowings = array_slice(Borrowing::getAll(), 0, 5);
        $recent_borrowings_array = array_map(function($borrowing) {
            $book = $borrowing->getBook();
            $student = $borrowing->getStudent();
            return [
                'id' => $borrowing->id,
                'book_title' => $book ? $book->title : 'Unknown Book',
                'student_name' => $student ? $student->full_name : 'Unknown Student',
                'borrowed_date' => $borrowing->borrow_date,
                'due_date' => $borrowing->due_date
            ];
        }, $recent_borrowings);
        
        // Get overdue books
        $overdue_books_array = array_map(function($borrowing) {
            return [
                'id' => $borrowing->id,
                'title' => $borrowing->title,
                'student_name' => $borrowing->student_name,
                'due_date' => $borrowing->due_date,
                'days_overdue' => $borrowing->getDaysOverdue(),
                'penalty_amount' => $borrowing->penalty_amount ?? 0
            ];
        }, Borrowing::getOverdue());
        
        $this->view('operator/dashboard', [
            'total_books' => $total_books,
            'active_borrowings' => $active_borrowings,
            'overdue_books' => $overdue_books,
            'total_students' => $total_students,
            'recent_books' => $recent_books_array,
            'recent_borrowings' => $recent_borrowings_array,
            'overdue_books_array' => $overdue_books_array
        ]);
    }

    public function books()
    {
        Auth::requireRole('operator');
        
        $search = $_GET['search'] ?? '';
        $bookObjects = $search ? Book::search($search) : Book::getAll();
        
        // Convert Book objects to arrays for the view
        $books = array_map(function($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'copies' => $book->copies_total,
                'available_copies' => $book->copies_available,
                'borrow_fee' => $book->borrow_fee,
                'is_active' => $book->is_active,
                'created_at' => $book->created_at
            ];
        }, $bookObjects);
        
        $this->view('operator/books', [
            'books' => $books,
            'search' => $search
        ]);
    }

    public function borrowings()
    {
        Auth::requireRole('operator');
        
        $status = $_GET['status'] ?? 'all';
        $search = $_GET['search'] ?? '';
        
        if ($status === 'active') {
            $borrowingObjects = array_filter(Borrowing::getAll(), fn($b) => $b->status === 'active');
        } elseif ($status === 'overdue') {
            $borrowingObjects = Borrowing::getOverdue();
        } else {
            $borrowingObjects = Borrowing::getAll();
        }
        
        if ($search) {
            $borrowingObjects = array_filter($borrowingObjects, function($b) use ($search) {
                return stripos($b->title, $search) !== false || 
                       stripos($b->student_name, $search) !== false;
            });
        }
        
        // Convert Borrowing objects to arrays for the view
        $borrowings = array_map(function($borrowing) {
            $book = $borrowing->getBook();
            $student = $borrowing->getStudent();
            return [
                'id' => $borrowing->id,
                'book_title' => $book ? $book->title : 'Unknown Book',
                'student_name' => $student ? $student->full_name : 'Unknown Student',
                'borrowed_date' => $borrowing->borrow_date,
                'due_date' => $borrowing->due_date,
                'status' => $borrowing->status,
                'penalty_amount' => $borrowing->penalty_amount
            ];
        }, $borrowingObjects);
        
        $this->view('operator/borrowings', [
            'borrowings' => $borrowings,
            'status' => $status,
            'search' => $search
        ]);
    }

    public function students()
    {
        Auth::requireRole('operator');
        
        $search = $_GET['search'] ?? '';
        $studentObjects = $search ? 
            array_filter(User::getByRole('student'), fn($s) => 
                stripos($s->full_name, $search) !== false || 
                stripos($s->username, $search) !== false
            ) : 
            User::getByRole('student');
        
        // Convert User objects to arrays for the view
        $students = array_map(function($student) {
            $active_borrowings = $student->getActiveBorrowings();
            $overdue_books = $student->getOverdueBooks();
            $total_penalties = $student->getTotalPenalties();
            
            return [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'email' => $student->email,
                'student_id' => $student->username, // Using username as student_id
                'active_borrowings' => count($active_borrowings),
                'overdue_books' => count($overdue_books),
                'total_penalties' => $total_penalties,
                'is_active' => $student->is_active
            ];
        }, $studentObjects);
        
        $this->view('operator/students', [
            'students' => $students,
            'search' => $search
        ]);
    }

    public function studentDetails()
    {
        Auth::requireRole('operator');
        
        $id = $_GET['id'] ?? 0;
        $student = User::find($id);
        
        if (!$student || !$student->isStudent()) {
            $this->setFlash('error', 'Student not found.');
            $this->redirect('/operator/students');
            return;
        }
        
        $borrowings = $student->getBorrowingHistory();
        $active_borrowings = $student->getActiveBorrowings();
        $overdue_books = $student->getOverdueBooks();
        $total_penalties = $student->getTotalPenalties();
        
        $this->view('operator/student-details', [
            'student' => $student,
            'borrowings' => $borrowings,
            'active_borrowings' => $active_borrowings,
            'overdue_books' => $overdue_books,
            'total_penalties' => $total_penalties
        ]);
    }

    public function viewStudent()
    {
        Auth::requireRole('operator');
        
        $id = $_GET['id'] ?? 0;
        $student = User::find($id);
        
        if (!$student || !$student->isStudent()) {
            $this->setFlash('error', 'Student not found.');
            $this->redirect('/operator/students');
            return;
        }
        
        $borrowings = $student->getBorrowingHistory();
        $active_borrowings = $student->getActiveBorrowings();
        $overdue_books = $student->getOverdueBooks();
        $total_penalties = $student->getTotalPenalties();
        
        $this->view('operator/student-details', [
            'student' => $student,
            'borrowings' => $borrowings,
            'active_borrowings' => $active_borrowings,
            'overdue_books' => $overdue_books,
            'total_penalties' => $total_penalties
        ]);
    }

    public function editStudent()
    {
        Auth::requireRole('operator');
        
        $id = $_GET['id'] ?? 0;
        $student = User::find($id);
        
        if (!$student || !$student->isStudent()) {
            $this->setFlash('error', 'Student not found.');
            $this->redirect('/operator/students');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $this->setFlash('error', 'Invalid request. Please try again.');
                $this->redirect('/operator/students');
                return;
            }
            
            $full_name = $_POST['full_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $student_id = $_POST['student_id'] ?? '';
            $is_active = isset($_POST['is_active']);
            
            if (empty($full_name) || empty($email) || empty($student_id)) {
                $this->setFlash('error', 'All fields are required.');
                $this->redirect('/operator/students/edit?id=' . $id);
                return;
            }
            
            // Check if email is already taken by another user
            $existingUser = User::findByEmail($email);
            if ($existingUser && $existingUser->id != $id) {
                $this->setFlash('error', 'Email is already taken.');
                $this->redirect('/operator/students/edit?id=' . $id);
                return;
            }
            
            // Check if username (student_id) is already taken by another user
            $existingStudent = User::findByUsername($student_id);
            if ($existingStudent && $existingStudent->id != $id) {
                $this->setFlash('error', 'Student ID is already taken.');
                $this->redirect('/operator/students/edit?id=' . $id);
                return;
            }
            
            $student->full_name = $full_name;
            $student->email = $email;
            $student->username = $student_id;
            $student->is_active = $is_active;
            $student->save();
            
            $this->setFlash('success', 'Student updated successfully.');
            $this->redirect('/operator/students');
            return;
        }
        
        $this->view('operator/student-edit', [
            'student' => $student
        ]);
    }

    public function studentBorrowings()
    {
        Auth::requireRole('operator');
        
        $id = $_GET['id'] ?? 0;
        $student = User::find($id);
        
        if (!$student || !$student->isStudent()) {
            $this->setFlash('error', 'Student not found.');
            $this->redirect('/operator/students');
            return;
        }
        
        $borrowings = $student->getBorrowingHistory();
        $active_borrowings = $student->getActiveBorrowings();
        $overdue_books = $student->getOverdueBooks();
        
        $this->view('operator/student-borrowings', [
            'student' => $student,
            'borrowings' => $borrowings,
            'active_borrowings' => $active_borrowings,
            'overdue_books' => $overdue_books
        ]);
    }

    public function processReturn()
    {
        Auth::requireRole('operator');
        
        $id = $_GET['id'] ?? 0;
        $borrowing = Borrowing::find($id);
        
        if (!$borrowing) {
            $this->setFlash('error', 'Borrowing record not found.');
            $this->redirect('/operator/borrowings');
            return;
        }

        if ($borrowing->status !== 'active') {
            $this->setFlash('error', 'This book has already been returned.');
            $this->redirect('/operator/borrowings');
            return;
        }

        $book = $borrowing->getBook();
        
        if ($borrowing->return() && $book->return()) {
            $this->setFlash('success', "Book '{$book->title}' returned successfully.");
            
            // If there's a penalty, notify the student
            if ($borrowing->penalty_amount > 0) {
                $student = $borrowing->getStudent();
                Notification::notifyPenalty($student, $borrowing);
            }
            
            $this->redirect('/operator/borrowings');
        } else {
            $this->setFlash('error', 'Failed to return book. Please try again.');
            $this->redirect('/operator/borrowings');
        }
    }

    public function processPenaltyPayment()
    {
        Auth::requireRole('operator');
        
        $id = $_GET['id'] ?? 0;
        $borrowing = Borrowing::find($id);
        
        if (!$borrowing) {
            $this->setFlash('error', 'Borrowing record not found.');
            $this->redirect('/operator/borrowings');
            return;
        }

        if ($borrowing->penalty_paid) {
            $this->setFlash('error', 'Penalty has already been paid.');
            $this->redirect('/operator/borrowings');
            return;
        }

        if ($borrowing->payPenalty()) {
            $this->setFlash('success', 'Penalty payment processed successfully.');
        } else {
            $this->setFlash('error', 'Failed to process penalty payment. Please try again.');
        }
        
        $this->redirect('/operator/borrowings');
    }

    public function notifications()
    {
        Auth::requireRole('operator');
        
        $user = Auth::user();
        $notifications = Notification::getByUser($user->id);
        
        $this->view('operator/notifications', [
            'notifications' => $notifications
        ]);
    }

    public function markNotificationRead()
    {
        Auth::requireRole('operator');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        $notification = Notification::find($id);
        
        if ($notification && $notification->user_id === Auth::user()->id) {
            $notification->markAsRead();
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function markAllNotificationsRead()
    {
        Auth::requireRole('operator');
        
        Notification::markAllAsRead(Auth::user()->id);
        $this->redirect('/operator/notifications');
    }

    public function createBook()
    {
        Auth::requireRole('operator');
        $this->view('books/create');
    }

    public function storeBook()
    {
        Auth::requireRole('operator');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/operator/books/create');
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
            $this->redirect('/operator/books/create');
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
            $this->redirect('/operator/books');
        } else {
            $this->setFlash('error', 'Failed to add book. Please try again.');
            $this->redirect('/operator/books/create');
        }
    }

    public function deleteBook()
    {
        Auth::requireRole('operator');
        
        $id = $_GET['id'] ?? 0;
        $book = Book::find($id);
        
        if (!$book) {
            $this->setFlash('error', 'Book not found.');
            $this->redirect('/operator/books');
            return;
        }
        
        // Check if book has active borrowings
        $active_borrowings = Borrowing::getActiveByBook($id);
        if (!empty($active_borrowings)) {
            $this->setFlash('error', 'Cannot delete book with active borrowings.');
            $this->redirect('/operator/books');
            return;
        }
        
        if ($book->delete()) {
            $this->setFlash('success', "Book '{$book->title}' deleted successfully.");
        } else {
            $this->setFlash('error', 'Failed to delete book. Please try again.');
        }
        
        $this->redirect('/operator/books');
    }

    public function editBook()
    {
        Auth::requireRole('operator');
        
        $id = $_GET['id'] ?? 0;
        $book = Book::find($id);
        
        if (!$book) {
            $this->setFlash('error', 'Book not found.');
            $this->redirect('/operator/books');
            return;
        }
        
        // Convert Book object to array for the view
        $bookData = [
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
        
        $this->view('books/edit', [
            'book' => $bookData
        ]);
    }

    public function updateBook()
    {
        Auth::requireRole('operator');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/operator/books');
            return;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $book = Book::find($id);
        
        if (!$book) {
            $this->setFlash('error', 'Book not found.');
            $this->redirect('/operator/books');
            return;
        }
        
        $book->title = $_POST['title'] ?? $book->title;
        $book->author = $_POST['author'] ?? $book->author;
        $book->isbn = $_POST['isbn'] ?: null;
        $book->published_year = $_POST['published_year'] ? (int)$_POST['published_year'] : null;
        $book->description = $_POST['description'] ?: null;
        $old_total = $book->copies_total;
        $new_total = (int)($_POST['copies_total'] ?? $book->copies_total);
        $book->copies_total = $new_total;
        
        $book->borrow_fee = (float)($_POST['borrow_fee'] ?? $book->borrow_fee);
        $book->is_active = (bool)($_POST['is_active'] ?? $book->is_active);
        
        // Update the book - database triggers will automatically handle availability calculation
        $stmt = Database::pdo()->prepare("
            UPDATE books SET 
                title = ?, author = ?, isbn = ?, published_year = ?, 
                description = ?, copies_total = ?, 
                borrow_fee = ?, is_active = ?, updated_at = datetime('now')
            WHERE id = ?
        ");
        $result = $stmt->execute([
            $book->title,
            $book->author,
            $book->isbn,
            $book->published_year,
            $book->description,
            $book->copies_total,
            $book->borrow_fee,
            $book->is_active ? 1 : 0,
            $book->id
        ]);
        
        if ($result) {
            $this->setFlash('success', "Book '{$book->title}' updated successfully.");
            $this->redirect('/operator/books');
        } else {
            $this->setFlash('error', 'Failed to update book. Please try again.');
            $this->redirect('/operator/books/edit?id=' . $book->id);
        }
    }

    public function viewBorrowing()
    {
        Auth::requireRole('operator');
        
        $id = $_GET['id'] ?? 0;
        $borrowing = Borrowing::find($id);
        
        if (!$borrowing) {
            $this->setFlash('error', 'Borrowing record not found.');
            $this->redirect('/operator/borrowings');
            return;
        }
        
        // Get additional details
        $book = Book::find($borrowing->book_id);
        $student = User::find($borrowing->student_id);
        
        $this->view('operator/borrowing-details', [
            'borrowing' => $borrowing,
            'book' => $book,
            'student' => $student
        ]);
    }
}
