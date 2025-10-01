<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    public int $id;
    public string $username;
    public string $email;
    public string $role;
    public string $full_name;
    public ?string $phone;
    public bool $is_active;
    public string $created_at;
    public string $updated_at;
    private ?string $password_hash = null;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = (int) ($data['id'] ?? 0);
            $this->username = $data['username'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->role = $data['role'] ?? '';
            $this->full_name = $data['full_name'] ?? '';
            $this->phone = $data['phone'] ?? null;
            $this->is_active = (bool) ($data['is_active'] ?? true);
            $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
            $this->updated_at = $data['updated_at'] ?? date('Y-m-d H:i:s');
        }
    }

    public static function find(int $id): ?User
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? new User($data) : null;
    }

    public static function findByUsername(string $username): ?User
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $data = $stmt->fetch();
        return $data ? new User($data) : null;
    }

    public static function findByEmail(string $email): ?User
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $data = $stmt->fetch();
        return $data ? new User($data) : null;
    }

    public static function authenticate(string $username, string $password): ?User
    {
        $user = self::findByUsername($username);
        if ($user && password_verify($password, $user->getPasswordHash())) {
            return $user;
        }
        return null;
    }

    public function getPasswordHash(): string
    {
        if ($this->password_hash === null) {
            $stmt = Database::pdo()->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$this->id]);
            $this->password_hash = $stmt->fetchColumn();
        }
        return $this->password_hash;
    }

    public function setPasswordHash(string $hash): void
    {
        $this->password_hash = $hash;
    }

    public function updatePassword(string $password): bool
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = Database::pdo()->prepare("UPDATE users SET password_hash = ?, updated_at = datetime('now') WHERE id = ?");
        return $stmt->execute([$hash, $this->id]);
    }

    public function save(): bool
    {
        if (isset($this->id) && $this->id > 0) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    private function create(): bool
    {
        $stmt = Database::pdo()->prepare("
            INSERT INTO users (username, email, password_hash, role, full_name, phone, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $result = $stmt->execute([
            $this->username,
            $this->email,
            $this->password_hash,
            $this->role,
            $this->full_name,
            $this->phone,
            $this->is_active ? 1 : 0
        ]);
        
        if (!$result) {
            // Log the error for debugging
            $error = $stmt->errorInfo();
            error_log("User creation failed: " . print_r($error, true));
            error_log("User data: " . print_r([
                'username' => $this->username,
                'email' => $this->email,
                'role' => $this->role,
                'full_name' => $this->full_name,
                'phone' => $this->phone,
                'is_active' => $this->is_active
            ], true));
        }
        
        if ($result) {
            $this->id = (int) Database::pdo()->lastInsertId();
        }
        
        return $result;
    }

    private function update(): bool
    {
        $stmt = Database::pdo()->prepare("
            UPDATE users SET 
                username = ?, email = ?, role = ?, full_name = ?, 
                phone = ?, is_active = ?, updated_at = datetime('now')
            WHERE id = ?
        ");
        return $stmt->execute([
            $this->username,
            $this->email,
            $this->role,
            $this->full_name,
            $this->phone,
            $this->is_active ? 1 : 0,
            $this->id
        ]);
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function canManageBooks(): bool
    {
        return $this->isOperator() || $this->isSuperAdmin();
    }

    public function canManageUsers(): bool
    {
        return $this->isSuperAdmin();
    }

    public function canBorrowBooks(): bool
    {
        return $this->isStudent();
    }

    public static function getAll(): array
    {
        $stmt = Database::pdo()->query("SELECT * FROM users ORDER BY created_at DESC");
        return array_map(fn($data) => new User($data), $stmt->fetchAll());
    }

    public static function getByRole(string $role): array
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM users WHERE role = ? ORDER BY created_at DESC");
        $stmt->execute([$role]);
        return array_map(fn($data) => new User($data), $stmt->fetchAll());
    }

    public function getActiveBorrowings(): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT b.*, bk.title, bk.author 
            FROM borrowings b 
            JOIN books bk ON b.book_id = bk.id 
            WHERE b.student_id = ? AND b.status = 'active'
            ORDER BY b.due_date ASC
        ");
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }

    public function getBorrowingHistory(): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT b.*, bk.title, bk.author 
            FROM borrowings b 
            JOIN books bk ON b.book_id = bk.id 
            WHERE b.student_id = ?
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }

    public function getTotalPenalties(): float
    {
        $stmt = Database::pdo()->prepare("
            SELECT COALESCE(SUM(penalty_amount), 0) as total 
            FROM borrowings 
            WHERE student_id = ? AND penalty_paid = 0
        ");
        $stmt->execute([$this->id]);
        return (float) $stmt->fetchColumn();
    }

    public function getOverdueBooks(): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT b.*, bk.title, bk.author,
                   CAST((julianday('now') - julianday(b.due_date)) AS INTEGER) as days_overdue
            FROM borrowings b 
            JOIN books bk ON b.book_id = bk.id 
            WHERE b.student_id = ? AND b.status = 'active' AND b.due_date < datetime('now')
        ");
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }

    public function delete(): bool
    {
        $stmt = Database::pdo()->prepare("UPDATE users SET is_active = 0, updated_at = datetime('now') WHERE id = ?");
        return $stmt->execute([$this->id]);
    }
    
}
