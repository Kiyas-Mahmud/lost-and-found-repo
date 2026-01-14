<?php
/**
 * Authentication Controller
 * Handles login, register, logout
 */

function login() {
    // Handle login form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // TODO: Implement login logic
        $email = clean_input($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Placeholder: For now just show error
        set_flash('error', 'Login functionality will be implemented in Phase 2');
        redirect('login');
    }
    
    $page = 'login';
    $page_title = 'Login';
    
    load_layout('public', VIEWS_PATH . '/pages/public/login.php', [
        'page' => $page,
        'page_title' => $page_title
    ]);
}

function register() {
    // Handle registration form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // TODO: Implement registration logic
        set_flash('error', 'Registration functionality will be implemented in Phase 2');
        redirect('register');
    }
    
    $page = 'register';
    $page_title = 'Register';
    
    load_layout('public', VIEWS_PATH . '/pages/public/register.php', [
        'page' => $page,
        'page_title' => $page_title
    ]);
}

function logout() {
    // Destroy session
    session_destroy();
    set_flash('success', 'You have been logged out successfully');
    redirect('home');
}
