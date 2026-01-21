<?php
/**
 * Home API Endpoint
 * Provides statistics and recent items for home page
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/item.php';
require_once __DIR__ . '/../base.php';

// Get database connection
$db = get_db_connection();
$itemModel = new Item($db);

// Get action
$action = $_GET['action'] ?? 'stats';

switch ($action) {
    case 'stats':
        // Get statistics
        $stats = $itemModel->getStatistics();
        jsonSuccess($stats);
        break;

    case 'recent':
        // Get recent found items
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
        $items = $itemModel->getRecentFoundItems($limit);
        jsonSuccess($items);
        break;

    default:
        jsonError('Invalid action', 400);
}
