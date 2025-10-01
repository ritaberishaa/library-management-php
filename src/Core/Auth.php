<?php
namespace App\Core;

use App\Models\User;

class Auth
{
    private static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(User $user): void
    {
        self::ensureSession();
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_role'] = $user->role;
        $_SESSION['user_name'] = $user->full_name;
        $_SESSION['logged_in'] = true;
    }

    public static function logout(): void
    {
        self::ensureSession();
        session_destroy();
    }

    public static function check(): bool
    {
        self::ensureSession();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public static function user(): ?User
    {
        if (!self::check()) {
            return null;
        }

        return User::find($_SESSION['user_id']);
    }

    public static function isStudent(): bool
    {
        return self::check() && $_SESSION['user_role'] === 'student';
    }

    public static function isOperator(): bool
    {
        return self::check() && $_SESSION['user_role'] === 'operator';
    }

    public static function isSuperAdmin(): bool
    {
        return self::check() && $_SESSION['user_role'] === 'super_admin';
    }

    public static function isAdmin(): bool
    {
        return self::isSuperAdmin();
    }

    public static function canManageBooks(): bool
    {
        return self::isOperator() || self::isSuperAdmin();
    }

    public static function canManageUsers(): bool
    {
        return self::isSuperAdmin();
    }

    public static function canBorrowBooks(): bool
    {
        return self::isStudent();
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireAuth();
        
        if ($_SESSION['user_role'] !== $role) {
            header('Location: /unauthorized');
            exit;
        }
    }

    public static function requireAnyRole(array $roles): void
    {
        self::requireAuth();
        
        if (!in_array($_SESSION['user_role'], $roles)) {
            header('Location: /unauthorized');
            exit;
        }
    }

    public static function redirectIfLoggedIn(): void
    {
        if (self::check()) {
            $user = self::user();
            if ($user) {
                switch ($user->role) {
                    case 'student':
                        header('Location: /student/dashboard');
                        break;
                    case 'operator':
                        header('Location: /operator/dashboard');
                        break;
                    case 'super_admin':
                        header('Location: /admin/dashboard');
                        break;
                }
                exit;
            }
        }
    }

}
