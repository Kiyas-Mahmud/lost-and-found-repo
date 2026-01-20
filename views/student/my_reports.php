<?php
// Set page variable for navbar
$page = 'my_reports';

// Start session and check authentication
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/../..');
}
require_once BASE_PATH . '/config/session.php';
require_once BASE_PATH . '/config/helpers.php';

// Require student authentication
requireStudent();

$userName = $_SESSION['full_name'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports - Lost & Found</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include '../components/common/navbar_student.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1><i class="fas fa-flag"></i> My Reports</h1>
                    <p>Track your submitted item reports and their status</p>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <button class="filter-tab active" data-status="all" onclick="filterReports('all', this)">
                    <i class="fas fa-list"></i> All Reports
                </button>
                <button class="filter-tab" data-status="OPEN" onclick="filterReports('OPEN', this)">
                    <i class="fas fa-clock"></i> Pending
                </button>
                <button class="filter-tab" data-status="RESOLVED" onclick="filterReports('RESOLVED', this)">
                    <i class="fas fa-check"></i> Resolved
                </button>
            </div>

            <!-- Loading State -->
            <div id="loadingState" class="loading-container" style="display: none;">
                <div class="spinner"></div>
                <p>Loading reports...</p>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Reports Grid -->
            <div id="reportsContainer" class="reports-grid">
                <!-- Reports will be loaded here dynamically -->
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="empty-state" style="display: none;">
                <div class="empty-state-icon">
                    <i class="fas fa-flag"></i>
                </div>
                <h3 class="empty-state-title">No Reports Yet</h3>
                <p class="empty-state-text">You haven't submitted any reports. If you see suspicious posts, you can report them.</p>
                
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/my-reports.js"></script>
</body>
</html>
