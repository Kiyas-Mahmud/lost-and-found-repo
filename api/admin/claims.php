<?php
require_once __DIR__ . '/../../api/base.php';
require_once __DIR__ . '/../../controllers/admin/claims.php';

// Check admin authentication
$session = checkAdmin();
$adminId = $session['user_id'];

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

$claims = new ClaimsController();

// Handle GET requests (list claims)
if ($method === 'GET') {
    try {
        // Get filters and pagination from query params
        $filters = [
            'status' => $_GET['status'] ?? 'PENDING',
            'type' => $_GET['type'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;
        
        // Get claims
        $result = $claims->getPendingClaims($filters, $page, $perPage);
        
        jsonSuccess($result, 'Claims loaded successfully');
        
    } catch (Exception $e) {
        jsonError('Failed to load claims: ' . $e->getMessage(), 500);
    }
}

// Handle POST requests (approve/reject claim)
elseif ($method === 'POST') {
    try {
        $input = getJsonInput();
        
        // Validate required fields
        $errors = validateRequired($input, ['claimId', 'action']);
        if (!empty($errors)) {
            jsonError('Validation failed', 400, $errors);
        }
        
        $claimId = (int)$input['claimId'];
        $action = sanitize($input['action']);
        $adminNote = sanitize($input['adminNote'] ?? '');
        
        // Perform action
        if ($action === 'approve') {
            $success = $claims->approveClaim($claimId, $adminNote, $adminId);
            if ($success) {
                jsonSuccess([], 'Claim approved successfully');
            } else {
                jsonError('Failed to approve claim: ' . $claims->getLastError(), 500);
            }
        } 
        elseif ($action === 'reject') {
            $success = $claims->rejectClaim($claimId, $adminNote, $adminId);
            if ($success) {
                jsonSuccess([], 'Claim rejected successfully');
            } else {
                jsonError('Failed to reject claim: ' . $claims->getLastError(), 500);
            }
        } 
        else {
            jsonError('Invalid action', 400);
        }
        
    } catch (Exception $e) {
        jsonError('Failed to process claim: ' . $e->getMessage(), 500);
    }
}

// Handle unsupported methods
else {
    jsonError('Method not allowed', 405);
}
