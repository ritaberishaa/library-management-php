<?php
namespace App\Core;

use App\Models\User;

class Middleware
{
    public static function auth(): callable
    {
        return function() {
            Auth::requireAuth();
        };
    }

    public static function student(): callable
    {
        return function() {
            Auth::requireRole('student');
        };
    }

    public static function operator(): callable
    {
        return function() {
            Auth::requireRole('operator');
        };
    }

    public static function superAdmin(): callable
    {
        return function() {
            Auth::requireRole('super_admin');
        };
    }

    public static function staff(): callable
    {
        return function() {
            Auth::requireAnyRole(['operator', 'super_admin']);
        };
    }

    public static function admin(): callable
    {
        return function() {
            Auth::requireAnyRole(['super_admin']);
        };
    }

    public static function guest(): callable
    {
        return function() {
            Auth::redirectIfLoggedIn();
        };
    }

    public static function csrf(): callable
    {
        return function() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    http_response_code(403);
                    die('CSRF token mismatch');
                }
            }
        };
    }

    public static function rateLimit(int $maxRequests = 10, int $windowMinutes = 1): callable
    {
        return function() use ($maxRequests, $windowMinutes) {
            $key = 'rate_limit_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
            $now = time();
            $window = $windowMinutes * 60;
            
            if (!isset($_SESSION[$key])) {
                $_SESSION[$key] = [];
            }
            
            // Clean old requests
            $_SESSION[$key] = array_filter($_SESSION[$key], function($timestamp) use ($now, $window) {
                return ($now - $timestamp) < $window;
            });
            
            if (count($_SESSION[$key]) >= $maxRequests) {
                http_response_code(429);
                die('Too many requests. Please try again later.');
            }
            
            $_SESSION[$key][] = $now;
        };
    }
}
