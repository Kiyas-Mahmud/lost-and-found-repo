<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$email = trim($_POST['email'] ?? '');
$new_password = $_POST['new_password'] ?? '';

// Validation
if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

if (empty($new_password)) {
    echo json_encode(['success' => false, 'message' => 'New password is required']);
    exit();
}

if (strlen($new_password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit();
}

try {
    $conn = get_db_connection();
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Email address not found']);
        exit();
    }
    
    // Update password
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $update_stmt = $conn->prepare("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE email = ?");
    $update_stmt->execute([$password_hash, $email]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Password reset successful! You can now login with your new password.'
    ]);
    
} catch (PDOException $e) {
    error_log("Reset password error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
}
