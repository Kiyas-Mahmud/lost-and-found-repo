<?php
/**
 * Item Details API Endpoint (Public)
 * Get single item details
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/item.php';
require_once __DIR__ . '/../base.php';

// Get database connection
$db = get_db_connection();
$itemModel = new Item($db);

// Get item ID
$itemId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$itemId) {
    jsonError('Item ID is required', 400);
}

$item = $itemModel->getItemDetails($itemId);

if (!$item) {
    jsonError('Item not found', 404);
}

// Don't show hidden items to public
if ($item['current_status'] === 'HIDDEN') {
    jsonError('Item not available', 404);
}

jsonSuccess($item);
