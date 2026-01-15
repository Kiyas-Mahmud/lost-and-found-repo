<?php
/**
 * Admin Categories Controller
 * Handles category CRUD operations
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/db.php';
requireAdmin();

class CategoriesController {
    private $db;
    
    public function __construct() {
        $this->db = get_db_connection();
    }
    
    public function getAllCategories() {
        $stmt = $this->db->query("
            SELECT 
                c.*,
                COUNT(i.item_id) as item_count
            FROM categories c
            LEFT JOIN items i ON c.category_id = i.category_id
            GROUP BY c.category_id
            ORDER BY c.category_name ASC
        ");
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function addCategory($categoryName) {
        $stmt = $this->db->prepare("
            INSERT INTO categories (category_name, is_active) 
            VALUES (:name, 1)
        ");
        
        return $stmt->execute([':name' => $categoryName]);
    }
    
    public function toggleCategory($categoryId) {
        $stmt = $this->db->prepare("
            UPDATE categories 
            SET is_active = NOT is_active 
            WHERE category_id = :id
        ");
        
        return $stmt->execute([':id' => $categoryId]);
    }
    
    public function deleteCategory($categoryId) {
        // Check if category is in use
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM items 
            WHERE category_id = :id
        ");
        $stmt->execute([':id' => $categoryId]);
        $count = $stmt->fetch(PDO::FETCH_OBJ)->count;
        
        if ($count > 0) {
            return false; // Cannot delete category in use
        }
        
        $stmt = $this->db->prepare("DELETE FROM categories WHERE category_id = :id");
        return $stmt->execute([':id' => $categoryId]);
    }
}
