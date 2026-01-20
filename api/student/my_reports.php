<?php
/**
 * My Reports API
 * Returns reports submitted by logged-in student
 */

require_once __DIR__ . '/../../api/base.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/report.php';

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
    
    // Get status filter from query params
    $status = isset($_GET['status']) ? sanitize($_GET['status']) : null;
    
    // Build filters
    $filters = [];
    if ($status && $status !== 'all' && in_array($status, ['OPEN', 'RESOLVED'])) {
        $filters['status'] = $status;
    }
    
    // Get user's reports using the Report model
    $reportModel = new Report($db);
    $reports = $reportModel->getUserReports($userId, $filters);
    
    // Send success response
    jsonSuccess([
        'reports' => $reports,
        'count' => count($reports),
        'filter' => $status ?? 'all'
    ], 'Reports loaded successfully');
    
} catch (Exception $e) {
    jsonError('Failed to load reports: ' . $e->getMessage(), 500);
}
