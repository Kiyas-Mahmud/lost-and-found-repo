<?php
/**
 * Admin Locations Controller
 * Handles location CRUD operations
 */

require_once __DIR__ . '/../../config/db.php';

class LocationsController {
    private $db;
    private $lastError = null;
    
    public function __construct() {
        $this->db = get_db_connection();
    }
    
    public function getLastError() {
        return $this->lastError;
    }
    
    public function getAllLocations() {
        $stmt = $this->db->query("
            SELECT 
                l.*,
                COUNT(i.item_id) as item_count
            FROM locations l
            LEFT JOIN items i ON l.location_id = i.location_id
            GROUP BY l.location_id
            ORDER BY l.location_name ASC
        ");
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function addLocation($locationName) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO locations (location_name, is_active) 
                VALUES (:name, 1)
            ");
            
            $result = $stmt->execute([':name' => $locationName]);
            return $result;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
    
    public function toggleLocation($locationId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE locations 
                SET is_active = NOT is_active 
                WHERE location_id = :id
            ");
            
            $result = $stmt->execute([':id' => $locationId]);
            return $result;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
    
    public function deleteLocation($locationId) {
        try {
            // Check if location is in use
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM items 
                WHERE location_id = :id
            ");
            $stmt->execute([':id' => $locationId]);
            $count = $stmt->fetch(PDO::FETCH_OBJ)->count;
            
            if ($count > 0) {
                $this->lastError = 'Cannot delete location that is in use';
                return false;
            }
            
            $stmt = $this->db->prepare("DELETE FROM locations WHERE location_id = :id");
            $result = $stmt->execute([':id' => $locationId]);
            return $result;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
}
