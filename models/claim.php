<?php
// Claim Model
class Claim {
    private $conn;
    private $table = 'claims';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get claims made by a specific user
     */
    public function getMyClaims($userId, $filters = []) {
        $query = "SELECT 
                    c.claim_id,
                    c.claim_status,
                    c.created_at,
                    c.proof_answer_1,
                    c.proof_answer_2,
                    c.proof_image_path,
                    c.admin_note,
                    c.reviewed_at,
                    i.item_id,
                    i.title,
                    i.description,
                    i.item_type,
                    i.image_path,
                    i.event_date,
                    cat.category_name,
                    loc.location_name,
                    u.full_name as poster_name
                FROM {$this->table} c
                INNER JOIN items i ON c.item_id = i.item_id
                LEFT JOIN categories cat ON i.category_id = cat.category_id
                LEFT JOIN locations loc ON i.location_id = loc.location_id
                LEFT JOIN users u ON i.posted_by = u.user_id
                WHERE c.claimed_by = ?";

        $params = [$userId];

        // Apply status filter
        if (!empty($filters['status']) && in_array($filters['status'], ['PENDING', 'APPROVED', 'REJECTED'])) {
            $query .= " AND c.claim_status = ?";
            $params[] = $filters['status'];
        }

        $query .= " ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cancel a claim (only if pending)
     */
    public function cancelClaim($claimId, $userId) {
        // First verify the claim belongs to the user and is pending
        $query = "SELECT claim_status FROM {$this->table} WHERE claim_id = ? AND claimed_by = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$claimId, $userId]);
        $claim = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$claim) {
            return ['success' => false, 'message' => 'Claim not found'];
        }

        if ($claim['claim_status'] !== 'PENDING') {
            return ['success' => false, 'message' => 'Only pending claims can be cancelled'];
        }

        // Delete the claim
        $query = "DELETE FROM {$this->table} WHERE claim_id = ? AND claimed_by = ?";
        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute([$claimId, $userId])) {
            return ['success' => true, 'message' => 'Claim cancelled successfully'];
        }

        return ['success' => false, 'message' => 'Failed to cancel claim'];
    }

    /**
     * Check if user has already claimed an item
     */
    public function hasUserClaimedItem($userId, $itemId) {
        $query = "SELECT claim_id FROM {$this->table} 
                  WHERE claimed_by = ? AND item_id = ? 
                  AND claim_status IN ('PENDING', 'APPROVED')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId, $itemId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    /**
     * Create a new claim
     */
    public function createClaim($claimData) {
        $query = "INSERT INTO {$this->table} 
                  (item_id, claimed_by, proof_answer_1, proof_answer_2, claim_status) 
                  VALUES (?, ?, ?, ?, 'PENDING')";
        
        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute([
            $claimData['item_id'],
            $claimData['claimed_by'],
            $claimData['proof_answer_1'],
            $claimData['proof_answer_2'] ?? '' // Optional second proof
        ])) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
}
?>
