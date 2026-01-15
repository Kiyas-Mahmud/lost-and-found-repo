<?php
/**
 * Admin Dashboard Controller
 * Handles dashboard statistics and overview
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/db.php';
requireAdmin();

class DashboardController {
    private $db;
    
    public function __construct() {
        $this->db = get_db_connection();
    }
    
    public function getStatistics() {
        $stats = [];
        
        // Pending claims count
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM claims WHERE claim_status = 'PENDING'");
        $stats['pendingClaims'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
        
        // Open reports count
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM reports WHERE report_status = 'OPEN'");
        $stats['openReports'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
        
        // Hidden posts count
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM items WHERE current_status = 'HIDDEN'");
        $stats['hiddenPosts'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
        
        // Total active posts
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM items WHERE current_status = 'OPEN'");
        $stats['activePosts'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
        
        // Total users
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'STUDENT'");
        $stats['totalUsers'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
        
        // Today's activity
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM items WHERE DATE(created_at) = CURDATE()");
        $stats['todayActivity'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
        
        // Recent activity
        $stmt = $this->db->query("
            SELECT 'post' as type, i.title as description, i.created_at as activity_time, 
                   CONCAT(u.full_name, ' posted an item') as activity_text
            FROM items i
            JOIN users u ON i.posted_by = u.user_id
            ORDER BY i.created_at DESC
            LIMIT 10
        ");
        $stats['recentActivity'] = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Recent claims
        $stmt = $this->db->query("
            SELECT 
                c.claim_id,
                c.created_at,
                i.title as item_title,
                u.full_name as claimer_name
            FROM claims c
            JOIN items i ON c.item_id = i.item_id
            JOIN users u ON c.claimed_by = u.user_id
            WHERE c.claim_status = 'PENDING'
            ORDER BY c.created_at DESC
            LIMIT 5
        ");
        $stats['recentClaims'] = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Recent reports
        $stmt = $this->db->query("
            SELECT 
                r.report_id,
                r.reason,
                r.comment,
                r.created_at,
                i.title as item_title,
                u.full_name as reporter_name
            FROM reports r
            JOIN items i ON r.item_id = i.item_id
            JOIN users u ON r.reported_by = u.user_id
            WHERE r.report_status = 'OPEN'
            ORDER BY r.created_at DESC
            LIMIT 5
        ");
        $stats['recentReports'] = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        return $stats;
    }
}

// Initialize controller
$controller = new DashboardController();
$dashboardData = $controller->getStatistics();
