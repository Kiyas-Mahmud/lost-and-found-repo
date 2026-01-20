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

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$userId = $_SESSION['user_id'];
$claimId = isset($_POST['claim_id']) ? (int)$_POST['claim_id'] : 0;

if (!$claimId) {
    echo json_encode(['success' => false, 'message' => 'Claim ID is required']);
    exit;
}

$claimModel = new Claim($conn);

try {
    $result = $claimModel->cancelClaim($claimId, $userId);
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to cancel claim: ' . $e->getMessage()
    ]);
}
