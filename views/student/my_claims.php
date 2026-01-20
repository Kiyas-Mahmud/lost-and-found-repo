<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/../..');
}
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/config/helpers.php';
require_once BASE_PATH . '/config/session.php';
requireStudent();

$page_title = 'My Claims';
$page = 'my_claims';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Lost and Found System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <script>
        const BASE_URL = '<?= BASE_URL ?>';
    </script>
</head>
<body>
    <?php include '../components/common/navbar_student.php'; ?>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-hand-paper"></i> My Claims
                </h1>
                <p class="page-subtitle">Track your item claim requests and their status</p>
            </div>

            <!-- Filters -->
            <div class="posts-filters">
                <div class="filters-row">
                    <div class="filter-group-inline">
                        <label class="filter-label">Status:</label>
                        <div class="filter-chips">
                            <button class="chip active" data-filter="status" data-value="">
                                <i class="fas fa-list"></i> All
                            </button>
                            <button class="chip" data-filter="status" data-value="PENDING">
                                <i class="fas fa-clock"></i> Pending
                            </button>
                            <button class="chip" data-filter="status" data-value="APPROVED">
                                <i class="fas fa-check"></i> Approved
                            </button>
                            <button class="chip" data-filter="status" data-value="REJECTED">
                                <i class="fas fa-times"></i> Rejected
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Claims Grid -->
            <div id="claimsContainer">
                <div class="loading-container">
                    <div class="spinner"></div>
                    <p>Loading your claims...</p>
                </div>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="empty-state" style="display: none;">
                <div class="empty-state-icon">
                    <i class="fas fa-hand-holding"></i>
                </div>
                <h3 class="empty-state-title">No Claims Yet</h3>
                <p class="empty-state-text">Start claiming items you've lost by browsing available found items.</p>
                
            </div>
        </div>
    </main>

    <?php include '../components/modals/cancel_claim_modal.php'; ?>
    <?php include '../components/modals/claim_details_modal.php'; ?>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/my-claims.js"></script>
</body>
</html>
