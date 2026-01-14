<?php
/**
 * Report Model
 * Handles all report-related database operations
 */

class Report {
    private $conn;
    private $table = 'reports';

    // Report properties
    public $report_id;
    public $item_id;
    public $reported_by;
    public $reason;
    public $comment;
    public $report_status;
    public $admin_note;
    public $resolved_by;
    public $resolved_at;
    public $created_at;

    /**
     * Constructor with database connection
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create new report
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO {$this->table} 
                  (item_id, reported_by, reason, comment, report_status) 
                  VALUES (:item_id, :reported_by, :reason, :comment, :report_status)";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->comment = htmlspecialchars(strip_tags($this->comment));

        // Bind parameters
        $stmt->bindParam(':item_id', $this->item_id);
        $stmt->bindParam(':reported_by', $this->reported_by);
        $stmt->bindParam(':reason', $this->reason);
        $stmt->bindParam(':comment', $this->comment);
        $stmt->bindParam(':report_status', $this->report_status);

        if ($stmt->execute()) {
            $this->report_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Get report by ID with related data
     * @param int $id
     * @return object|null
     */
    public function getById($id) {
        $query = "SELECT r.*, 
                         i.title as item_title, 
                         i.item_type,
                         i.current_status as item_status,
                         i.image_path as item_image,
                         u.full_name as reporter_name, 
                         u.email as reporter_email,
                         resolver.full_name as resolver_name
                  FROM {$this->table} r
                  LEFT JOIN items i ON r.item_id = i.item_id
                  LEFT JOIN users u ON r.reported_by = u.user_id
                  LEFT JOIN users resolver ON r.resolved_by = resolver.user_id
                  WHERE r.report_id = :report_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':report_id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get all reports with filters
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($filters = [], $limit = 20, $offset = 0) {
        $query = "SELECT r.*, 
                         i.title as item_title, 
                         i.item_type,
                         i.image_path as item_image,
                         i.current_status as item_status,
                         u.full_name as reporter_name, 
                         u.email as reporter_email
                  FROM {$this->table} r
                  LEFT JOIN items i ON r.item_id = i.item_id
                  LEFT JOIN users u ON r.reported_by = u.user_id
                  WHERE 1=1";

        // Apply filters
        if (isset($filters['report_status']) && $filters['report_status'] != '') {
            $query .= " AND r.report_status = :report_status";
        }
        if (isset($filters['reason']) && $filters['reason'] != '') {
            $query .= " AND r.reason = :reason";
        }
        if (isset($filters['reported_by']) && $filters['reported_by'] != '') {
            $query .= " AND r.reported_by = :reported_by";
        }

        $query .= " ORDER BY r.report_status ASC, r.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        if (isset($filters['report_status']) && $filters['report_status'] != '') {
            $stmt->bindParam(':report_status', $filters['report_status']);
        }
        if (isset($filters['reason']) && $filters['reason'] != '') {
            $stmt->bindParam(':reason', $filters['reason']);
        }
        if (isset($filters['reported_by']) && $filters['reported_by'] != '') {
            $stmt->bindParam(':reported_by', $filters['reported_by']);
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
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";

        if (isset($filters['report_status']) && $filters['report_status'] != '') {
            $query .= " AND report_status = :report_status";
        }
        if (isset($filters['reason']) && $filters['reason'] != '') {
            $query .= " AND reason = :reason";
        }
        if (isset($filters['reported_by']) && $filters['reported_by'] != '') {
            $query .= " AND reported_by = :reported_by";
        }

        $stmt = $this->conn->prepare($query);

        if (isset($filters['report_status']) && $filters['report_status'] != '') {
            $stmt->bindParam(':report_status', $filters['report_status']);
        }
        if (isset($filters['reason']) && $filters['reason'] != '') {
            $stmt->bindParam(':reason', $filters['reason']);
        }
        if (isset($filters['reported_by']) && $filters['reported_by'] != '') {
            $stmt->bindParam(':reported_by', $filters['reported_by']);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Check if user already reported this item
     * @param int $item_id
     * @param int $user_id
     * @return bool
     */
    public function hasUserReported($item_id, $user_id) {
        $query = "SELECT report_id FROM {$this->table} 
                  WHERE item_id = :item_id AND reported_by = :user_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':item_id', $item_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Update report status to resolved
     * @param int $resolver_id
     * @param string $admin_note
     * @return bool
     */
    public function markAsResolved($resolver_id, $admin_note = null) {
        $query = "UPDATE {$this->table} 
                  SET report_status = 'RESOLVED', 
                      resolved_by = :resolver_id, 
                      resolved_at = NOW(), 
                      admin_note = :admin_note 
                  WHERE report_id = :report_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':resolver_id', $resolver_id);
        $stmt->bindParam(':admin_note', $admin_note);
        $stmt->bindParam(':report_id', $this->report_id);

        return $stmt->execute();
    }

    /**
     * Delete report
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE report_id = :report_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':report_id', $this->report_id);

        return $stmt->execute();
    }

    /**
     * Get reports by item ID
     * @param int $item_id
     * @return array
     */
    public function getByItemId($item_id) {
        $query = "SELECT r.*, u.full_name as reporter_name, u.email as reporter_email
                  FROM {$this->table} r
                  LEFT JOIN users u ON r.reported_by = u.user_id
                  WHERE r.item_id = :item_id
                  ORDER BY r.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':item_id', $item_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
?>
