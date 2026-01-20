<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/../..');
}
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/config/helpers.php';
require_once BASE_PATH . '/config/session.php';
requireStudent();

$page_title = 'My Posts';
$page = 'my_posts';
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
                    <i class="fas fa-box"></i> My Posts
                </h1>
                <p class="page-subtitle">Manage your posted lost and found items</p>
            </div>

            <!-- Filters -->
            <div class="posts-filters">
                <div class="filters-row">
                    <div class="filter-group-inline">
                        <label class="filter-label">Item Type:</label>
                        <div class="filter-chips">
                            <button class="chip active" data-filter="type" data-value="">
                                <i class="fas fa-list"></i> All
                            </button>
                            <button class="chip" data-filter="type" data-value="LOST">
                                <i class="fas fa-search"></i> Lost Items
                            </button>
                            <button class="chip" data-filter="type" data-value="FOUND">
                                <i class="fas fa-check-circle"></i> Found Items
                            </button>
                        </div>
                    </div>

                    <div class="filter-group-inline">
                        <label class="filter-label">Status:</label>
                        <select id="statusFilter" class="filter-select">
                            <option value="">All Status</option>
                            <option value="OPEN">Open</option>
                            <option value="CLAIM_PENDING">Claim Pending</option>
                            <option value="APPROVED">Approved</option>
                            <option value="RETURNED">Returned</option>
                            <option value="CLOSED">Closed</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Items Grid -->
            <div id="postsContainer">
                <div class="loading-container">
                    <div class="spinner"></div>
                    <p>Loading your posts...</p>
                </div>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="empty-state" style="display: none;">
                <div class="empty-state-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3 class="empty-state-title">No Items Posted Yet</h3>
                <p class="empty-state-text">Start helping your community by posting lost or found items!</p>
                
            </div>
        </div>
    </main>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-backdrop">
        <div class="modal-dialog">
            <div class="modal-content-card">
                <button class="modal-close-button" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i>
                </button>
                
                <div class="modal-icon-wrapper danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                
                <h2 class="modal-heading">Confirm Delete</h2>
                <p class="modal-subtext">Are you sure you want to delete this item? This action cannot be undone.</p>
                <p class="text-muted text-center" id="deleteItemTitle"></p>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal-backdrop">
        <div class="modal-dialog">
            <div class="modal-content-card">
                <button class="modal-close-button" onclick="closeStatusModal()">
                    <i class="fas fa-times"></i>
                </button>
                
                <div class="modal-icon-wrapper info">
                    <i class="fas fa-sync-alt"></i>
                </div>
                
                <h2 class="modal-heading">Update Status</h2>
                <p class="modal-subtext">Change the status of this item</p>
                
                <div class="form-group">
                    <p class="text-muted text-center mb-3" id="statusItemTitle"></p>
                    <select id="newStatus" class="form-control">
                        <option value="OPEN">Open</option>
                        <option value="CLAIM_PENDING">Claim Pending</option>
                        <option value="APPROVED">Approved</option>
                        <option value="RETURNED">Returned</option>
                        <option value="CLOSED">Closed</option>
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeStatusModal()">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="confirmStatusUpdate()">
                        <i class="fas fa-check"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/my-posts.js"></script>
</body>
</html>
