<?php
/**
 * Claim Model
 * Handles all claim-related database operations
 */

class Claim {
    private $conn;
    private $table = 'claims';

    // Claim properties
    public $claim_id;
    public $item_id;
    public $claimed_by;
    public $proof_answer_1;
    public $proof_answer_2;
    public $proof_image_path;
    public $claim_status;
    public $admin_note;
    public $reviewed_by;
    public $reviewed_at;
    public $created_at;
    public $updated_at;

    /**
     * Constructor with database connection
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create new claim
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO {$this->table} 
                  (item_id, claimed_by, proof_answer_1, proof_answer_2, proof_image_path, claim_status) 
                  VALUES (:item_id, :claimed_by, :proof_answer_1, :proof_answer_2, :proof_image_path, :claim_status)";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->proof_answer_1 = htmlspecialchars(strip_tags($this->proof_answer_1));
        $this->proof_answer_2 = htmlspecialchars(strip_tags($this->proof_answer_2));

        // Bind parameters
        $stmt->bindParam(':item_id', $this->item_id);
        $stmt->bindParam(':claimed_by', $this->claimed_by);
        $stmt->bindParam(':proof_answer_1', $this->proof_answer_1);
        $stmt->bindParam(':proof_answer_2', $this->proof_answer_2);
        $stmt->bindParam(':proof_image_path', $this->proof_image_path);
        $stmt->bindParam(':claim_status', $this->claim_status);

        if ($stmt->execute()) {
            $this->claim_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Get claim by ID with related data
     * @param int $id
     * @return object|null
     */
    public function getById($id) {
        $query = "SELECT c.*, 
                         i.title as item_title, 
                         i.item_type, 
                         i.description as item_description,
                         i.image_path as item_image,
                         i.category_id,
                         i.location_id,
                         i.event_date,
                         i.current_status as item_status,
                         cat.category_name,
                         loc.location_name,
                         u.full_name as claimant_name, 
                         u.email as claimant_email,
                         u.phone as claimant_phone,
                         reviewer.full_name as reviewer_name
                  FROM {$this->table} c
                  LEFT JOIN items i ON c.item_id = i.item_id
                  LEFT JOIN categories cat ON i.category_id = cat.category_id
                  LEFT JOIN locations loc ON i.location_id = loc.location_id
                  LEFT JOIN users u ON c.claimed_by = u.user_id
                  LEFT JOIN users reviewer ON c.reviewed_by = reviewer.user_id
                  WHERE c.claim_id = :claim_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':claim_id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get all claims with filters
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($filters = [], $limit = 10, $offset = 0) {
        $query = "SELECT c.*, 
                         i.title as item_title, 
                         i.item_type,
                         i.image_path as item_image,
                         u.full_name as claimant_name, 
                         u.email as claimant_email
                  FROM {$this->table} c
                  LEFT JOIN items i ON c.item_id = i.item_id
                  LEFT JOIN users u ON c.claimed_by = u.user_id
                  WHERE 1=1";

        // Apply filters
        if (isset($filters['claim_status']) && $filters['claim_status'] != '') {
            $query .= " AND c.claim_status = :claim_status";
        }
        if (isset($filters['claimed_by']) && $filters['claimed_by'] != '') {
            $query .= " AND c.claimed_by = :claimed_by";
        }
        if (isset($filters['item_type']) && $filters['item_type'] != '') {
            $query .= " AND i.item_type = :item_type";
        }

        $query .= " ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        if (isset($filters['claim_status']) && $filters['claim_status'] != '') {
            $stmt->bindParam(':claim_status', $filters['claim_status']);
        }
        if (isset($filters['claimed_by']) && $filters['claimed_by'] != '') {
            $stmt->bindParam(':claimed_by', $filters['claimed_by']);
        }
        if (isset($filters['item_type']) && $filters['item_type'] != '') {
            $stmt->bindParam(':item_type', $filters['item_type']);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get total count with filters
     * @param array $filters
     * @return int
     */
    public function getCount($filters = []) {
        $query = "SELECT COUNT(*) as total 
                  FROM {$this->table} c
                  LEFT JOIN items i ON c.item_id = i.item_id
                  WHERE 1=1";

        if (isset($filters['claim_status']) && $filters['claim_status'] != '') {
            $query .= " AND c.claim_status = :claim_status";
        }
        if (isset($filters['claimed_by']) && $filters['claimed_by'] != '') {
            $query .= " AND c.claimed_by = :claimed_by";
        }
        if (isset($filters['item_type']) && $filters['item_type'] != '') {
            $query .= " AND i.item_type = :item_type";
        }

        $stmt = $this->conn->prepare($query);

        if (isset($filters['claim_status']) && $filters['claim_status'] != '') {
            $stmt->bindParam(':claim_status', $filters['claim_status']);
        }
        if (isset($filters['claimed_by']) && $filters['claimed_by'] != '') {
            $stmt->bindParam(':claimed_by', $filters['claimed_by']);
        }
        if (isset($filters['item_type']) && $filters['item_type'] != '') {
            $stmt->bindParam(':item_type', $filters['item_type']);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Check if user already claimed this item
     * @param int $item_id
     * @param int $user_id
     * @return bool
     */
    public function hasUserClaimed($item_id, $user_id) {
        $query = "SELECT claim_id FROM {$this->table} 
                  WHERE item_id = :item_id AND claimed_by = :user_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':item_id', $item_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Update claim status (approve/reject)
     * @param string $status
     * @param int $reviewer_id
     * @param string $admin_note
     * @return bool
     */
    public function updateStatus($status, $reviewer_id, $admin_note = null) {
        $query = "UPDATE {$this->table} 
                  SET claim_status = :status, 
                      reviewed_by = :reviewer_id, 
                      reviewed_at = NOW(), 
                      admin_note = :admin_note 
                  WHERE claim_id = :claim_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':reviewer_id', $reviewer_id);
        $stmt->bindParam(':admin_note', $admin_note);
        $stmt->bindParam(':claim_id', $this->claim_id);

        return $stmt->execute();
    }

    /**
     * Delete claim
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE claim_id = :claim_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':claim_id', $this->claim_id);

        return $stmt->execute();
    }

    /**
     * Get claims by item ID
     * @param int $item_id
     * @return array
     */
    public function getByItemId($item_id) {
        $query = "SELECT c.*, u.full_name as claimant_name, u.email as claimant_email
                  FROM {$this->table} c
                  LEFT JOIN users u ON c.claimed_by = u.user_id
                  WHERE c.item_id = :item_id
                  ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':item_id', $item_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
?>
