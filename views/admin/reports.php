<?php
// Load controller
require_once '../../controllers/admin/reports.php';

$pageTitle = 'Reports';

// Initialize controller
$controller = new ReportsController();

// Handle report actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $reportId = $_POST['report_id'] ?? null;
    $resolution = $_POST['resolution'] ?? '';
    
    if ($reportId && $action === 'resolve') {
        $controller->resolveReport($reportId, $resolution);
        header('Location: reports.php?msg=resolve');
        exit();
    } elseif ($reportId && $action === 'dismiss') {
        $controller->dismissReport($reportId, $resolution);
        header('Location: reports.php?msg=dismiss');
        exit();
    }
}

// Get filters from request
$filters = [
    'status' => $_GET['status'] ?? 'OPEN'
];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get data from controller
$result = $controller->getAllReports($filters, $page, 15);
$counts = $controller->getReportCounts();

$reports = $result['reports'];
$totalReports = $result['total'];
$totalPages = $result['totalPages'];
$openCount = $counts['open'];
$resolvedCount = $counts['resolved'];
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
                        <span class="badge-info"><?php echo $totalReports; ?> Total</span>
                    </div>
                </div>

                <!-- Status Tabs -->
                <div class="status-tabs">
                    <a href="reports.php?status=OPEN" 
                       class="status-tab <?php echo $filters['status'] === 'OPEN' ? 'active' : ''; ?>">
                        <span class="tab-label">Open</span>
                        <span class="tab-count"><?php echo $openCount; ?></span>
                    </a>
                    <a href="reports.php?status=RESOLVED" 
                       class="status-tab <?php echo $filters['status'] === 'RESOLVED' ? 'active' : ''; ?>">
                        <span class="tab-label">Resolved</span>
                        <span class="tab-count"><?php echo $resolvedCount; ?></span>
                    </a>
                    <a href="reports.php?status=ALL" 
                       class="status-tab <?php echo $filters['status'] === 'ALL' ? 'active' : ''; ?>">
                        <span class="tab-label">All</span>
                    </a>
                </div>

                <!-- Reports Table -->
                <div class="admin-table-container">
                    <?php if (count($reports) > 0): ?>
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
                                <tbody>
                                    <?php foreach ($reports as $report): ?>
                                        <tr>
                                            <td><span class="text-mono">#<?php echo $report->report_id; ?></span></td>
                                            <td>
                                                <div class="table-item-title">
                                                    <?php echo htmlspecialchars($report->item_title); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="report-reason"><?php echo htmlspecialchars($report->reason); ?></span>
                                            </td>
                                            <td>
                                                <div class="user-name"><?php echo htmlspecialchars($report->poster_name); ?></div>
                                            </td>
                                            <td>
                                                <div class="table-user-info">
                                                    <div class="user-name"><?php echo htmlspecialchars($report->reporter_name); ?></div>
                                                    <div class="user-email"><?php echo htmlspecialchars($report->reporter_email); ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-date">
                                                    <?php echo date('M d, Y', strtotime($report->created_at)); ?>
                                                    <span class="table-time"><?php echo date('h:i A', strtotime($report->created_at)); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php 
                                                include '../components/admin/badge.php';
                                                renderStatusBadge($report->report_status);
                                                ?>
                                            </td>
                                            <td>
                                                <div class="table-actions">
                                                    <?php if ($report->report_status === 'OPEN'): ?>
                                                        <button type="button" 
                                                                class="btn-primary-sm" 
                                                                onclick="openReviewModal(<?php echo $report->report_id; ?>, '<?php echo addslashes($report->item_title); ?>', '<?php echo addslashes($report->reason); ?>', '<?php echo addslashes($report->comment ?? ''); ?>')">
                                                            Review
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" 
                                                                class="btn-secondary-sm" 
                                                                onclick="viewDetails(<?php echo $report->report_id; ?>)">
                                                            View
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
                            $baseUrl = 'reports.php?';
                            if ($filters['status']) $baseUrl .= 'status=' . urlencode($filters['status']) . '&';
                            renderPagination($page, $totalPages, $baseUrl);
                        }
                        ?>
                    <?php else: ?>
                        <!-- Empty State -->
                        <div class="empty-state">
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Report Modal -->
    <div id="reviewModal" class="modal-overlay">
        <div class="modal modal-large">
            <div class="modal-header">
                <h3 class="modal-title">Review Report</h3>
            </div>
            <div class="modal-body">
                <div class="report-details">
                    <div class="detail-row">
                        <strong>Item:</strong>
                        <span id="modalItemTitle"></span>
                    </div>
                    <div class="detail-row">
                        <strong>Reason:</strong>
                        <span id="modalReason" class="report-reason"></span>
                    </div>
                    <div class="detail-row">
                        <strong>Description:</strong>
                        <p id="modalDescription" class="report-description"></p>
                    </div>
                </div>
                
                <form method="POST" action="" id="reportForm">
                    <div class="form-group">
                        <label for="resolution_notes">Resolution Notes (Optional)</label>
                        <textarea id="resolution_notes" 
                                  name="resolution" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Add notes about your decision..."></textarea>
                    </div>
                    <input type="hidden" name="report_id" id="modalReportId">
                    <input type="hidden" name="action" id="modalAction">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn-cancel" onclick="closeModal('reviewModal')">Cancel</button>
                <button type="button" class="btn-dismiss" onclick="submitReport('dismiss')">Dismiss Report</button>
                <button type="button" class="btn-resolve" onclick="submitReport('resolve')">Take Action & Resolve</button>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
        function openReviewModal(reportId, itemTitle, reason, description) {
            document.getElementById('modalReportId').value = reportId;
            document.getElementById('modalItemTitle').textContent = itemTitle;
            document.getElementById('modalReason').textContent = reason;
            document.getElementById('modalDescription').textContent = description || 'No additional details provided.';
            openModal('reviewModal');
        }

        function submitReport(action) {
            document.getElementById('modalAction').value = action;
            document.getElementById('reportForm').submit();
        }

        function viewDetails(reportId) {
            alert('View report details: ' + reportId);
        }
    </script>
</body>
</html>
