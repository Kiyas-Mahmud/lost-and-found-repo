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
    
    // Unread notifications count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$userId]);
    $stats['unreadNotifications'] = $stmt->fetch(PDO::FETCH_OBJ)->count;
    
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
    
    // Send success response with data
    jsonSuccess($stats, 'Dashboard data loaded successfully');
    
} catch (Exception $e) {
    jsonError('Failed to load dashboard data: ' . $e->getMessage(), 500);
}
