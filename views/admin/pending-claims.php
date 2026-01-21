<?php
// Check authentication
require_once '../../config/helpers.php';
require_once '../../config/session.php';
requireAdmin();

$pageTitle = 'Pending Claims';
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
                        <h2 class="page-title-large">Pending Claims</h2>
                        <p class="page-subtitle">Review and manage item claims</p>
                    </div>
                    <div class="page-actions">
                        <span class="badge-info" id="total-count">0 Total</span>
                    </div>
                </div>

                <!-- Filters & Search -->
                <?php
                $filterConfig = [
                    'searchPlaceholder' => 'Search by title or user...',
                    'filters' => [
                        [
                            'id' => 'filter-type',
                            'options' => [
                                '' => 'All Types',
                                'LOST' => 'Lost',
                                'FOUND' => 'Found'
                            ]
                        ]
                    ],
                    'showDateFilter' => false
                ];
                include '../components/admin/filter_search.php';
                ?>

                <div class="admin-table-container">

                    <!-- Loading Spinner -->
                    <div class="loading-spinner" id="loading-spinner">
                        <i class="fas fa-circle-notch fa-spin fa-3x" style="color: #ff6b6b;"></i>
                    </div>

                    <!-- Claims Table -->
                    <div id="claims-container" style="display: none;">
                        <div class="table-wrapper">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Claim ID</th>
                                        <th>Item Title</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Claimed By</th>
                                        <th>Date Claimed</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="claims-tbody">
                                    <!-- Claims will be loaded here via AJAX -->
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
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                        </div>
                        <h3 class="empty-title">No Pending Claims</h3>
                        <p class="empty-text">There are no claims waiting for review at the moment.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/admin/claims.js"></script>
</body>
</html>
