<?php
// Load controller
require_once '../../controllers/admin/posts.php';

$pageTitle = 'All Posts';

// Initialize controller
$controller = new PostsController();

// Handle hide/unhide actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $itemId = $_POST['item_id'] ?? null;
    $reason = $_POST['reason'] ?? '';
    
    if ($itemId && $action === 'hide') {
        $controller->hidePost($itemId, $reason);
        header('Location: posts.php?msg=hide');
        exit();
    } elseif ($itemId && $action === 'unhide') {
        $controller->unhidePost($itemId);
        header('Location: posts.php?msg=unhide');
        exit();
    }
}

// Get filters from request
$filters = [
    'type' => $_GET['type'] ?? '',
    'status' => $_GET['status'] ?? '',
    'date' => $_GET['date'] ?? '',
    'search' => $_GET['search'] ?? ''
];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get data from controller
$result = $controller->getAllPosts($filters, $page, 20);

$posts = $result['posts'];
$totalPosts = $result['total'];
$totalPages = $result['totalPages'];
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
                        <span class="badge-info"><?php echo $totalPosts; ?> Total</span>
                    </div>
                </div>

                <!-- Filters & Search -->
                <div class="admin-table-container">
                    <div class="table-filters">
                        <form method="GET" class="filter-form">
                            <div class="filter-group">
                                <input type="text" 
                                       name="search" 
                                       class="filter-input search-input-full" 
                                       value="<?php echo htmlspecialchars($filters['search']); ?>"
                                       placeholder="Search by title, description, or category...">
                                
                                <select name="type" class="filter-select" onchange="this.form.submit()">
                                    <option value="">All Types</option>
                                    <option value="LOST" <?php echo $filters['type'] === 'LOST' ? 'selected' : ''; ?>>Lost</option>
                                    <option value="FOUND" <?php echo $filters['type'] === 'FOUND' ? 'selected' : ''; ?>>Found</option>
                                </select>
                                
                                <select name="status" class="filter-select" onchange="this.form.submit()">
                                    <option value="">All Status</option>
                                    <option value="OPEN" <?php echo $filters['status'] === 'OPEN' ? 'selected' : ''; ?>>Open</option>
                                    <option value="CLAIMED" <?php echo $filters['status'] === 'CLAIMED' ? 'selected' : ''; ?>>Claimed</option>
                                    <option value="RETURNED" <?php echo $filters['status'] === 'RETURNED' ? 'selected' : ''; ?>>Returned</option>
                                    <option value="HIDDEN" <?php echo $filters['status'] === 'HIDDEN' ? 'selected' : ''; ?>>Hidden</option>
                                </select>
                                
                                <input type="date" 
                                       name="date" 
                                       class="filter-input" 
                                       value="<?php echo htmlspecialchars($filters['date']); ?>"
                                       onchange="this.form.submit()">
                                
                                <button type="submit" class="btn-primary-sm">Search</button>
                                
                                <?php if ($filters['type'] || $filters['status'] || $filters['date'] || $filters['search']): ?>
                                    <a href="posts.php" class="btn-secondary-sm">Clear</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <!-- Posts Table -->
                    <?php if (count($posts) > 0): ?>
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
                                <tbody>
                                    <?php foreach ($posts as $post): ?>
                                        <tr class="<?php echo $post->current_status === 'HIDDEN' ? 'row-hidden' : ''; ?>">
                                            <td><span class="text-mono">#<?php echo $post->item_id; ?></span></td>
                                            <td>
                                                <div class="table-item-title">
                                                    <?php echo htmlspecialchars($post->title); ?>
                                                    <?php if ($post->current_status === 'HIDDEN'): ?>
                                                        <span class="hidden-indicator">🔒</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php 
                                                include '../components/admin/badge.php';
                                                renderTypeBadge($post->item_type);
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($post->category_name); ?></td>
                                            <td><?php renderStatusBadge($post->current_status); ?></td>
                                            <td>
                                                <?php if ($post->claim_count > 0): ?>
                                                    <span class="badge-warning"><?php echo $post->claim_count; ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="table-user-info">
                                                    <div class="user-name"><?php echo htmlspecialchars($post->poster_name); ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-date">
                                                    <?php echo date('M d, Y', strtotime($post->created_at)); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-actions">
                                                    <?php if ($post->current_status === 'HIDDEN'): ?>
                                                        <button type="button" 
                                                                class="btn-secondary-sm" 
                                                                onclick="unhidePost(<?php echo $post->item_id; ?>)">
                                                            Unhide
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" 
                                                                class="btn-danger-sm" 
                                                                onclick="openHideModal(<?php echo $post->item_id; ?>, '<?php echo addslashes($post->title); ?>')">
                                                            Hide
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php 
                        if ($totalPages > 1) {
                            include '../components/admin/pagination.php';
                            $baseUrl = 'posts.php?';
                            if ($filters['search']) $baseUrl .= 'search=' . urlencode($filters['search']) . '&';
                            if ($filters['type']) $baseUrl .= 'type=' . urlencode($filters['type']) . '&';
                            if ($filters['status']) $baseUrl .= 'status=' . urlencode($filters['status']) . '&';
                            if ($filters['date']) $baseUrl .= 'date=' . urlencode($filters['date']) . '&';
                            renderPagination($page, $totalPages, $baseUrl);
                        }
                        ?>
                    <?php else: ?>
                        <!-- Empty State -->
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="m21 21-4.35-4.35"/>
                                </svg>
                            </div>
                            <h3 class="empty-title">No Posts Found</h3>
                            <p class="empty-text">No items match your search criteria.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Hide Post Modal -->
    <div id="hideModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Hide Post</h3>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <p>Are you sure you want to hide "<strong id="hideItemTitle"></strong>"? This post will no longer be visible to users.</p>
                    <div class="form-group">
                        <label for="hide_reason">Reason for Hiding (Required)</label>
                        <textarea id="hide_reason" 
                                  name="reason" 
                                  class="form-control" 
                                  rows="3" 
                                  required
                                  placeholder="Explain why this post is being hidden..."></textarea>
                    </div>
                    <input type="hidden" name="action" value="hide">
                    <input type="hidden" name="item_id" id="hideItemId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn-cancel" onclick="closeModal('hideModal')">Cancel</button>
                    <button type="submit" class="modal-btn-confirm btn-danger">Hide Post</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Unhide Form (hidden) -->
    <form id="unhideForm" method="POST" action="" style="display: none;">
        <input type="hidden" name="action" value="unhide">
        <input type="hidden" name="item_id" id="unhideItemId">
    </form>

    <script src="../../assets/js/main.js"></script>
    <script>
        function openHideModal(itemId, itemTitle) {
            document.getElementById('hideItemId').value = itemId;
            document.getElementById('hideItemTitle').textContent = itemTitle;
            openModal('hideModal');
        }

        function unhidePost(itemId) {
            if (confirm('Are you sure you want to unhide this post?')) {
                document.getElementById('unhideItemId').value = itemId;
                document.getElementById('unhideForm').submit();
            }
        }
    </script>
</body>
</html>
