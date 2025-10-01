<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class AuditLog
{
    public int $id;
    public ?int $user_id;
    public string $action;
    public string $table_name;
    public ?int $record_id;
    public ?string $old_values;
    public ?string $new_values;
    public ?string $ip_address;
    public string $created_at;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = (int) ($data['id'] ?? 0);
            $this->user_id = $data['user_id'] ? (int) $data['user_id'] : null;
            $this->action = $data['action'] ?? '';
            $this->table_name = $data['table_name'] ?? '';
            $this->record_id = $data['record_id'] ? (int) $data['record_id'] : null;
            $this->old_values = $data['old_values'] ?? null;
            $this->new_values = $data['new_values'] ?? null;
            $this->ip_address = $data['ip_address'] ?? null;
            $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        }
    }

    public static function create(array $data): bool
    {
        $stmt = Database::pdo()->prepare("
            INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values, ip_address) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['user_id'] ?? null,
            $data['action'],
            $data['table_name'] ?? '',
            $data['record_id'] ?? null,
            $data['old_values'] ?? null,
            $data['new_values'] ?? null,
            $data['ip_address'] ?? null
        ]);
    }

    public static function getAll(int $limit = 50, int $offset = 0): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT al.*, u.full_name as user_name 
            FROM audit_logs al 
            LEFT JOIN users u ON al.user_id = u.id 
            ORDER BY al.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public static function getByUser(int $user_id, int $limit = 50, int $offset = 0): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT al.*, u.full_name as user_name 
            FROM audit_logs al 
            LEFT JOIN users u ON al.user_id = u.id 
            WHERE al.user_id = ? 
            ORDER BY al.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$user_id, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public static function getByAction(string $action, int $limit = 50, int $offset = 0): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT al.*, u.full_name as user_name 
            FROM audit_logs al 
            LEFT JOIN users u ON al.user_id = u.id 
            WHERE al.action = ? 
            ORDER BY al.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$action, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public static function getByDateRange(string $from_date, string $to_date, int $limit = 50, int $offset = 0): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT al.*, u.full_name as user_name 
            FROM audit_logs al 
            LEFT JOIN users u ON al.user_id = u.id 
            WHERE DATE(al.created_at) BETWEEN ? AND ? 
            ORDER BY al.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$from_date, $to_date, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public static function search(string $search_term, int $limit = 50, int $offset = 0): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT al.*, u.full_name as user_name 
            FROM audit_logs al 
            LEFT JOIN users u ON al.user_id = u.id 
            WHERE al.action LIKE ? OR al.table_name LIKE ? OR u.full_name LIKE ?
            ORDER BY al.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $search = "%{$search_term}%";
        $stmt->execute([$search, $search, $search, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public static function getTotalCount(): int
    {
        $stmt = Database::pdo()->query("SELECT COUNT(*) FROM audit_logs");
        return (int) $stmt->fetchColumn();
    }

    public static function clearAll(): bool
    {
        $stmt = Database::pdo()->prepare("DELETE FROM audit_logs");
        return $stmt->execute();
    }

    public static function exportToCSV(): string
    {
        $logs = self::getAll(1000); // Export last 1000 logs
        $csv = "ID,User,Action,Table,Record ID,Old Values,New Values,IP Address,Created At\n";
        
        foreach ($logs as $log) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $log['id'],
                '"' . str_replace('"', '""', $log['user_name'] ?? 'System') . '"',
                '"' . str_replace('"', '""', $log['action']) . '"',
                '"' . str_replace('"', '""', $log['table_name']) . '"',
                $log['record_id'] ?? '',
                '"' . str_replace('"', '""', $log['old_values'] ?? '') . '"',
                '"' . str_replace('"', '""', $log['new_values'] ?? '') . '"',
                '"' . str_replace('"', '""', $log['ip_address'] ?? '') . '"',
                '"' . str_replace('"', '""', $log['created_at']) . '"'
            );
        }
        
        return $csv;
    }

    public static function getDescription(string $action, string $table_name, ?array $old_values = null, ?array $new_values = null): string
    {
        switch ($action) {
            case 'login':
                return 'User logged into the system';
            case 'logout':
                return 'User logged out of the system';
            case 'create':
                return "Created new {$table_name} record";
            case 'update':
                return "Updated {$table_name} record";
            case 'delete':
                return "Deleted {$table_name} record";
            case 'borrow':
                return "Borrowed book";
            case 'return':
                return "Returned book";
            default:
                return ucfirst($action) . " action on {$table_name}";
        }
    }
}
