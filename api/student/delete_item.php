<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/item.php';

// Check authentication
if (!is_logged_in() || !is_student()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$userId = $_SESSION['user_id'];
$itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

if (!$itemId) {
    echo json_encode(['success' => false, 'message' => 'Item ID is required']);
    exit;
}

$itemModel = new Item($conn);

try {
    $imagePath = $itemModel->deleteItem($itemId, $userId);
    
    if ($imagePath === false) {
        echo json_encode([
            'success' => false,
            'message' => 'Item not found or you do not have permission to delete it'
        ]);
        exit;
    }

    // Delete the image file if it exists
    if (!empty($imagePath)) {
        $fullPath = '../../' . $imagePath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Item deleted successfully'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete item: ' . $e->getMessage()
    ]);
}
