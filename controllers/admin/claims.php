<?php
/**
 * Admin Claims Controller
 * Handles claim review and approval/rejection
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/db.php';
requireAdmin();

class ClaimsController {
    private $db;
    
    public function __construct() {
        $this->db = get_db_connection();
    }
    
    public function getPendingClaims($filters = [], $page = 1, $perPage = 15) {
        $offset = ($page - 1) * $perPage;
        $whereConditions = ["c.claim_status = 'PENDING'"];
        $params = [];
        
        // Apply filters
        if (!empty($filters['type'])) {
            $whereConditions[] = "i.item_type = :type";
            $params[':type'] = $filters['type'];
        }
        
        if (!empty($filters['date'])) {
            $whereConditions[] = "DATE(c.created_at) = :date";
            $params[':date'] = $filters['date'];
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Get total count
        $countQuery = "SELECT COUNT(*) as total 
                       FROM claims c
                       JOIN items i ON c.item_id = i.item_id
                       WHERE $whereClause";
        $stmt = $this->db->prepare($countQuery);
        $stmt->execute($params);
        $totalClaims = $stmt->fetch(PDO::FETCH_OBJ)->total;
        $totalPages = ceil($totalClaims / $perPage);
        
        // Get claims data
        $query = "SELECT 
                    c.claim_id,
                    c.claim_status,
                    c.created_at,
                    i.item_id,
                    i.title,
                    i.item_type,
                    cat.category_name,
                    u.full_name as claimer_name,
                    u.email as claimer_email
                  FROM claims c
                  JOIN items i ON c.item_id = i.item_id
                  JOIN users u ON c.claimed_by = u.user_id
                  JOIN categories cat ON i.category_id = cat.category_id
                  WHERE $whereClause
                  ORDER BY c.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'claims' => $stmt->fetchAll(PDO::FETCH_OBJ),
            'total' => $totalClaims,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ];
    }
    
    public function getClaimDetails($claimId) {
        $query = "SELECT 
                    c.*,
                    i.title,
                    i.description as item_description,
                    i.item_type,
                    i.event_date,
                    i.image_path,
                    cat.category_name,
                    loc.location_name,
                    u.full_name as claimer_name,
                    u.email as claimer_email,
                    u.phone as claimer_phone,
                    poster.full_name as poster_name,
                    poster.email as poster_email
                  FROM claims c
                  JOIN items i ON c.item_id = i.item_id
                  JOIN users u ON c.claimed_by = u.user_id
                  JOIN users poster ON i.posted_by = poster.user_id
                  JOIN categories cat ON i.category_id = cat.category_id
                  JOIN locations loc ON i.location_id = loc.location_id
                  WHERE c.claim_id = :claim_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':claim_id' => $claimId]);
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    public function approveClaim($claimId, $adminNote = '') {
        try {
            $this->db->beginTransaction();
            
            // Update claim status
            $stmt = $this->db->prepare("
                UPDATE claims 
                SET claim_status = 'APPROVED',
                    admin_note = :admin_note,
                    reviewed_by = :admin_id,
                    reviewed_at = NOW()
                WHERE claim_id = :claim_id
            ");
            $stmt->execute([
                ':admin_note' => $adminNote,
                ':admin_id' => $_SESSION['user_id'],
                ':claim_id' => $claimId
            ]);
            
            // Update item status to RETURNED
            $stmt = $this->db->prepare("
                UPDATE items i
                JOIN claims c ON i.item_id = c.item_id
                SET i.current_status = 'RETURNED'
                WHERE c.claim_id = :claim_id
            ");
            $stmt->execute([':claim_id' => $claimId]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    public function rejectClaim($claimId, $adminNote = '') {
        $stmt = $this->db->prepare("
            UPDATE claims 
            SET claim_status = 'REJECTED',
                admin_note = :admin_note,
                reviewed_by = :admin_id,
                reviewed_at = NOW()
            WHERE claim_id = :claim_id
        ");
        
        return $stmt->execute([
            ':admin_note' => $adminNote,
            ':admin_id' => $_SESSION['user_id'],
            ':claim_id' => $claimId
        ]);
    }
}
