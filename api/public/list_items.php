<?php
/**
 * List Items API Endpoint (Public)
 * Browse, filter, and search items
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/item.php';
require_once __DIR__ . '/../base.php';

// Get database connection
$db = get_db_connection();
$itemModel = new Item($db);

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
$limit = isset($_GET['limit']) ? min(50, max(1, (int)$_GET['limit'])) : 10;
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
