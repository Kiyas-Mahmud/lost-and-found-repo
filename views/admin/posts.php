<?php
// Check authentication
require_once '../../config/helpers.php';
require_once '../../config/session.php';
requireAdmin();

$pageTitle = 'All Posts';
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
                        <h2 class="page-title-large">All Posts</h2>
                        <p class="page-subtitle">Manage all lost and found items</p>
                    </div>
                    <div class="page-actions">
                        <span class="badge-info" id="total-count">0 Total</span>
                    </div>
                </div>

                <!-- Filters & Search -->
                <?php
                $filterConfig = [
                    'searchPlaceholder' => 'Search by title, description, or category...',
                    'filters' => [
                        [
                            'id' => 'filter-type',
                            'options' => [
                                '' => 'All Types',
                                'LOST' => 'Lost',
                                'FOUND' => 'Found'
                            ]
                        ],
                        [
                            'id' => 'filter-status',
                            'options' => [
                                '' => 'All Status',
                                'OPEN' => 'Open',
                                'CLAIMED' => 'Claimed',
                                'RETURNED' => 'Returned',
                                'HIDDEN' => 'Hidden'
                            ]
                        ]
                    ],
                    'showDateFilter' => true
                ];
                include '../components/admin/filter_search.php';
                ?>

                <div class="admin-table-container">

                    <!-- Loading Spinner -->
                    <div class="loading-spinner" id="loading-spinner">
                        <i class="fas fa-circle-notch fa-spin fa-3x" style="color: #ff6b6b;"></i>
                    </div>

                    <!-- Posts Table -->
                    <div id="posts-container" style="display: none;">
                        <div class="table-wrapper">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Claims</th>
                                        <th>Posted By</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="posts-tbody">
                                    <!-- Posts will be loaded here via AJAX -->
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
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                        </div>
                        <h3 class="empty-title">No Posts Found</h3>
                        <p class="empty-text">No items match your search criteria.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../components/modals/hide_post_modal.php'; ?>

    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/admin/posts.js"></script>
</body>
</html>
