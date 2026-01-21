<?php
/**
 * API Base - JSON Response Helpers
 */

// Set JSON headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/**
 * Send JSON success response
 */
function jsonSuccess($data = [], $message = 'Success', $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data
    ], JSON_PRETTY_PRINT);
    exit();
}

/**
 * Send JSON error response
 */
function jsonError($message = 'Error occurred', $code = 400, $errors = []) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'message' => $message,
        'errors' => $errors
    ], JSON_PRETTY_PRINT);
    exit();
}

/**
 * Validate required fields
 */
function validateRequired($data, $fields) {
    $errors = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $errors[$field] = ucfirst($field) . ' is required';
        }
    }
    return $errors;
}

/**
 * Get JSON input
 */
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}

/**
 * Check if user is authenticated
 */
function checkAuth() {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        jsonError('Unauthorized. Please login.', 401);
    }
    return $_SESSION;
}

/**
 * Check if user is admin
 */
function checkAdmin() {
    $session = checkAuth();
    if ($session['role'] !== 'ADMIN') {
        jsonError('Forbidden. Admin access required.', 403);
    }
    return $session;
}

/**
 * Check if user is student
 */
function checkStudent() {
    $session = checkAuth();
    if ($session['role'] !== 'STUDENT') {
        jsonError('Forbidden. Student access required.', 403);
    }
    return $session;
}

/**
 * Sanitize input
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}
