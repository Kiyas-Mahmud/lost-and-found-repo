<?php
/**
 * Student Dashboard API
 * Returns dashboard statistics for logged-in student
 */

require_once __DIR__ . '/../../api/base.php';
require_once __DIR__ . '/../../config/db.php';

// Check student authentication
checkStudent();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Only accept GET requests
if ($method !== 'GET') {
    jsonError('Method not allowed', 405);
}

try {
    $db = get_db_connection();
    $userId = $_SESSION['user_id'];
    $stats = [];
    
    // My posted items count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM items WHERE posted_by = ?");
    $stmt->execute([$userId]);
    $stats['myItems'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
    // My active claims count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM claims WHERE claimed_by = ? AND claim_status = 'PENDING'");
    $stmt->execute([$userId]);
    $stats['activeClaims'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
    // My approved claims count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM claims WHERE claimed_by = ? AND claim_status = 'APPROVED'");
    $stmt->execute([$userId]);
    $stats['approvedClaims'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
    // Total reports count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM reports WHERE reported_by = ?");
    $stmt->execute([$userId]);
    $stats['totalReports'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
    // Pending reports count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM reports WHERE reported_by = ? AND report_status = 'OPEN'");
    $stmt->execute([$userId]);
    $stats['pendingReports'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
    // Resolved reports count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM reports WHERE reported_by = ? AND report_status = 'RESOLVED'");
    $stmt->execute([$userId]);
    $stats['resolvedReports'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
    // My recent items
    $stmt = $db->prepare("
        SELECT 
            i.item_id,
            i.title,
            i.item_type,
            i.current_status,
            i.event_date,
            i.image_path,
            i.created_at,
            c.category_name,
            l.location_name
        FROM items i
        LEFT JOIN categories c ON i.category_id = c.category_id
        LEFT JOIN locations l ON i.location_id = l.location_id
        WHERE i.posted_by = ?
        ORDER BY i.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$userId]);
    $stats['recentItems'] = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // My recent claims
    $stmt = $db->prepare("
        SELECT 
            c.claim_id,
            c.claim_status,
            c.created_at,
            i.title as item_title,
            i.item_type,
            i.image_path
        FROM claims c
        JOIN items i ON c.item_id = i.item_id
        WHERE c.claimed_by = ?
        ORDER BY c.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$userId]);
    $stats['recentClaims'] = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // My recent reports
    $stmt = $db->prepare("
        SELECT 
            r.report_id,
            r.reason,
            r.report_status,
            r.created_at,
            i.item_id,
            i.title as item_title,
            i.item_type,
            i.image_path
        FROM reports r
        JOIN items i ON r.item_id = i.item_id
        WHERE r.reported_by = ?
        ORDER BY r.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$userId]);
    $stats['recentReports'] = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Total claims count (for the stat card)
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM claims WHERE claimed_by = ?");
    $stmt->execute([$userId]);
    $stats['totalClaims'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
    // Lost items count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM items WHERE posted_by = ? AND item_type = 'LOST'");
    $stmt->execute([$userId]);
    $stats['lostItems'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
    // Found items count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM items WHERE posted_by = ? AND item_type = 'FOUND'");
    $stmt->execute([$userId]);
    $stats['foundItems'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
    // Send success response with data
    jsonSuccess($stats, 'Dashboard data loaded successfully');
    
} catch (Exception $e) {
    jsonError('Failed to load dashboard data: ' . $e->getMessage(), 500);
}
