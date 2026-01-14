<?php
// Session configuration
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function isAdmin() {
    return getUserRole() === 'ADMIN';
}

function requireLogin() {
    if (!isLoggedIn()) {
        // Get the base path relative to current location
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if (strpos($basePath, '/views/admin') !== false || strpos($basePath, '/views/student') !== false) {
            header('Location: ../login.php');
        } elseif (strpos($basePath, '/views') !== false) {
            header('Location: login.php');
        } else {
            header('Location: views/login.php');
        }
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        // Get the base path relative to current location
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if (strpos($basePath, '/views/admin') !== false) {
            header('Location: ../student/dashboard.php');
        } elseif (strpos($basePath, '/views') !== false) {
            header('Location: student/dashboard.php');
        } else {
            header('Location: views/student/dashboard.php');
        }
        exit();
    }
}
?>
