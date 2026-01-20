<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/claim.php';

// Check authentication
if (!is_logged_in() || !is_student()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$userId = $_SESSION['user_id'];
$claimModel = new Claim($conn);

// Get filters
$filters = [
    'status' => isset($_GET['status']) ? trim($_GET['status']) : ''
];

try {
    $claims = $claimModel->getMyClaims($userId, $filters);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'claims' => $claims,
            'count' => count($claims)
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch claims: ' . $e->getMessage()
    ]);
}
