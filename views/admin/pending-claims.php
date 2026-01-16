<?php
// Check authentication
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

                <!-- Filters -->
                <div class="admin-table-container">
                    <div class="table-filters">
                        <div class="filter-form">
                            <div class="filter-group">
                                <select name="type" id="filter-type" class="filter-select">
                                    <option value="">All Types</option>
                                    <option value="LOST">Lost</option>
                                    <option value="FOUND">Found</option>
                                </select>
                                
                                <input type="text" 
                                       id="filter-search" 
                                       class="filter-input" 
                                       placeholder="Search by title or user...">
                                
                                <button id="clear-filters" class="btn-secondary" style="display: none;">Clear Filters</button>
                            </div>
                        </div>
                    </div>

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
    <script>
        let currentPage = 1;
        let currentFilters = {
            status: 'PENDING',
            type: '',
            search: ''
        };

        // Load claims on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadClaims();

            // Set up filter listeners
            document.getElementById('filter-type').addEventListener('change', (e) => {
                currentFilters.type = e.target.value;
                currentPage = 1;
                loadClaims();
                toggleClearButton();
            });

            document.getElementById('filter-search').addEventListener('input', debounce((e) => {
                currentFilters.search = e.target.value;
                currentPage = 1;
                loadClaims();
                toggleClearButton();
            }, 500));

            document.getElementById('clear-filters').addEventListener('click', () => {
                document.getElementById('filter-type').value = '';
                document.getElementById('filter-search').value = '';
                currentFilters = { status: 'PENDING', type: '', search: '' };
                currentPage = 1;
                loadClaims();
                toggleClearButton();
            });
        });

        async function loadClaims() {
            const loadingSpinner = document.getElementById('loading-spinner');
            const claimsContainer = document.getElementById('claims-container');
            const emptyState = document.getElementById('empty-state');

            // Show loading
            loadingSpinner.style.display = 'flex';
            claimsContainer.style.display = 'none';
            emptyState.style.display = 'none';

            try {
                const params = new URLSearchParams({
                    ...currentFilters,
                    page: currentPage,
                    perPage: 15
                });

                const response = await apiGet(`../../api/admin/claims.php?${params}`);

                if (response.success) {
                    const { claims, total, totalPages } = response.data;

                    // Update total count
                    document.getElementById('total-count').textContent = `${total} Total`;

                    if (claims.length > 0) {
                        renderClaimsTable(claims);
                        renderPagination(totalPages);
                        claimsContainer.style.display = 'block';
                    } else {
                        emptyState.style.display = 'flex';
                    }
                } else {
                    showToast(response.message || 'Failed to load claims', 'error');
                    emptyState.style.display = 'flex';
                }
            } catch (error) {
                console.error('Error loading claims:', error);
                showToast('Error loading claims', 'error');
                emptyState.style.display = 'flex';
            } finally {
                loadingSpinner.style.display = 'none';
            }
        }

        function renderClaimsTable(claims) {
            const tbody = document.getElementById('claims-tbody');
            tbody.innerHTML = claims.map(claim => {
                const claimDate = new Date(claim.created_at);
                const formattedDate = claimDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const formattedTime = claimDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

                const typeBadge = claim.item_type === 'LOST' 
                    ? '<span class="badge-lost">Lost</span>' 
                    : '<span class="badge-found">Found</span>';

                const statusBadge = getStatusBadge(claim.claim_status);

                return `
                    <tr>
                        <td><span class="text-mono">#${claim.claim_id}</span></td>
                        <td>
                            <div class="table-item-title">${escapeHtml(claim.title)}</div>
                        </td>
                        <td>${typeBadge}</td>
                        <td>${escapeHtml(claim.category_name)}</td>
                        <td>
                            <div class="table-user-info">
                                <div class="user-name">${escapeHtml(claim.claimer_name)}</div>
                                <div class="user-email">${escapeHtml(claim.claimer_email)}</div>
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
                                <a href="claim-review.php?id=${claim.claim_id}" class="btn-primary-sm">
                                    Review
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
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
            loadClaims();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function getStatusBadge(status) {
            switch(status) {
                case 'PENDING':
                    return '<span class="badge-warning">Pending</span>';
                case 'APPROVED':
                    return '<span class="badge-success">Approved</span>';
                case 'REJECTED':
                    return '<span class="badge-danger">Rejected</span>';
                default:
                    return '<span class="badge-secondary">' + status + '</span>';
            }
        }

        function toggleClearButton() {
            const clearBtn = document.getElementById('clear-filters');
            const hasFilters = currentFilters.type || currentFilters.search;
            clearBtn.style.display = hasFilters ? 'inline-block' : 'none';
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    </script>
</body>
</html>
