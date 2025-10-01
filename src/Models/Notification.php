<?php

namespace App\Models;

use App\Core\Database;

class Notification
{
    public $id;
    public $user_id;
    public $title;
    public $message;
    public $type;
    public $is_read;
    public $created_at;
    public $updated_at;

    public function __construct($data = [])
    {
        $this->id = (int) ($data['id'] ?? 0);
        $this->user_id = (int) ($data['user_id'] ?? 0);
        $this->title = $data['title'] ?? '';
        $this->message = $data['message'] ?? '';
        $this->type = $data['type'] ?? 'system';
        $this->is_read = (bool) ($data['is_read'] ?? false);
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $data['updated_at'] ?? date('Y-m-d H:i:s');
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
        $stmt = Database::pdo()->prepare("
            INSERT INTO notifications (user_id, title, message, type, is_read) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $result = $stmt->execute([
            $this->user_id,
            $this->title,
            $this->message,
            $this->type,
            $this->is_read ? 1 : 0
        ]);
        
        if ($result) {
            $this->id = (int) Database::pdo()->lastInsertId();
        }
        
        return $result;
    }

    private function update(): bool
    {
        $stmt = Database::pdo()->prepare("
            UPDATE notifications 
            SET is_read = ?, updated_at = datetime('now')
            WHERE id = ?
        ");
        return $stmt->execute([$this->is_read ? 1 : 0, $this->id]);
    }

    public static function createForAllStudents($title, $message, $type = 'system'): bool
    {
        $stmt = Database::pdo()->prepare("
            SELECT id FROM users WHERE role = 'student'
        ");
        $stmt->execute();
        $students = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        $success = true;
        foreach ($students as $student_id) {
            $notification = new self([
                'user_id' => $student_id,
                'title' => $title,
                'message' => $message,
                'type' => $type
            ]);
            
            if (!$notification->save()) {
                $success = false;
            }
        }
        
        return $success;
    }

    public static function getByUser($user_id): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$user_id]);
        return array_map(fn($data) => new self($data), $stmt->fetchAll());
    }

    public static function getUnreadCount($user_id): int
    {
        $stmt = Database::pdo()->prepare("
            SELECT COUNT(*) FROM notifications 
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$user_id]);
        return (int) $stmt->fetchColumn();
    }

    public function markAsRead(): bool
    {
        $this->is_read = true;
        return $this->save();
    }

    public static function markAllAsRead($user_id): bool
    {
        $stmt = Database::pdo()->prepare("
            UPDATE notifications 
            SET is_read = 1, updated_at = datetime('now')
            WHERE user_id = ? AND is_read = 0
        ");
        return $stmt->execute([$user_id]);
    }
    
    public static function find($id): ?self
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM notifications WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        
        if ($data) {
            return new self($data);
        }
        
        return null;
    }
}