<?php
/**
 * Locations API
 * Get all active locations
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../base.php';

// Get database connection
$db = get_db_connection();

try {
    $query = "SELECT location_id, location_name 
              FROM locations 
              ORDER BY location_name ASC";
    
    $stmt = $db->query($query);
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    jsonSuccess($locations);
} catch (Exception $e) {
    jsonError('Failed to load locations: ' . $e->getMessage(), 500);
}
