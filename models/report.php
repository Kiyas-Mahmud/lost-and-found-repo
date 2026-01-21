<?php
// Report Model
class Report {
    private $conn;
    private $table = 'reports';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a new report
     */
    public function createReport($data) {
        // Check if user already reported this item
        $checkQuery = "SELECT report_id FROM {$this->table} 
                       WHERE item_id = ? AND reported_by = ?";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->execute([$data['item_id'], $data['reported_by']]);
        
        if ($checkStmt->fetch()) {
            return ['success' => false, 'message' => 'You have already reported this item'];
        }

        $query = "INSERT INTO {$this->table} 
                  (item_id, reported_by, reason, comment) 
                  VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute([
            $data['item_id'],
            $data['reported_by'],
            $data['reason'],
            $data['comment'] ?? null
        ]);

        if ($result) {
            return [
                'success' => true, 
                'message' => 'Report submitted successfully',
                'report_id' => $this->conn->lastInsertId()
            ];
        }

        return ['success' => false, 'message' => 'Failed to submit report'];
    }

    /**
     * Get reports made by a specific user
     */
    public function getUserReports($userId, $filters = []) {
        $query = "SELECT 
                    r.report_id,
                    r.reason,
                    r.comment,
                    r.report_status,
                    r.admin_note,
                    r.created_at,
                    r.resolved_at,
                    i.item_id,
                    i.title,
                    i.item_type,
                    i.image_path,
                    i.current_status,
                    cat.category_name,
                    loc.location_name
                FROM {$this->table} r
                INNER JOIN items i ON r.item_id = i.item_id
                LEFT JOIN categories cat ON i.category_id = cat.category_id
                LEFT JOIN locations loc ON i.location_id = loc.location_id
                WHERE r.reported_by = ?";

        $params = [$userId];

        // Apply status filter
        if (!empty($filters['status']) && in_array($filters['status'], ['OPEN', 'RESOLVED'])) {
            $query .= " AND r.report_status = ?";
            $params[] = $filters['status'];
        }

        $query .= " ORDER BY r.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get report by ID
     */
    public function getReportById($reportId) {
        $query = "SELECT 
                    r.*,
                    i.title,
                    i.description,
                    i.item_type,
                    i.image_path,
                    i.current_status,
                    cat.category_name,
                    loc.location_name,
                    reporter.full_name as reporter_name,
                    reporter.email as reporter_email,
                    poster.full_name as poster_name
                FROM {$this->table} r
                INNER JOIN items i ON r.item_id = i.item_id
                LEFT JOIN categories cat ON i.category_id = cat.category_id
                LEFT JOIN locations loc ON i.location_id = loc.location_id
                LEFT JOIN users reporter ON r.reported_by = reporter.user_id
                LEFT JOIN users poster ON i.posted_by = poster.user_id
                WHERE r.report_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$reportId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update report status
     */
    public function updateReportStatus($reportId, $status, $adminNote = null, $adminId = null) {
        $query = "UPDATE {$this->table} 
                  SET report_status = ?,
                      admin_note = ?,
                      resolved_by = ?,
                      resolved_at = " . ($status === 'RESOLVED' ? 'NOW()' : 'NULL') . "
                  WHERE report_id = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $adminNote, $adminId, $reportId]);
    }

    /**
     * Check if user has already reported an item
     */
    public function hasUserReportedItem($userId, $itemId) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} 
                  WHERE item_id = ? AND reported_by = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$itemId, $userId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Get report count for an item
     */
    public function getItemReportCount($itemId) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} 
                  WHERE item_id = ? AND report_status = 'OPEN'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$itemId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    /**
     * Get count of reports submitted by user
     */
    public function getUserReportsCount($userId, $status = null) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE reported_by = ?";
        $params = [$userId];

        if ($status && in_array($status, ['OPEN', 'RESOLVED'])) {
            $query .= " AND report_status = ?";
            $params[] = $status;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    /**
     * Get recent reports by user
     */
    public function getRecentUserReports($userId, $limit = 5) {
        $query = "SELECT 
                    r.report_id,
                    r.reason,
                    r.comment,
                    r.report_status,
                    r.admin_note,
                    r.created_at,
                    r.resolved_at,
                    i.item_id,
                    i.title as item_title,
                    i.item_type,
                    i.image_path
                FROM {$this->table} r
                INNER JOIN items i ON r.item_id = i.item_id
                WHERE r.reported_by = ?
                ORDER BY r.created_at DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId, $limit]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
