<?php
// Load controller
require_once '../../controllers/admin/claims.php';

$pageTitle = 'Pending Claims';

// Get filters from request
$filters = [
    'type' => $_GET['type'] ?? '',
    'date' => $_GET['date'] ?? ''
];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get data from controller
$controller = new ClaimsController();
$result = $controller->getPendingClaims($filters, $page, 15);

$claims = $result['claims'];
$totalClaims = $result['total'];
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
                        <h2 class="page-title-large">Pending Claims</h2>
                        <p class="page-subtitle">Review and manage item claims</p>
                    </div>
                    <div class="page-actions">
                        <span class="badge-info"><?php echo $totalClaims; ?> Total</span>
                    </div>
                </div>

                <!-- Filters -->
                <div class="admin-table-container">
                    <div class="table-filters">
                        <form method="GET" class="filter-form">
                            <div class="filter-group">
                                <select name="type" class="filter-select" onchange="this.form.submit()">
                                    <option value="">All Types</option>
                                    <option value="LOST" <?php echo $filters['type'] === 'LOST' ? 'selected' : ''; ?>>Lost</option>
                                    <option value="FOUND" <?php echo $filters['type'] === 'FOUND' ? 'selected' : ''; ?>>Found</option>
                                </select>
                                
                                <input type="date" 
                                       name="date" 
                                       class="filter-input" 
                                       value="<?php echo htmlspecialchars($filters['date']); ?>"
                                       onchange="this.form.submit()"
                                       placeholder="Filter by date">
                                
                                <?php if ($filters['type'] || $filters['date']): ?>
                                    <a href="pending-claims.php" class="btn-secondary">Clear Filters</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <!-- Claims Table -->
                    <?php if (count($claims) > 0): ?>
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
                                <tbody>
                                    <?php foreach ($claims as $claim): ?>
                                        <tr>
                                            <td><span class="text-mono">#<?php echo $claim->claim_id; ?></span></td>
                                            <td>
                                                <div class="table-item-title">
                                                    <?php echo htmlspecialchars($claim->title); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php 
                                                include '../components/admin/badge.php';
                                                renderTypeBadge($claim->item_type);
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($claim->category_name); ?></td>
                                            <td>
                                                <div class="table-user-info">
                                                    <div class="user-name"><?php echo htmlspecialchars($claim->claimer_name); ?></div>
                                                    <div class="user-email"><?php echo htmlspecialchars($claim->claimer_email); ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-date">
                                                    <?php echo date('M d, Y', strtotime($claim->created_at)); ?>
                                                    <span class="table-time"><?php echo date('h:i A', strtotime($claim->created_at)); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php renderStatusBadge($claim->claim_status); ?>
                                            </td>
                                            <td>
                                                <div class="table-actions">
                                                    <a href="claim-review.php?id=<?php echo $claim->claim_id; ?>" class="btn-primary-sm">
                                                        Review
                                                    </a>
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
                            $baseUrl = 'pending-claims.php?';
                            if ($filterType) $baseUrl .= 'type=' . urlencode($filterType) . '&';
                            if ($filterDate) $baseUrl .= 'date=' . urlencode($filterDate) . '&';
                            renderPagination($page, $totalPages, $baseUrl);
                        }
                        ?>
                    <?php else: ?>
                        <!-- Empty State -->
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 6v6l4 2"/>
                                </svg>
                            </div>
                            <h3 class="empty-title">No Pending Claims</h3>
                            <p class="empty-text">There are no claims waiting for review at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
