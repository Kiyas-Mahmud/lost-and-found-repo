<?php
/**
 * Items API Endpoint (Public)
 * Handles browse, filter, search, and item details
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/item.php';
require_once __DIR__ . '/../base.php';

// Get database connection
$db = get_db_connection();
$itemModel = new Item($db);

// Get action
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        // Build filters from query parameters
        $filters = [
            'keyword' => $_GET['keyword'] ?? '',
            'type' => $_GET['type'] ?? '',
            'category' => $_GET['category'] ?? '',
            'location' => $_GET['location'] ?? '',
            'status' => $_GET['status'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        // Pagination
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        // Get items and count
        $items = $itemModel->getPublicItems($filters, $limit, $offset);
        $total = $itemModel->countPublicItems($filters);
        $totalPages = ceil($total / $limit);

        jsonSuccess([
            'items' => $items,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $total,
                'items_per_page' => $limit
            ]
        ]);
        break;

    case 'details':
        // Get single item details
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
        break;

    default:
        jsonError('Invalid action', 400);
}
