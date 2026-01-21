<?php
/**
 * Authentication Controller
 * Handles login, registration, and logout logic
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/user.php';

class AuthController {
    private $db;
    private $userModel;
    
    public function __construct() {
        $this->db = get_db_connection();
        $this->userModel = new User($this->db);
    }
    
    /**
     * Handle user login
     */
    public function login($login, $password) {
        // Validate inputs
        if (empty($login) || empty($password)) {
            return [
                'success' => false,
                'message' => 'All fields are required'
            ];
        }
        
        // Check if login is email or student ID
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $userData = $this->userModel->getByEmail($login);
        } else {
            $userData = $this->userModel->getByStudentId($login);
        }
        
        // Verify credentials
        if (!$userData || !password_verify($password, $userData->password_hash)) {
            return [
                'success' => false,
                'message' => 'Invalid email/student ID or password'
            ];
        }
        
        // Check account status
        if ($userData->account_status !== 'ACTIVE') {
            return [
                'success' => false,
                'message' => 'Your account is ' . strtolower($userData->account_status)
            ];
        }
        
        // Set session
        $_SESSION['user_id'] = $userData->user_id;
        $_SESSION['role'] = $userData->role;
        $_SESSION['full_name'] = $userData->full_name;
        $_SESSION['email'] = $userData->email;
        
        // Determine redirect URL based on role
        $redirectUrl = (in_array($userData->role, ['ADMIN', 'ADMINISTRATOR', 'MODERATOR', 'STAFF'])) 
            ? 'admin/dashboard.php' 
            : 'student/dashboard.php';
        
        return [
            'success' => true,
            'message' => 'Login successful',
            'redirect' => $redirectUrl
        ];
    }
    
    /**
     * Handle user registration
     */
    public function register($fullName, $email, $studentId, $phone, $password, $confirmPassword) {
        // Validate inputs
        if (empty($fullName) || empty($email) || empty($studentId) || empty($phone) || empty($password)) {
            return [
                'success' => false,
                'message' => 'All fields are required'
            ];
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format'
            ];
        }
        
        // Validate phone number
        if (!preg_match('/^01[0-9]{9}$/', $phone)) {
            return [
                'success' => false,
                'message' => 'Phone number must be 11 digits starting with 01'
            ];
        }
        
        // Validate password length
        if (strlen($password) < 6) {
            return [
                'success' => false,
                'message' => 'Password must be at least 6 characters'
            ];
        }
        
        // Validate password match
        if ($password !== $confirmPassword) {
            return [
                'success' => false,
                'message' => 'Passwords do not match'
            ];
        }
        
        // Register user through model
        return $this->userModel->register($fullName, $email, $studentId, $phone, $password);
    }
    
    /**
     * Handle user logout
     */
    public function logout() {
        session_unset();
        session_destroy();
        return [
            'success' => true,
            'message' => 'Logged out successfully',
            'redirect' => 'index.php?page=login'
        ];
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get current user info
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'user_id' => $_SESSION['user_id'],
            'role' => $_SESSION['role'],
            'full_name' => $_SESSION['full_name'],
            'email' => $_SESSION['email']
        ];
    }
}

/**
 * Logout function for router
 */
function logout() {
    session_unset();
    session_destroy();
    redirect('login');
}
