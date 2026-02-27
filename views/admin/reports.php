<?php
// Check authentication
require_once '../../config/helpers.php';
require_once '../../config/session.php';
requireAdmin();

$pageTitle = 'Reports';
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
                <!-- Page Header -->
                <div class="page-header">
                    <div>
                        <h2 class="page-title-large">Reports Management</h2>
                        <p class="page-subtitle">Review and resolve reported content</p>
                    </div>
                    <div class="page-actions">
                        <span class="badge-info" id="total-count">0 Total</span>
                    </div>
                </div>

                <!-- Status Tabs -->
                <div class="status-tabs">
                    <a href="#" data-status="OPEN" class="status-tab active" id="tab-open">
                        <span class="tab-label">Open</span>
                        <span class="tab-count" id="count-open">0</span>
                    </a>
                    <a href="#" data-status="RESOLVED" class="status-tab" id="tab-resolved">
                        <span class="tab-label">Resolved</span>
                        <span class="tab-count" id="count-resolved">0</span>
                    </a>
                    <a href="#" data-status="ALL" class="status-tab" id="tab-all">
                        <span class="tab-label">All</span>
                    </a>
                </div>

                <!-- Filters & Search -->
                <?php
                $filterConfig = [
                    'searchPlaceholder' => 'Search reports...',
                    'filters' => [],
                    'showDateFilter' => false
                ];
                include '../components/admin/filter_search.php';
                ?>

                <!-- Reports Table -->
                <div class="admin-table-container">
                    <!-- Loading Spinner -->
                    <div class="loading-spinner" id="loading-spinner">
                        <i class="fas fa-circle-notch fa-spin fa-3x" style="color: #ff6b6b;"></i>
                    </div>

                    <div id="reports-container" style="display: none;">
                        <div class="table-wrapper">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Report ID</th>
                                        <th>Item</th>
                                        <th>Reason</th>
                                        <th>Reported User</th>
                                        <th>Reported By</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="reports-tbody">
                                    <!-- Reports will be loaded here via AJAX -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div id="pagination-container"></div>
                    </div>

                    <!-- Empty State -->
                    <div class="empty-state" id="empty-state" style="display: none;">
                        <div class="empty-icon">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                        <h3 class="empty-title">No Reports Found</h3>
                        <p class="empty-text">There are no reports in this category.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../components/modals/review_report_modal.php'; ?>

    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/admin/reports.js"></script>
</body>
</html>
