<?php
// Session configuration
session_start();

// Note: Core authentication functions (is_logged_in, is_admin, is_student, etc.) 
// are now defined in helpers.php to avoid duplication

function requireLogin() {
    if (!is_logged_in()) {
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
    if (!is_admin()) {
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

function requireStudent() {
    requireLogin();
    if (!is_student()) {
        // Redirect non-students to appropriate page
        if (is_admin()) {
            header('Location: ../admin/dashboard.php');
        } else {
            header('Location: ../login.php');
        }
        exit();
    }
}
?>
