<?php
/**
 * Helper Functions
 * Utility functions used throughout the application
 */

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is student
 */
function is_student() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'STUDENT';
}

/**
 * Check if user is admin
 */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'ADMIN';
}

/**
 * Require student authentication
 */
function check_student_auth() {
    if (!is_logged_in() || !is_student()) {
        $_SESSION['error'] = "Please login as student to access this page.";
        header("Location: index.php?page=login");
        exit();
    }
}

/**
 * Require admin authentication
 */
function check_admin_auth() {
    if (!is_logged_in() || !is_admin()) {
        $_SESSION['error'] = "Unauthorized access. Admin only.";
        header("Location: index.php?page=login");
        exit();
    }
}

/**
 * Redirect to a page
 */
function redirect($page, $params = []) {
    $url = "index.php?page=" . $page;
    if (!empty($params)) {
        $url .= "&" . http_build_query($params);
    }
    header("Location: " . $url);
    exit();
}

/**
 * Set flash message
 */
function set_flash($type, $message) {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

/**
 * Get and clear flash message
 */
function get_flash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type' => $_SESSION['flash_type'],
            'message' => $_SESSION['flash_message']
        ];
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * Sanitize input
 */
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Format date for display
 */
function format_date($date) {
    return date('M d, Y', strtotime($date));
}

/**
 * Format datetime for display
 */
function format_datetime($datetime) {
    return date('M d, Y h:i A', strtotime($datetime));
}

/**
 * Get base URL
 */
function base_url($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $base = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . "://" . $host . $base . ($path ? '/' . $path : '');
}

/**
 * Get asset URL
 */
function asset_url($path) {
    return base_url('assets/' . $path);
}

/**
 * Get upload URL
 */
function upload_url($path) {
    return base_url('uploads/' . $path);
}

/**
 * Upload image file
 */
function upload_image($file, $folder = 'items') {
    $target_dir = UPLOADS_PATH . '/' . $folder . '/';
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error'];
    }
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $file_type = mime_content_type($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        return ['success' => false, 'error' => 'Only JPG and PNG images are allowed'];
    }
    
    // Validate file size (max 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        return ['success' => false, 'error' => 'File size must be less than 2MB'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $target_file = $target_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'filename' => $folder . '/' . $filename];
    } else {
        return ['success' => false, 'error' => 'Failed to save uploaded file'];
    }
}

/**
 * Escape output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
