<?php
/**
 * Post Item API Endpoint
 * Handles posting new lost or found items with image upload
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/item.php';
require_once __DIR__ . '/../base.php';

// Check authentication
if (!is_logged_in() || !is_student()) {
    jsonError('Unauthorized access', 401);
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

// Get database connection
$db = get_db_connection();
$itemModel = new Item($db);

// Get user ID
$userId = $_SESSION['user_id'];

// Validate required fields
$required = ['title', 'description', 'item_type', 'category_id', 'location_id', 'event_date'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        jsonError("$field is required", 400);
    }
}

// Validate item type
if (!in_array($_POST['item_type'], ['LOST', 'FOUND'])) {
    jsonError('Invalid item type', 400);
}

// Validate date (should not be in the future)
$eventDate = $_POST['event_date'];
$eventTimestamp = strtotime($eventDate . ' 00:00:00');
$todayTimestamp = strtotime(date('Y-m-d') . ' 00:00:00');

if ($eventTimestamp > $todayTimestamp) {
    jsonError('Event date cannot be in the future', 400);
}

// Handle image upload
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $imagePath = handleImageUpload($_FILES['image']);
    if (!$imagePath) {
        jsonError('Failed to upload image', 400);
    }
}

// Prepare item data
$itemData = [
    'title' => trim($_POST['title']),
    'description' => trim($_POST['description']),
    'item_type' => $_POST['item_type'],
    'category_id' => (int)$_POST['category_id'],
    'location_id' => (int)$_POST['location_id'],
    'event_date' => $eventDate,
    'image_path' => $imagePath,
    'posted_by' => $userId,
    'current_status' => 'OPEN'
];

// Create item
try {
    $itemId = $itemModel->createItem($itemData);
    
    if ($itemId) {
        jsonSuccess([
            'item_id' => $itemId,
            'message' => 'Item posted successfully'
        ]);
    } else {
        // Clean up uploaded image if item creation failed
        if ($imagePath && file_exists(__DIR__ . '/../../' . $imagePath)) {
            unlink(__DIR__ . '/../../' . $imagePath);
        }
        jsonError('Failed to create item', 500);
    }
} catch (Exception $e) {
    // Clean up uploaded image on error
    if ($imagePath && file_exists(__DIR__ . '/../../' . $imagePath)) {
        unlink(__DIR__ . '/../../' . $imagePath);
    }
    jsonError('Error creating item: ' . $e->getMessage(), 500);
}

/**
 * Handle image upload with validation
 */
function handleImageUpload($file) {
    // Validate file
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return false;
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        return false;
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = __DIR__ . '/../../uploads/items/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('item_', true) . '.' . $extension;
    $uploadPath = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Return relative path for database storage
        return 'uploads/items/' . $filename;
    }
    
    return false;
}
