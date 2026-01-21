<?php
/**
 * Categories API
 * Get all active categories
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../base.php';

// Get database connection
$db = get_db_connection();

try {
    $query = "SELECT category_id, category_name 
              FROM categories 
              ORDER BY category_name ASC";
    
    $stmt = $db->query($query);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    jsonSuccess($categories);
} catch (Exception $e) {
    jsonError('Failed to load categories: ' . $e->getMessage(), 500);
}
