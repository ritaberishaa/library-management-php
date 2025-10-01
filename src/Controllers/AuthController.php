<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;
use App\Models\AuditLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        Auth::redirectIfLoggedIn();
        $this->view('auth/login');
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->setFlash('error', 'Please fill in all fields.');
            $this->redirect('/login');
            return;
        }

        $user = User::authenticate($username, $password);
        
        if (!$user) {
            $this->setFlash('error', 'Invalid username or password.');
            $this->redirect('/login');
            return;
        }

        if (!$user->is_active) {
            $this->setFlash('error', 'Your account has been deactivated.');
            $this->redirect('/login');
            return;
        }

        Auth::login($user);
        
        // Log successful login
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'table_name' => 'users',
            'record_id' => $user->id,
            'old_values' => null,
            'new_values' => null,
            'ip_address' => $ip_address
        ]);
        
        // Redirect based on role
        switch ($user->role) {
            case 'student':
                $this->redirect('/student/dashboard');
                break;
            case 'operator':
                $this->redirect('/operator/dashboard');
                break;
            case 'super_admin':
                $this->redirect('/admin/dashboard');
                break;
            default:
                $this->redirect('/');
        }
    }

    public function logout()
    {
        Auth::logout();
        $this->redirect('/login');
    }

    public function showRegister()
    {
        Auth::redirectIfLoggedIn();
        $this->view('auth/register');
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
            return;
        }

        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $full_name = $_POST['full_name'] ?? '';
        $phone = $_POST['phone'] ?? '';

        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
            $this->setFlash('error', 'Please fill in all required fields.');
            $this->redirect('/register');
            return;
        }

        if ($password !== $confirm_password) {
            $this->setFlash('error', 'Passwords do not match.');
            $this->redirect('/register');
            return;
        }

        if (strlen($password) < 6) {
            $this->setFlash('error', 'Password must be at least 6 characters long.');
            $this->redirect('/register');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Please enter a valid email address.');
            $this->redirect('/register');
            return;
        }

        // Check if username or email already exists
        if (User::findByUsername($username)) {
            $this->setFlash('error', 'Username already exists.');
            $this->redirect('/register');
            return;
        }

        if (User::findByEmail($email)) {
            $this->setFlash('error', 'Email already exists.');
            $this->redirect('/register');
            return;
        }

        // Create new user (only students can register)
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->setPasswordHash(password_hash($password, PASSWORD_DEFAULT));
        $user->role = 'student';
        $user->full_name = $full_name;
        $user->phone = $phone;
        $user->is_active = true;

        if ($user->save()) {
            $this->setFlash('success', 'Registration successful! Please login.');
            $this->redirect('/login');
        } else {
            $this->setFlash('error', 'Registration failed. Please try again.');
            $this->redirect('/register');
        }
    }

    public function showChangePassword()
    {
        Auth::requireAuth();
        $this->view('auth/change-password');
    }

    public function changePassword()
    {
        Auth::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/change-password');
            return;
        }

        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $this->setFlash('error', 'Please fill in all fields.');
            $this->redirect('/change-password');
            return;
        }

        if ($new_password !== $confirm_password) {
            $this->setFlash('error', 'New passwords do not match.');
            $this->redirect('/change-password');
            return;
        }

        if (strlen($new_password) < 6) {
            $this->setFlash('error', 'New password must be at least 6 characters long.');
            $this->redirect('/change-password');
            return;
        }

        $user = Auth::user();
        if (!password_verify($current_password, $user->getPasswordHash())) {
            $this->setFlash('error', 'Current password is incorrect.');
            $this->redirect('/change-password');
            return;
        }

        if ($user->updatePassword($new_password)) {
            $this->setFlash('success', 'Password changed successfully.');
            $this->redirect('/change-password');
        } else {
            $this->setFlash('error', 'Failed to change password. Please try again.');
            $this->redirect('/change-password');
        }
    }

    public function unauthorized()
    {
        http_response_code(403);
        $this->view('errors/unauthorized');
    }
    
    public function notifications()
    {
        Auth::requireAuth();
        
        $user = Auth::user();
        
        // Redirect based on user role
        switch ($user->role) {
            case 'student':
                $this->redirect('/student/notifications');
                break;
            case 'operator':
                $this->redirect('/operator/notifications');
                break;
            case 'super_admin':
                $this->redirect('/admin/notifications');
                break;
            default:
                $this->redirect('/unauthorized');
        }
    }
}
