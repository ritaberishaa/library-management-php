<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Book
{
    public int $id;
    public string $title;
    public string $author;
    public ?string $isbn;
    public ?int $published_year;
    public ?string $description;
    public int $copies_total;
    public int $copies_available;
    public float $borrow_fee;
    public bool $is_active;
    public string $created_at;
    public string $updated_at;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = isset($data['id']) ? (int) $data['id'] : 0;
            $this->title = $data['title'];
            $this->author = $data['author'];
            $this->isbn = $data['isbn'] ?? null;
            $this->published_year = $data['published_year'] ? (int) $data['published_year'] : null;
            $this->description = $data['description'] ?? null;
            $this->copies_total = (int) $data['copies_total'];
            $this->copies_available = (int) $data['copies_available'];
            $this->borrow_fee = (float) $data['borrow_fee'];
            $this->is_active = (bool) ($data['is_active'] ?? true);
            $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
            $this->updated_at = $data['updated_at'] ?? date('Y-m-d H:i:s');
        }
    }

    public static function find(int $id): ?Book
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? new Book($data) : null;
    }

    public static function getAll(): array
    {
        $stmt = Database::pdo()->query("SELECT * FROM books WHERE is_active = 1 ORDER BY title ASC");
        return array_map(fn($data) => new Book($data), $stmt->fetchAll());
    }

    public static function search(string $query): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT * FROM books 
            WHERE is_active = 1 AND (
                title LIKE ? OR 
                author LIKE ? OR 
                isbn LIKE ?
            )
            ORDER BY title ASC
        ");
        $searchTerm = "%{$query}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        return array_map(fn($data) => new Book($data), $stmt->fetchAll());
    }

    public static function getAvailable(): array
    {
        $stmt = Database::pdo()->query("
            SELECT * FROM books 
            WHERE is_active = 1 AND copies_available > 0 
            ORDER BY title ASC
        ");
        return array_map(fn($data) => new Book($data), $stmt->fetchAll());
    }

    public static function getLowStock(): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT * FROM books 
            WHERE is_active = 1 AND copies_available <= ?
            ORDER BY copies_available ASC
        ");
        $threshold = self::getLowStockThreshold();
        $stmt->execute([$threshold]);
        return array_map(fn($data) => new Book($data), $stmt->fetchAll());
    }

    public function isAvailable(): bool
    {
        return $this->copies_available > 0 && $this->is_active;
    }

    public function canBorrow(): bool
    {
        return $this->isAvailable();
    }

    public function borrow(): bool
    {
        if (!$this->canBorrow()) {
            return false;
        }

        $stmt = Database::pdo()->prepare("
            UPDATE books 
            SET copies_available = copies_available - 1, updated_at = datetime('now')
            WHERE id = ? AND copies_available > 0
        ");
        $result = $stmt->execute([$this->id]);
        
        // Update the object's property to reflect the change
        if ($result) {
            $this->copies_available = $this->copies_available - 1;
        }
        
        return $result;
    }

    public function return(): bool
    {
        $stmt = Database::pdo()->prepare("
            UPDATE books 
            SET copies_available = copies_available + 1, updated_at = datetime('now')
            WHERE id = ? AND copies_available < copies_total
        ");
        $result = $stmt->execute([$this->id]);
        
        // Update the object's property to reflect the change
        if ($result) {
            $this->copies_available = $this->copies_available + 1;
        }
        
        return $result;
    }

    public function save(): bool
    {
        if ($this->id > 0) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    private function create(): bool
    {
        try {
            $stmt = Database::pdo()->prepare("
                INSERT INTO books (title, author, isbn, published_year, description, copies_total, copies_available, borrow_fee, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $result = $stmt->execute([
                $this->title,
                $this->author,
                $this->isbn,
                $this->published_year,
                $this->description,
                $this->copies_total,
                $this->copies_available,
                $this->borrow_fee,
                $this->is_active ? 1 : 0
            ]);
            
            if ($result) {
                $this->id = (int) Database::pdo()->lastInsertId();
            }
            
            return $result;
        } catch (\Exception $e) {
            error_log("Book creation error: " . $e->getMessage());
            return false;
        }
    }

    private function update(): bool
    {
        $stmt = Database::pdo()->prepare("
            UPDATE books SET 
                title = ?, author = ?, isbn = ?, published_year = ?, 
                description = ?, copies_total = ?, copies_available = ?, 
                borrow_fee = ?, is_active = ?, updated_at = datetime('now')
            WHERE id = ?
        ");
        return $stmt->execute([
            $this->title,
            $this->author,
            $this->isbn,
            $this->published_year,
            $this->description,
            $this->copies_total,
            $this->copies_available,
            $this->borrow_fee,
            $this->is_active ? 1 : 0,
            $this->id
        ]);
    }

    public function delete(): bool
    {
        $stmt = Database::pdo()->prepare("UPDATE books SET is_active = 0, updated_at = datetime('now') WHERE id = ?");
        return $stmt->execute([$this->id]);
    }

    public static function getPenaltyRate(): float
    {
        $stmt = Database::pdo()->query("SELECT setting_value FROM system_settings WHERE setting_key = 'penalty_rate'");
        $result = $stmt->fetch();
        return $result ? (float)$result['setting_value'] : 1.00;
    }

    public function getBook(): Book
    {
        return $this;
    }

    public function getBorrowingHistory(): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT b.*, u.full_name as student_name, u.username
            FROM borrowings b 
            JOIN users u ON b.student_id = u.id 
            WHERE b.book_id = ?
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }

    public static function getLowStockThreshold(): int
    {
        $stmt = Database::pdo()->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'low_stock_threshold'");
        $stmt->execute();
        return (int) $stmt->fetchColumn() ?: 2;
    }

    public static function getBorrowingLimit(): int
    {
        $stmt = Database::pdo()->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'borrowing_limit'");
        $stmt->execute();
        return (int) $stmt->fetchColumn() ?: 3;
    }

    public static function getBorrowingDuration(): int
    {
        $stmt = Database::pdo()->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'borrowing_duration'");
        $stmt->execute();
        return (int) $stmt->fetchColumn() ?: 14;
    }

    public static function getDefaultBorrowFee(): float
    {
        $stmt = Database::pdo()->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'borrow_fee'");
        $stmt->execute();
        return (float) $stmt->fetchColumn() ?: 1.00;
    }

    /**
     * Recalculate and update the copies_available for a specific book
     * based on active borrowings
     */
    public function recalculateAvailability(): bool
    {
        // Count active borrowings for this book
        $stmt = Database::pdo()->prepare("
            SELECT COUNT(*) FROM borrowings 
            WHERE book_id = ? AND status = 'active'
        ");
        $stmt->execute([$this->id]);
        $active_borrowings = (int) $stmt->fetchColumn();
        
        // Calculate correct available copies
        $correct_available = $this->copies_total - $active_borrowings;
        
        // Update the database
        $stmt = Database::pdo()->prepare("
            UPDATE books 
            SET copies_available = ?, updated_at = datetime('now')
            WHERE id = ?
        ");
        
        return $stmt->execute([$correct_available, $this->id]);
    }

    /**
     * Recalculate and update copies_available for all books
     */
    public static function recalculateAllAvailability(): bool
    {
        try {
            // Get all books
            $books = self::getAll();
            
            foreach ($books as $book) {
                $book->recalculateAvailability();
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the number of currently borrowed copies
     */
    public function getBorrowedCount(): int
    {
        try {
            $stmt = Database::pdo()->prepare("
                SELECT COUNT(*) 
                FROM borrowings 
                WHERE book_id = ? AND return_date IS NULL
            ");
            $stmt->execute([$this->id]);
            return (int) $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Force recalculation of available copies based on current borrowings
     */
    public function forceRecalculateAvailability(): bool
    {
        try {
            $borrowed_count = $this->getBorrowedCount();
            $new_available = $this->copies_total - $borrowed_count;
            
            // Update the object property
            $this->copies_available = $new_available;
            
            // Update the database
            $stmt = Database::pdo()->prepare("
                UPDATE books 
                SET copies_available = ?, updated_at = datetime('now')
                WHERE id = ?
            ");
            $result = $stmt->execute([$new_available, $this->id]);
            
            error_log("DEBUG: forceRecalculateAvailability - Book {$this->id}: Total={$this->copies_total}, Borrowed={$borrowed_count}, New Available={$new_available}");
            
            return $result;
        } catch (\Exception $e) {
            error_log("Error recalculating availability for book {$this->id}: " . $e->getMessage());
            return false;
        }
    }

}
