<?php
// Load controller
require_once '../../controllers/admin/claims.php';

$pageTitle = 'Review Claim';
$claimId = $_GET['id'] ?? null;

if (!$claimId) {
    header('Location: pending-claims.php');
    exit();
}

// Initialize controller
$controller = new ClaimsController();

// Handle claim actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $adminNotes = $_POST['admin_notes'] ?? '';
    
    if ($action === 'approve') {
        $controller->approveClaim($claimId, $adminNotes);
        header('Location: pending-claims.php?msg=approve');
        exit();
    } elseif ($action === 'reject') {
        $controller->rejectClaim($claimId, $adminNotes);
        header('Location: pending-claims.php?msg=reject');
        exit();
    }
}

// Fetch claim details from controller
$claim = $controller->getClaimDetails($claimId);


if (!$claim) {
    header('Location: pending-claims.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="admin-layout">
        <?php include '../components/admin/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include '../components/admin/header.php'; ?>
            
            <div class="admin-content">
                <!-- Back Button -->
                <div class="page-nav">
                    <a href="pending-claims.php" class="btn-back">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                        Back to Claims
                    </a>
                </div>

                <!-- Page Header -->
                <div class="page-header">
                    <div>
                        <h2 class="page-title-large">Review Claim #<?php echo $claim->claim_id; ?></h2>
                        <p class="page-subtitle">Carefully review the claim details before making a decision</p>
                    </div>
                    <div class="page-actions">
                        <?php 
                        include '../components/admin/badge.php';
                        renderStatusBadge($claim->claim_status);
                        ?>
                    </div>
                </div>

                <!-- 2-Column Layout -->
                <div class="claim-review-container">
                    <!-- Left Column - Item Details -->
                    <div class="review-column">
                        <div class="review-card">
                            <h3 class="review-card-title">Item Details</h3>
                            
                            <?php if ($claim->image_path): ?>
                                <div class="item-image-container">
                                    <img src="../../<?php echo htmlspecialchars($claim->image_path); ?>" 
                                         alt="Item Image" 
                                         class="item-image">
                                </div>
                            <?php endif; ?>
                            
                            <div class="review-info-group">
                                <label class="review-label">Item Title</label>
                                <div class="review-value"><?php echo htmlspecialchars($claim->item_title); ?></div>
                            </div>

                            <div class="review-info-group">
                                <label class="review-label">Type & Category</label>
                                <div class="review-badges">
                                    <?php renderTypeBadge($claim->item_type); ?>
                                    <span class="badge-secondary"><?php echo htmlspecialchars($claim->category_name); ?></span>
                                </div>
                            </div>

                            <div class="review-info-group">
                                <label class="review-label">Description</label>
                                <div class="review-value"><?php echo nl2br(htmlspecialchars($claim->item_description)); ?></div>
                            </div>

                            <div class="review-info-group">
                                <label class="review-label">Location Found</label>
                                <div class="review-value"><?php echo htmlspecialchars($claim->location_name); ?></div>
                            </div>

                            <div class="review-info-group">
                                <label class="review-label">Date Found</label>
                                <div class="review-value"><?php echo date('F d, Y', strtotime($claim->event_date)); ?></div>
                            </div>

                            <div class="review-divider"></div>

                            <h4 class="review-section-title">Posted By</h4>
                            <div class="review-user-card">
                                <div class="user-avatar-lg"><?php echo strtoupper(substr($claim->poster_name, 0, 1)); ?></div>
                                <div class="user-details">
                                    <div class="user-name-lg"><?php echo htmlspecialchars($claim->poster_name); ?></div>
                                    <div class="user-meta"><?php echo htmlspecialchars($claim->poster_student_id); ?></div>
                                    <div class="user-meta"><?php echo htmlspecialchars($claim->poster_email); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Claim Details -->
                    <div class="review-column">
                        <div class="review-card">
                            <h3 class="review-card-title">Claim Information</h3>
                            
                            <div class="review-info-group">
                                <label class="review-label">Claim ID</label>
                                <div class="review-value text-mono">#<?php echo $claim->claim_id; ?></div>
                            </div>

                            <div class="review-info-group">
                                <label class="review-label">Submitted On</label>
                                <div class="review-value"><?php echo date('F d, Y \a\t h:i A', strtotime($claim->created_at)); ?></div>
                            </div>

                            <div class="review-divider"></div>

                            <h4 class="review-section-title">Claimed By</h4>
                            <div class="review-user-card">
                                <div class="user-avatar-lg"><?php echo strtoupper(substr($claim->claimer_name, 0, 1)); ?></div>
                                <div class="user-details">
                                    <div class="user-name-lg"><?php echo htmlspecialchars($claim->claimer_name); ?></div>
                                    <div class="user-meta"><?php echo htmlspecialchars($claim->claimer_student_id); ?></div>
                                    <div class="user-meta"><?php echo htmlspecialchars($claim->claimer_email); ?></div>
                                    <?php if ($claim->phone): ?>
                                        <div class="user-meta">📱 <?php echo htmlspecialchars($claim->phone); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="review-divider"></div>

                            <div class="review-info-group">
                                <label class="review-label">Verification Details</label>
                                <div class="review-value"><?php echo nl2br(htmlspecialchars($claim->proof_answer_1)); ?></div>
                            </div>

                            <?php if ($claim->proof_answer_2): ?>
                                <div class="review-info-group">
                                    <label class="review-label">Additional Proof</label>
                                    <div class="review-value review-highlight"><?php echo htmlspecialchars($claim->proof_answer_2); ?></div>
                                </div>
                            <?php endif; ?>

                            <?php if ($claim->claim_status === 'PENDING'): ?>
                                <div class="review-divider"></div>
                                
                                <!-- Action Form -->
                                <div class="review-actions">
                                    <h4 class="review-section-title">Decision</h4>
                                    
                                    <div class="action-buttons">
                                        <button type="button" 
                                                class="btn-approve" 
                                                onclick="openModal('approveModal')">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                <polyline points="22 4 12 14.01 9 11.01"/>
                                            </svg>
                                            Approve Claim
                                        </button>
                                        
                                        <button type="button" 
                                                class="btn-reject" 
                                                onclick="openModal('rejectModal')">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="15" y1="9" x2="9" y2="15"/>
                                                <line x1="9" y1="9" x2="15" y2="15"/>
                                            </svg>
                                            Reject Claim
                                        </button>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="review-info-group">
                                    <label class="review-label">Admin Notes</label>
                                    <div class="review-value"><?php echo nl2br(htmlspecialchars($claim->admin_note ?? 'No notes')); ?></div>
                                </div>
                                
                                <div class="review-info-group">
                                    <label class="review-label">Reviewed At</label>
                                    <div class="review-value"><?php echo date('F d, Y \a\t h:i A', strtotime($claim->reviewed_at)); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Approve Claim</h3>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <p>Are you sure you want to approve this claim? This will mark the item as returned to the claimer.</p>
                    <div class="form-group">
                        <label for="approve_notes">Admin Notes (Optional)</label>
                        <textarea id="approve_notes" 
                                  name="admin_notes" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Add any notes about this decision..."></textarea>
                    </div>
                    <input type="hidden" name="action" value="approve">
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn-cancel" onclick="closeModal('approveModal')">Cancel</button>
                    <button type="submit" class="modal-btn-confirm">Approve Claim</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Reject Claim</h3>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <p>Are you sure you want to reject this claim? The item will remain available for other claims.</p>
                    <div class="form-group">
                        <label for="reject_notes">Reason for Rejection (Optional)</label>
                        <textarea id="reject_notes" 
                                  name="admin_notes" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Explain why this claim is being rejected..."></textarea>
                    </div>
                    <input type="hidden" name="action" value="reject">
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn-cancel" onclick="closeModal('rejectModal')">Cancel</button>
                    <button type="submit" class="modal-btn-confirm btn-danger">Reject Claim</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
