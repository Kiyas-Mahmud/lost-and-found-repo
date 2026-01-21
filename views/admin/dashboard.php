<?php
require_once '../../config/helpers.php';
require_once '../../config/session.php';
requireAdmin();
$pageTitle = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="admin-layout">
        <?php include '../components/admin/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include '../components/admin/header.php'; ?>
            
            <div class="admin-content">
                <!-- Loading Spinner -->
                <div id="dashboardLoading" class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading dashboard data...</p>
                </div>

                <!-- Statistics Grid -->
                <div id="statsGrid" class="stats-grid" style="display: none;">
                    <!-- Stats will be loaded via AJAX -->
                </div>

                <!-- Recent Activity -->
                <div id="recentActivityContainer" class="admin-table-container" style="display: none;">
                    <div class="table-header">
                        <h3 class="table-title">Recent Activity</h3>
                    </div>
                    <div id="recentActivityContent">
                        <!-- Activity will be loaded via AJAX -->
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions-container">
                    <div class="section-header">
                        <h3 class="section-title">Quick Actions</h3>
                    </div>
                    <div class="quick-actions-grid">
                        <a href="pending-claims.php" class="quick-action-card">
                            <div class="quick-action-icon pending">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <div class="quick-action-content">
                                <h4>Pending Claims</h4>
                                <p>Review and process claims</p>
                            </div>
                        </a>
                        
                        <a href="posts.php" class="quick-action-card">
                            <div class="quick-action-icon posts">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                                </svg>
                            </div>
                            <div class="quick-action-content">
                                <h4>Manage Posts</h4>
                                <p>View and moderate all items</p>
                            </div>
                        </a>
                        
                        <a href="reports.php" class="quick-action-card">
                            <div class="quick-action-icon reports">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/>
                                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
                            </div>
                            <div class="quick-action-content">
                                <h4>View Reports</h4>
                                <p>Handle flagged content</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/admin/dashboard.js"></script>
</body>
</html>
