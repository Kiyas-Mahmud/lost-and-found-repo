<?php
require_once __DIR__ . '/../../api/base.php';
require_once __DIR__ . '/../../config/db.php';

// Check admin authentication
checkAdmin();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Only accept GET requests
if ($method !== 'GET') {
    jsonError('Method not allowed', 405);
}

try {
    $db = get_db_connection();
    $stats = [];
    
    // Pending claims count
    $stmt = $db->query("SELECT COUNT(*) as count FROM claims WHERE claim_status = 'PENDING'");
    $stats['pendingClaims'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
    // Open reports count
    $stmt = $db->query("SELECT COUNT(*) as count FROM reports WHERE report_status = 'OPEN'");
    $stats['openReports'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
    // Hidden posts count
    $stmt = $db->query("SELECT COUNT(*) as count FROM items WHERE current_status = 'HIDDEN'");
    $stats['hiddenPosts'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
    // Active posts count
    $stmt = $db->query("SELECT COUNT(*) as count FROM items WHERE current_status != 'HIDDEN'");
    $stats['activePosts'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
    // Total users count
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'STUDENT'");
    $stats['totalUsers'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
    // Today's activity count
    $stmt = $db->query("SELECT COUNT(*) as count FROM items WHERE DATE(created_at) = CURDATE()");
    $stats['todayActivity'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
    // Recent activity
    $stmt = $db->query("
        SELECT 
            CONCAT('New ', LOWER(item_type), ' item posted') as activity_text,
            title as description,
            created_at as activity_time
        FROM items
        ORDER BY created_at DESC
        LIMIT 10
    ");
    $stats['recentActivity'] = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Send success response with data
    jsonSuccess($stats, 'Dashboard data loaded successfully');
    
} catch (Exception $e) {
    jsonError('Failed to load dashboard data: ' . $e->getMessage(), 500);
}
