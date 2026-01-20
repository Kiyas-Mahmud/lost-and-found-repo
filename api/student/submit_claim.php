<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/claim.php';

// Check authentication
if (!is_logged_in() || !is_student()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in as a student to claim items']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$userId = $_SESSION['user_id'];
$itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
$proofDescription = isset($_POST['proof_description']) ? trim($_POST['proof_description']) : '';

// Validate required fields
if (!$itemId) {
    echo json_encode(['success' => false, 'message' => 'Item ID is required']);
    exit;
}

if (empty($proofDescription)) {
    echo json_encode(['success' => false, 'message' => 'Proof of ownership is required']);
    exit;
}

if (strlen($proofDescription) < 20) {
    echo json_encode(['success' => false, 'message' => 'Please provide a more detailed proof (at least 20 characters)']);
    exit;
}

$claimModel = new Claim($conn);

try {
    // Check if user has already claimed this item
    if ($claimModel->hasUserClaimedItem($userId, $itemId)) {
        echo json_encode([
            'success' => false,
            'message' => 'You have already submitted a claim for this item'
        ]);
        exit;
    }

    // Create the claim
    $claimData = [
        'item_id' => $itemId,
        'claimed_by' => $userId,
        'proof_answer_1' => $proofDescription,
        'proof_answer_2' => '' // Can be extended later for additional proof
    ];

    $claimId = $claimModel->createClaim($claimData);

    if ($claimId) {
        echo json_encode([
            'success' => true,
            'message' => 'Your claim has been submitted successfully. Please wait for admin review.',
            'claim_id' => $claimId
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to submit claim. Please try again.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
