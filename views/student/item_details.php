<?php
require_once '../../config/config.php';
require_once '../../config/helpers.php';
require_once '../../config/session.php';

// Item details can be viewed by anyone, but only logged-in students can claim
$page_title = 'Item Details';
$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$item_id) {
    header('Location: browse.php');
    exit;
}
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
</head>
<body>
    <?php 
    if (is_logged_in() && is_student()) {
        include '../components/common/navbar_student.php';
    } else {
        include '../components/common/navbar_public.php';
    }
    ?>

    <main class="main-content">
        <div class="container">
            <!-- Loading State -->
            <div id="loadingState" class="loading-container">
                <div class="spinner"></div>
                <p>Loading item details...</p>
            </div>

            <!-- Item Details Container -->
            <div id="itemDetailsContainer" style="display: none;"></div>

            <!-- Error State -->
            <div id="errorState" class="empty-state" style="display: none;">
                <div class="empty-state-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <h3 class="empty-state-title">Item Not Found</h3>
                <p class="empty-state-text">The item you're looking for doesn't exist or has been removed.</p>
                <a href="browse.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Browse
                </a>
            </div>
        </div>
    </main>

    <?php include '../components/modals/claim_modal.php'; ?>
    <?php include '../components/modals/report_modal.php'; ?>

    <script>
        const BASE_URL = '<?= BASE_URL ?>';
        const ITEM_ID = <?= $item_id ?>;
        const IS_LOGGED_IN = <?= is_logged_in() ? 'true' : 'false' ?>;
        const IS_STUDENT = <?= (is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'STUDENT') ? 'true' : 'false' ?>;
    </script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/item-details.js"></script>
</body>
</html>