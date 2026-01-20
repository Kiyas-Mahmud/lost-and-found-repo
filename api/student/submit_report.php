<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/report.php';

// Check authentication
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to report items']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$userId = $_SESSION['user_id'];
$itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

// Validate required fields
if (!$itemId) {
    echo json_encode(['success' => false, 'message' => 'Item ID is required']);
    exit;
}

if (empty($reason)) {
    echo json_encode(['success' => false, 'message' => 'Please select a reason for reporting']);
    exit;
}

// Validate reason enum
$validReasons = ['FAKE_POST', 'WRONG_INFO', 'SPAM', 'SUSPICIOUS_CLAIM', 'OTHER'];
if (!in_array($reason, $validReasons)) {
    echo json_encode(['success' => false, 'message' => 'Invalid report reason']);
    exit;
}

// If reason is OTHER, comment is required
if ($reason === 'OTHER' && empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Please provide additional details for your report']);
    exit;
}

$reportModel = new Report($conn);

try {
    // Check if user has already reported this item
    if ($reportModel->hasUserReportedItem($userId, $itemId)) {
        echo json_encode([
            'success' => false,
            'message' => 'You have already reported this item'
        ]);
        exit;
    }

    // Create the report
    $reportData = [
        'item_id' => $itemId,
        'reported_by' => $userId,
        'reason' => $reason,
        'comment' => $comment
    ];

    $result = $reportModel->createReport($reportData);

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Report submitted successfully. Our team will review it shortly.',
            'report_id' => $result['report_id']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $result['message']
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
