<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Borrowing
{
    public int $id;
    public int $book_id;
    public int $student_id;
    public string $borrow_date;
    public string $due_date;
    public ?string $return_date;
    public float $borrow_fee;
    public float $penalty_amount;
    public bool $penalty_paid;
    public string $status;
    public string $created_at;
    public string $updated_at;
    
    // Additional fields from JOIN queries
    public ?string $title = null;
    public ?string $author = null;
    public ?string $student_name = null;
    public ?string $email = null;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = (int) ($data['id'] ?? 0);
            $this->book_id = (int) $data['book_id'];
            $this->student_id = (int) $data['student_id'];
            $this->borrow_date = $data['borrow_date'];
            $this->due_date = $data['due_date'];
            $this->return_date = $data['return_date'] ?? null;
            $this->borrow_fee = (float) ($data['borrow_fee'] ?? 0);
            $this->penalty_amount = (float) ($data['penalty_amount'] ?? 0);
            $this->penalty_paid = (bool) ($data['penalty_paid'] ?? false);
            $this->status = $data['status'] ?? 'active';
            $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
            $this->updated_at = $data['updated_at'] ?? date('Y-m-d H:i:s');
            
            // Additional fields from JOIN queries
            $this->title = $data['title'] ?? null;
            $this->author = $data['author'] ?? null;
            $this->student_name = $data['student_name'] ?? null;
            $this->email = $data['email'] ?? null;
        }
    }

    public static function find(int $id): ?Borrowing
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM borrowings WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? new Borrowing($data) : null;
    }

    public static function create(int $book_id, int $student_id, float $borrow_fee): ?Borrowing
    {
        $due_date = date('Y-m-d H:i:s', strtotime('+' . Book::getBorrowingDuration() . ' days'));
        
        $stmt = Database::pdo()->prepare("
            INSERT INTO borrowings (book_id, student_id, borrow_date, due_date, borrow_fee, status) 
            VALUES (?, ?, datetime('now'), ?, ?, 'active')
        ");
        
        if ($stmt->execute([$book_id, $student_id, $due_date, $borrow_fee])) {
            $id = Database::pdo()->lastInsertId();
            return self::find($id);
        }
        
        return null;
    }

    public function return(): bool
    {
        $this->return_date = date('Y-m-d H:i:s');
        $this->status = 'returned';
        
        // Calculate penalty if overdue
        if ($this->isOverdue()) {
            $this->penalty_amount = $this->calculatePenalty();
        }

        $stmt = Database::pdo()->prepare("
            UPDATE borrowings 
            SET return_date = ?, status = ?, penalty_amount = ?, updated_at = datetime('now')
            WHERE id = ?
        ");
        
        return $stmt->execute([$this->return_date, $this->status, $this->penalty_amount, $this->id]);
    }

    public function isOverdue(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        
        $current_date = new \DateTime();
        $due_date = new \DateTime($this->due_date);
        
        return $current_date > $due_date;
    }

    public function calculatePenalty(): float
    {
        if (!$this->isOverdue()) {
            return 0.0;
        }

        $current_date = new \DateTime();
        $due_date = new \DateTime($this->due_date);
        $days_overdue = $current_date->diff($due_date)->days;
        
        if ($current_date > $due_date) {
            return $days_overdue * Book::getPenaltyRate();
        }
        
        return 0.0;
    }
    
    public function updatePenalty(): bool
    {
        if ($this->isOverdue() && !$this->penalty_paid) {
            $this->penalty_amount = $this->calculatePenalty();
            return $this->save();
        }
        return true;
    }

    public function payPenalty(): bool
    {
        $stmt = Database::pdo()->prepare("
            UPDATE borrowings 
            SET penalty_paid = 1, updated_at = datetime('now')
            WHERE id = ?
        ");
        return $stmt->execute([$this->id]);
    }

    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return floor((time() - strtotime($this->due_date)) / 86400);
    }

    public function getBook(): ?Book
    {
        return Book::find($this->book_id);
    }

    public function getStudent(): ?User
    {
        return User::find($this->student_id);
    }

    public static function getActiveByStudent(int $student_id): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT b.*, bk.title, bk.author 
            FROM borrowings b 
            JOIN books bk ON b.book_id = bk.id 
            WHERE b.student_id = ? AND b.status = 'active'
            ORDER BY b.due_date ASC
        ");
        $stmt->execute([$student_id]);
        return array_map(fn($data) => new Borrowing($data), $stmt->fetchAll());
    }

    public static function getOverdue(): array
    {
        $stmt = Database::pdo()->query("
            SELECT b.*, bk.title, bk.author, u.full_name as student_name, u.email
            FROM borrowings b 
            JOIN books bk ON b.book_id = bk.id 
            JOIN users u ON b.student_id = u.id 
            WHERE b.status = 'active' AND b.due_date < datetime('now')
            ORDER BY b.due_date ASC
        ");
        return array_map(fn($data) => new Borrowing($data), $stmt->fetchAll());
    }

    public static function getAll(): array
    {
        $stmt = Database::pdo()->query("
            SELECT b.*, bk.title, bk.author, u.full_name as student_name
            FROM borrowings b 
            JOIN books bk ON b.book_id = bk.id 
            JOIN users u ON b.student_id = u.id 
            ORDER BY b.created_at DESC
        ");
        return array_map(fn($data) => new Borrowing($data), $stmt->fetchAll());
    }

    public static function getByBook(int $book_id): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT b.*, bk.title, bk.author, u.full_name as student_name
            FROM borrowings b 
            JOIN books bk ON b.book_id = bk.id 
            JOIN users u ON b.student_id = u.id 
            WHERE b.book_id = ?
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$book_id]);
        return array_map(fn($data) => new Borrowing($data), $stmt->fetchAll());
    }

    public static function getActiveByBook(int $book_id): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT b.*, bk.title, bk.author, u.full_name as student_name
            FROM borrowings b 
            JOIN books bk ON b.book_id = bk.id 
            JOIN users u ON b.student_id = u.id 
            WHERE b.book_id = ? AND b.status = 'active'
        ");
        $stmt->execute([$book_id]);
        return array_map(fn($data) => new Borrowing($data), $stmt->fetchAll());
    }


    public static function getByStudent(int $student_id): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT b.*, bk.title, bk.author 
            FROM borrowings b 
            JOIN books bk ON b.book_id = bk.id 
            WHERE b.student_id = ?
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$student_id]);
        return array_map(fn($data) => new Borrowing($data), $stmt->fetchAll());
    }

    public static function getStats(): array
    {
        $stats = [];
        
        // Total borrowings
        $stmt = Database::pdo()->query("SELECT COUNT(*) FROM borrowings");
        $stats['total_borrowings'] = $stmt->fetchColumn();
        
        // Active borrowings
        $stmt = Database::pdo()->query("SELECT COUNT(*) FROM borrowings WHERE status = 'active'");
        $stats['active_borrowings'] = $stmt->fetchColumn();
        
        // Overdue borrowings
        $stmt = Database::pdo()->query("SELECT COUNT(*) FROM borrowings WHERE status = 'active' AND due_date < datetime('now')");
        $stats['overdue_borrowings'] = $stmt->fetchColumn();
        
        // Total penalties
        $stmt = Database::pdo()->query("SELECT COALESCE(SUM(penalty_amount), 0) FROM borrowings WHERE penalty_paid = 0");
        $stats['total_unpaid_penalties'] = $stmt->fetchColumn();
        
        return $stats;
    }

    public function save(): bool
    {
        if (isset($this->id) && $this->id > 0) {
            return $this->update();
        } else {
            return $this->createRecord();
        }
    }

    private function createRecord(): bool
    {
        try {
            $stmt = Database::pdo()->prepare("
                INSERT INTO borrowings (book_id, student_id, borrow_date, due_date, borrow_fee, penalty_amount, penalty_paid, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $result = $stmt->execute([
                $this->book_id,
                $this->student_id,
                $this->borrow_date,
                $this->due_date,
                $this->borrow_fee,
                $this->penalty_amount ?? 0,
                $this->penalty_paid ? 1 : 0,
                $this->status
            ]);
            
            if ($result) {
                $this->id = (int) Database::pdo()->lastInsertId();
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function update(): bool
    {
        $stmt = Database::pdo()->prepare("
            UPDATE borrowings SET 
                book_id = ?, student_id = ?, borrow_date = ?, due_date = ?, 
                return_date = ?, borrow_fee = ?, penalty_amount = ?, 
                penalty_paid = ?, status = ?, updated_at = datetime('now')
            WHERE id = ?
        ");
        return $stmt->execute([
            $this->book_id,
            $this->student_id,
            $this->borrow_date,
            $this->due_date,
            $this->return_date,
            $this->borrow_fee,
            $this->penalty_amount,
            $this->penalty_paid ? 1 : 0,
            $this->status,
            $this->id
        ]);
    }

}
