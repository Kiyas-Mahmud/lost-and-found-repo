<?php
/**
 * Admin Posts Controller
 * Handles post management (hide/unhide, search, filter)
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/db.php';
requireAdmin();

class PostsController {
    private $db;
    
    public function __construct() {
        $this->db = get_db_connection();
    }
    
    public function getAllPosts($filters = [], $page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $whereConditions = ["1=1"];
        $params = [];
        
        // Apply filters
        if (!empty($filters['type'])) {
            $whereConditions[] = "i.item_type = :type";
            $params[':type'] = $filters['type'];
        }
        
        if (!empty($filters['status'])) {
            $whereConditions[] = "i.current_status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['date'])) {
            $whereConditions[] = "DATE(i.created_at) = :date";
            $params[':date'] = $filters['date'];
        }
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(i.title LIKE :search OR i.description LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Get total count
        $countQuery = "SELECT COUNT(*) as total 
                       FROM items i
                       WHERE $whereClause";
        $stmt = $this->db->prepare($countQuery);
        $stmt->execute($params);
        $totalPosts = $stmt->fetch(PDO::FETCH_OBJ)->total;
        $totalPages = ceil($totalPosts / $perPage);
        
        // Get posts data
        $query = "SELECT 
                    i.*,
                    cat.category_name,
                    loc.location_name,
                    u.full_name as poster_name,
                    u.email as poster_email,
                    (SELECT COUNT(*) FROM claims WHERE item_id = i.item_id) as claim_count
                  FROM items i
                  JOIN categories cat ON i.category_id = cat.category_id
                  JOIN locations loc ON i.location_id = loc.location_id
                  JOIN users u ON i.posted_by = u.user_id
                  WHERE $whereClause
                  ORDER BY i.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'posts' => $stmt->fetchAll(PDO::FETCH_OBJ),
            'total' => $totalPosts,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ];
    }
    
    public function hidePost($itemId, $reason = '') {
        $stmt = $this->db->prepare("
            UPDATE items 
            SET current_status = 'HIDDEN'
            WHERE item_id = :item_id
        ");
        
        return $stmt->execute([':item_id' => $itemId]);
    }
    
    public function unhidePost($itemId) {
        $stmt = $this->db->prepare("
            UPDATE items 
            SET current_status = 'OPEN'
            WHERE item_id = :item_id
        ");
        
        return $stmt->execute([':item_id' => $itemId]);
    }
    
    public function deletePost($itemId) {
        try {
            $this->db->beginTransaction();
            
            // Delete related claims
            $stmt = $this->db->prepare("DELETE FROM claims WHERE item_id = :item_id");
            $stmt->execute([':item_id' => $itemId]);
            
            // Delete related reports
            $stmt = $this->db->prepare("DELETE FROM reports WHERE item_id = :item_id");
            $stmt->execute([':item_id' => $itemId]);
            
            // Delete the item
            $stmt = $this->db->prepare("DELETE FROM items WHERE item_id = :item_id");
            $stmt->execute([':item_id' => $itemId]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
