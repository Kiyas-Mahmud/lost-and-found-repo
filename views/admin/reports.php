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
    <script>
        let currentPage = 1;
        let currentStatus = 'OPEN';

        // Load reports on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadReportCounts();
            loadReports();

            // Set up tab listeners
            document.querySelectorAll('.status-tab').forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                    // Update active tab
                    document.querySelectorAll('.status-tab').forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    
                    // Load reports with new status
                    currentStatus = tab.dataset.status;
                    currentPage = 1;
                    loadReports();
                });
            });
        });

        async function loadReportCounts() {
            try {
                const response = await apiGet('../../api/admin/reports.php?counts=true');
                if (response.success) {
                    document.getElementById('count-open').textContent = response.data.open;
                    document.getElementById('count-resolved').textContent = response.data.resolved;
                }
            } catch (error) {
                console.error('Error loading report counts:', error);
            }
        }

        async function loadReports() {
            const loadingSpinner = document.getElementById('loading-spinner');
            const reportsContainer = document.getElementById('reports-container');
            const emptyState = document.getElementById('empty-state');

            // Show loading
            loadingSpinner.style.display = 'flex';
            reportsContainer.style.display = 'none';
            emptyState.style.display = 'none';

            try {
                const params = new URLSearchParams({
                    status: currentStatus,
                    page: currentPage,
                    perPage: 15
                });

                const response = await apiGet(`../../api/admin/reports.php?${params}`);

                if (response.success) {
                    const { reports, total, totalPages } = response.data;

                    // Update total count
                    document.getElementById('total-count').textContent = `${total} Total`;

                    if (reports.length > 0) {
                        renderReportsTable(reports);
                        renderPagination(totalPages);
                        reportsContainer.style.display = 'block';
                    } else {
                        emptyState.style.display = 'flex';
                    }
                } else {
                    showToast(response.message || 'Failed to load reports', 'error');
                    emptyState.style.display = 'flex';
                }
            } catch (error) {
                console.error('Error loading reports:', error);
                showToast('Error loading reports', 'error');
                emptyState.style.display = 'flex';
            } finally {
                loadingSpinner.style.display = 'none';
            }
        }

        function renderReportsTable(reports) {
            const tbody = document.getElementById('reports-tbody');
            tbody.innerHTML = reports.map(report => {
                const reportDate = new Date(report.created_at);
                const formattedDate = reportDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const formattedTime = reportDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

                const statusBadge = report.report_status === 'OPEN' 
                    ? '<span class="badge-warning">Open</span>' 
                    : '<span class="badge-success">Resolved</span>';

                const actionButton = report.report_status === 'OPEN'
                    ? `<button type="button" class="btn-primary-sm" onclick="openReviewModal(${report.report_id}, '${escapeHtml(report.item_title).replace(/'/g, "\\'")}', '${escapeHtml(report.reason)}', '${escapeHtml(report.comment || '').replace(/'/g, "\\'")}')">Review</button>`
                    : `<button type="button" class="btn-secondary-sm" disabled>Resolved</button>`;

                return `
                    <tr>
                        <td><span class="text-mono">#${report.report_id}</span></td>
                        <td>
                            <div class="table-item-title">${escapeHtml(report.item_title)}</div>
                        </td>
                        <td>
                            <span class="report-reason">${escapeHtml(report.reason)}</span>
                        </td>
                        <td>
                            <div class="user-name">${escapeHtml(report.poster_name)}</div>
                        </td>
                        <td>
                            <div class="table-user-info">
                                <div class="user-name">${escapeHtml(report.reporter_name)}</div>
                                <div class="user-email">${escapeHtml(report.reporter_email)}</div>
                            </div>
                        </td>
                        <td>
                            <div class="table-date">
                                ${formattedDate}
                                <span class="table-time">${formattedTime}</span>
                            </div>
                        </td>
                        <td>${statusBadge}</td>
                        <td>
                            <div class="table-actions">
                                ${actionButton}
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function openReviewModal(reportId, itemTitle, reason, description) {
            document.getElementById('modalReportId').value = reportId;
            document.getElementById('modalItemTitle').textContent = itemTitle;
            document.getElementById('modalReason').textContent = reason;
            document.getElementById('modalDescription').textContent = description || 'No additional details provided.';
            document.getElementById('resolution_notes').value = '';
            openModal('reviewModal');
        }

        async function handleReportAction(action) {
            const reportId = document.getElementById('modalReportId').value;
            const resolution = document.getElementById('resolution_notes').value;

            try {
                const response = await apiPost('../../api/admin/reports.php', {
                    action: action,
                    report_id: reportId,
                    resolution: resolution
                });

                if (response.success) {
                    showToast(action === 'resolve' ? 'Report resolved successfully' : 'Report dismissed successfully', 'success');
                    closeModal('reviewModal');
                    loadReportCounts();
                    loadReports();
                } else {
                    showToast(response.message || 'Failed to process report', 'error');
                }
            } catch (error) {
                console.error('Error processing report:', error);
                showToast('Error processing report', 'error');
            }
        }

        function renderPagination(totalPages) {
            const container = document.getElementById('pagination-container');
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<div class="pagination">';
            
            // Previous button
            html += `<button class="pagination-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
                <i class="fas fa-chevron-left"></i>
            </button>`;

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    html += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    html += '<span class="pagination-ellipsis">...</span>';
                }
            }

            // Next button
            html += `<button class="pagination-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
                <i class="fas fa-chevron-right"></i>
            </button>`;

            html += '</div>';
            container.innerHTML = html;
        }

        function changePage(page) {
            currentPage = page;
            loadReports();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
