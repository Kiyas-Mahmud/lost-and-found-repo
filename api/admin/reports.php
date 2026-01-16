<?php
require_once __DIR__ . '/../../api/base.php';
require_once __DIR__ . '/../../controllers/admin/reports.php';

// Check admin authentication
$session = checkAdmin();
$adminId = $session['user_id'];

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

$reports = new ReportsController();

// Handle GET requests (list reports or get counts)
if ($method === 'GET') {
    try {
        // Check if requesting counts
        if (isset($_GET['counts']) && $_GET['counts'] === 'true') {
            $counts = $reports->getReportCounts();
            jsonSuccess($counts, 'Report counts loaded successfully');
        } else {
            // Get filters and pagination from query params
            $filters = [
                'status' => $_GET['status'] ?? 'OPEN'
            ];
            
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 15;
            
            // Get reports
            $result = $reports->getAllReports($filters, $page, $perPage);
            
            jsonSuccess($result, 'Reports loaded successfully');
        }
        
    } catch (Exception $e) {
        jsonError('Failed to load reports: ' . $e->getMessage(), 500);
    }
}

// Handle POST requests (resolve/dismiss report)
elseif ($method === 'POST') {
    try {
        $input = getJsonInput();
        
        // Support both report_id and reportId for backwards compatibility
        $reportId = (int)($input['report_id'] ?? $input['reportId'] ?? 0);
        
        // Validate required fields
        if (empty($reportId) || empty($input['action'])) {
            jsonError('Report ID and action are required', 400);
        }
        
        $action = sanitize($input['action']);
        $resolution = sanitize($input['resolution'] ?? '');
        
        // Perform action
        if ($action === 'resolve') {
            $success = $reports->resolveReport($reportId, $resolution, $adminId);
            if ($success) {
                jsonSuccess([], 'Report resolved successfully');
            } else {
                jsonError('Failed to resolve report: ' . $reports->getLastError(), 500);
            }
        } 
        elseif ($action === 'dismiss') {
            $success = $reports->dismissReport($reportId, $resolution, $adminId);
            if ($success) {
                jsonSuccess([], 'Report dismissed successfully');
            } else {
                jsonError('Failed to dismiss report: ' . $reports->getLastError(), 500);
            }
        } 
        else {
            jsonError('Invalid action', 400);
        }
        
    } catch (Exception $e) {
        jsonError('Failed to process report action: ' . $e->getMessage(), 500);
    }
}

// Handle unsupported methods
else {
    jsonError('Method not allowed', 405);
}
