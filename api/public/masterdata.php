<?php
/**
 * Master Data API Endpoint (Public)
 * Provides categories and locations for filters
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../base.php';

// Get database connection
$db = get_db_connection();

// Get action
$action = $_GET['action'] ?? 'all';

switch ($action) {
    case 'categories':
        $stmt = $db->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        jsonSuccess($categories);
        break;

    case 'locations':
        $stmt = $db->query("SELECT location_id, location_name FROM locations ORDER BY location_name");
        $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        jsonSuccess($locations);
        break;

    case 'all':
        $stmt1 = $db->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
        $categories = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt2 = $db->query("SELECT location_id, location_name FROM locations ORDER BY location_name");
        $locations = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        jsonSuccess([
            'categories' => $categories,
            'locations' => $locations
        ]);
        break;

    default:
        jsonError('Invalid action', 400);
}
