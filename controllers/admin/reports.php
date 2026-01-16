<?php
/**
 * Admin Reports Controller
 * Handles report management and resolution
 */

require_once __DIR__ . '/../../config/db.php';

class ReportsController {
    private $db;
    private $lastError = null;
    
    public function __construct() {
        $this->db = get_db_connection();
    }
    
    public function getLastError() {
        return $this->lastError;
    }
    
    public function getAllReports($filters = [], $page = 1, $perPage = 15) {
        $offset = ($page - 1) * $perPage;
        $whereConditions = ["1=1"];
        $params = [];
        
        // Apply status filter
        if (!empty($filters['status']) && $filters['status'] !== 'ALL') {
            $whereConditions[] = "r.report_status = :status";
            $params[':status'] = $filters['status'];
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Get total count
        $countQuery = "SELECT COUNT(*) as total 
                       FROM reports r
                       WHERE $whereClause";
        $stmt = $this->db->prepare($countQuery);
        $stmt->execute($params);
        $totalReports = $stmt->fetch(PDO::FETCH_OBJ)->total;
        $totalPages = ceil($totalReports / $perPage);
        
        // Get reports data
        $query = "SELECT 
                    r.*,
                    i.title as item_title,
                    i.item_type,
                    reporter.full_name as reporter_name,
                    reporter.email as reporter_email,
                    poster.full_name as poster_name,
                    poster.email as poster_email
                  FROM reports r
                  JOIN items i ON r.item_id = i.item_id
                  JOIN users reporter ON r.reported_by = reporter.user_id
                  JOIN users poster ON i.posted_by = poster.user_id
                  WHERE $whereClause
                  ORDER BY r.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'reports' => $stmt->fetchAll(PDO::FETCH_OBJ),
            'total' => $totalReports,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ];
    }
    
    public function getReportCounts() {
        $counts = [];
        
        // Open reports
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM reports WHERE report_status = 'OPEN'");
        $counts['open'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
        
        // Resolved reports
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM reports WHERE report_status = 'RESOLVED'");
        $counts['resolved'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
        
        return $counts;
    }
    
    public function resolveReport($reportId, $resolution = '', $adminId = null) {
        try {
            $stmt = $this->db->prepare("
                UPDATE reports 
                SET report_status = 'RESOLVED',
                    admin_note = :resolution,
                    resolved_by = :admin_id,
                    resolved_at = NOW()
                WHERE report_id = :report_id
            ");
            
            $result = $stmt->execute([
                ':resolution' => $resolution,
                ':admin_id' => $adminId,
                ':report_id' => $reportId
            ]);
            
            return $result;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
    
    public function dismissReport($reportId, $reason = '', $adminId = null) {
        try {
            $stmt = $this->db->prepare("
                UPDATE reports 
                SET report_status = 'RESOLVED',
                    admin_note = :reason,
                    resolved_by = :admin_id,
                    resolved_at = NOW()
                WHERE report_id = :report_id
            ");
            
            $result = $stmt->execute([
                ':reason' => $reason,
                ':admin_id' => $adminId,
                ':report_id' => $reportId
            ]);
            
            return $result;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
}
