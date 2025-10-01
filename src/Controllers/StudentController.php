<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Notification;

class StudentController extends Controller
{
    public function dashboard()
    {
        Auth::requireRole('student');
        
        $user = Auth::user();
        $active_borrowings = $user->getActiveBorrowings();
        $overdue_books = $user->getOverdueBooks();
        $total_penalties = $user->getTotalPenalties();
        $notifications = Notification::getByUser($user->id);
        
        // Create notifications for overdue books
        foreach ($overdue_books as $book) {
            $existing_notification = array_filter($notifications, function($n) use ($book) {
                return $n->type === 'overdue' && strpos($n->message, $book['title']) !== false;
            });
            
            if (empty($existing_notification)) {
                $notification = new Notification([
                    'user_id' => $user->id,
                    'title' => 'Overdue Book Reminder',
                    'message' => "Your book '{$book['title']}' is overdue. Please return it as soon as possible to avoid additional penalties.",
                    'type' => 'overdue'
                ]);
                $notification->save();
                $notifications[] = $notification;
            }
        }
        
        $this->view('student/dashboard', [
            'user' => $user,
            'active_borrowings' => $active_borrowings,
            'overdue_books' => $overdue_books,
            'total_penalties' => $total_penalties,
            'notifications' => $notifications
        ]);
    }

    public function books()
    {
        Auth::requireRole('student');
        
        $search = $_GET['search'] ?? '';
        $books = $search ? Book::search($search) : Book::getAll();
        
        $this->view('student/books', [
            'books' => $books,
            'search' => $search
        ]);
    }

    public function borrowings()
    {
        Auth::requireRole('student');
        
        $user = Auth::user();
        $borrowings = $user->getBorrowingHistory();
        
        // Update penalties for overdue books and create notifications
        foreach ($borrowings as $borrowing) {
            if ($borrowing['status'] === 'active' && strtotime($borrowing['due_date']) < time() && !$borrowing['penalty_paid']) {
                $old_penalty = $borrowing['penalty_amount'];
                $days_overdue = floor((time() - strtotime($borrowing['due_date'])) / 86400);
                $new_penalty = $days_overdue * \App\Models\Book::getPenaltyRate();
                
                // Update penalty in database
                $stmt = \App\Core\Database::pdo()->prepare("
                    UPDATE borrowings 
                    SET penalty_amount = ?, updated_at = datetime('now')
                    WHERE id = ?
                ");
                $stmt->execute([$new_penalty, $borrowing['id']]);
                
                // Create notification for new penalty
                if ($new_penalty > $old_penalty) {
                    $notification = new Notification([
                        'user_id' => $user->id,
                        'title' => 'Overdue Book Penalty',
                        'message' => "Your book '{$borrowing['title']}' is overdue. Penalty: €" . number_format($new_penalty, 2),
                        'type' => 'penalty'
                    ]);
                    $notification->save();
                }
            }
        }
        
        $this->view('student/borrowings', [
            'borrowings' => $borrowings
        ]);
    }

    public function penalties()
    {
        Auth::requireRole('student');
        
        $user = Auth::user();
        $borrowings = $user->getBorrowingHistory();
        $penalty_borrowings = array_filter($borrowings, function($b) {
            return $b['penalty_amount'] > 0 && !$b['penalty_paid'];
        });
        
        $this->view('student/penalties', [
            'penalty_borrowings' => $penalty_borrowings,
            'total_penalties' => $user->getTotalPenalties()
        ]);
    }

    public function notifications()
    {
        Auth::requireRole('student');
        
        $user = Auth::user();
        $notifications = Notification::getByUser($user->id);
        
        $this->view('student/notifications', [
            'notifications' => $notifications
        ]);
    }

    public function markNotificationRead()
    {
        Auth::requireRole('student');
        
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
        Auth::requireRole('student');
        
        Notification::markAllAsRead(Auth::user()->id);
        $this->redirect('/student/notifications');
    }
}
