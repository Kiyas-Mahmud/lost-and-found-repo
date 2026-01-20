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

$userId = $_SESSION['user_id'];
$itemModel = new Item($conn);

// Get filters
$filters = [
    'type' => isset($_GET['type']) ? trim($_GET['type']) : '',
    'status' => isset($_GET['status']) ? trim($_GET['status']) : ''
];

try {
    $items = $itemModel->getMyItems($userId, $filters);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'items' => $items,
            'count' => count($items)
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch items: ' . $e->getMessage()
    ]);
}
