<?php
// Set page variable for navbar
$page = 'browse';

// Start session and check authentication
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/../..');
}
require_once BASE_PATH . '/config/session.php';
require_once BASE_PATH . '/config/helpers.php';

// Browse is public but login required for some features
if (!isset($_SESSION)) {
    session_start();
}

$userName = $_SESSION['full_name'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Items - Lost & Found</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        const BASE_URL = 'http://localhost:88/lost-and-found';
    </script>
</head>
<body>
    <?php include '../components/common/navbar_student.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>Browse Items</h1>
                <p>Search and filter lost and found items</p>
            </div>

            <!-- Search and Filter Bar -->
            <div class="filter-bar">
                <div class="search-box-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchKeyword" class="search-input" placeholder="Search by keyword, title, or description..." value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>">
                </div>

                <div class="filter-chips">
                    <div class="filter-chip-group">
                        <label class="chip-label">
                            <input type="radio" name="itemType" value="" checked>
                            <span class="chip">All Items</span>
                        </label>
                        <label class="chip-label">
                            <input type="radio" name="itemType" value="LOST">
                            <span class="chip chip-lost">Lost</span>
                        </label>
                        <label class="chip-label">
                            <input type="radio" name="itemType" value="FOUND">
                            <span class="chip chip-found">Found</span>
                        </label>
                    </div>

                    <select id="categoryFilter" class="filter-select">
                        <option value="">All Categories</option>
                    </select>

                    <select id="locationFilter" class="filter-select">
                        <option value="">All Locations</option>
                    </select>

                    <select id="statusFilter" class="filter-select">
                        <option value="">All Status</option>
                        <option value="OPEN">Open</option>
                        <option value="CLAIM_PENDING">Claim Pending</option>
                        <option value="APPROVED">Approved</option>
                    </select>

                    <button class="filter-toggle-btn" onclick="toggleAdvancedFilters()">
                        <i class="fas fa-sliders-h"></i> More Filters
                    </button>

                    <button onclick="clearFilters()" class="btn-clear-filters">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
            </div>

            <!-- Advanced Filters (Hidden by default) -->
            <div id="advancedFilters" class="advanced-filters" style="display: none;">
                <div class="date-filter-group">
                    <div class="date-filter">
                        <label>From Date:</label>
                        <input type="date" id="dateFrom" class="form-control">
                    </div>
                    <div class="date-filter">
                        <label>To Date:</label>
                        <input type="date" id="dateTo" class="form-control">
                    </div>
                </div>
            </div>

            <div class="browse-container">
                <!-- Items Grid -->
                <div class="items-content">
                    <div class="items-header">
                        <div class="items-count">
                            <span id="itemsCount">Loading...</span>
                        </div>
                    </div>

                    <div id="itemsGrid" class="items-grid">
                        <div class="loading-state">
                            <div class="spinner"></div>
                            <p>Loading items...</p>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination" class="pagination">
                        <!-- Pagination will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
        let currentPage = 1;
        const itemsPerPage = 10;

        // Load initial data
        document.addEventListener('DOMContentLoaded', function() {
            loadCategories();
            loadLocations();
            
            // Check if there's a keyword from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const keyword = urlParams.get('keyword');
            if (keyword) {
                document.getElementById('searchKeyword').value = keyword;
            }
            
            loadItems();

            // Add event listeners for all filters
            document.getElementById('searchKeyword').addEventListener('input', debounce(applyFilters, 500));
            document.querySelectorAll('input[name="itemType"]').forEach(radio => {
                radio.addEventListener('change', applyFilters);
            });
            document.getElementById('categoryFilter').addEventListener('change', applyFilters);
            document.getElementById('locationFilter').addEventListener('change', applyFilters);
            document.getElementById('statusFilter').addEventListener('change', applyFilters);
            document.getElementById('dateFrom').addEventListener('change', applyFilters);
            document.getElementById('dateTo').addEventListener('change', applyFilters);
        });

        async function loadCategories() {
            try {
                const response = await apiGet('/api/public/categories.php');
                if (response.success) {
                    const select = document.getElementById('categoryFilter');
                    response.data.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.category_id;
                        option.textContent = cat.category_name;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Failed to load categories:', error);
            }
        }

        async function loadLocations() {
            try {
                const response = await apiGet('/api/public/locations.php');
                if (response.success) {
                    const select = document.getElementById('locationFilter');
                    response.data.forEach(loc => {
                        const option = document.createElement('option');
                        option.value = loc.location_id;
                        option.textContent = loc.location_name;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Failed to load locations:', error);
            }
        }

        async function loadItems(page = 1) {
            currentPage = page;
            
            // Build query parameters
            const params = new URLSearchParams({
                page: page,
                limit: itemsPerPage,
                keyword: document.getElementById('searchKeyword').value,
                type: document.querySelector('input[name="itemType"]:checked').value,
                category: document.getElementById('categoryFilter').value,
                location: document.getElementById('locationFilter').value,
                status: document.getElementById('statusFilter').value,
                date_from: document.getElementById('dateFrom').value,
                date_to: document.getElementById('dateTo').value
            });

            // Remove empty params
            for (let [key, value] of [...params]) {
                if (!value) params.delete(key);
            }

            try {
                const response = await apiGet(`/api/public/list_items.php?${params.toString()}`);
                if (response.success) {
                    renderItems(response.data.items);
                    updateItemsCount(response.data.pagination.total_items);
                    renderPagination(response.data.pagination.total_items, page);
                }
            } catch (error) {
                console.error('Failed to load items:', error);
                showEmptyState();
            }
        }

        function renderItems(items) {
            const grid = document.getElementById('itemsGrid');
            
            if (!items || items.length === 0) {
                showEmptyState();
                return;
            }

            grid.innerHTML = items.map(item => {
                const imagePath = item.image_path 
                    ? (item.image_path.startsWith('uploads/') 
                        ? `${BASE_URL}/${item.image_path}` 
                        : `${BASE_URL}/uploads/items/${item.image_path}`)
                    : `${BASE_URL}/assets/images/placeholder.svg`;
                
                const detailsUrl = `item_details.php?id=${item.item_id}`;
                const eventDate = new Date(item.event_date).toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                });

                return `
                    <div class="item-card" data-aos="fade-up">
                        <div class="item-image">
                            <img src="${imagePath}" alt="${item.title}" onerror="this.src='${BASE_URL}/assets/images/placeholder.svg'">
                            <div class="item-badges">
                                <span class="badge badge-${item.item_type === 'LOST' ? 'lost' : 'found'}">
                                    <i class="fas fa-${item.item_type === 'LOST' ? 'exclamation-circle' : 'check-circle'}"></i>
                                    ${item.item_type}
                                </span>
                                <span class="badge badge-${getStatusClass(item.current_status)}">
                                    ${item.current_status.replace('_', ' ')}
                                </span>
                            </div>
                        </div>
                        <div class="item-details">
                            <h3 class="item-title">${item.title}</h3>
                            <div class="item-meta">
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>${item.location_name || 'Unknown'}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>${eventDate}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-tag"></i>
                                    <span>${item.category_name || 'Uncategorized'}</span>
                                </div>
                            </div>
                            <div class="item-actions">
                                <a href="${detailsUrl}" class="btn btn-primary btn-block">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            // Reinitialize AOS if available
            if (typeof AOS !== 'undefined') {
                AOS.refresh();
            }
        }

        function showEmptyState() {
            document.getElementById('itemsGrid').innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Items Found</h3>
                    <p>Try adjusting your filters or search terms</p>
                </div>
            `;
        }

        function updateItemsCount(total) {
            document.getElementById('itemsCount').textContent = `${total} item${total !== 1 ? 's' : ''} found`;
        }

        function renderPagination(total, current) {
            const totalPages = Math.ceil(total / itemsPerPage);
            const pagination = document.getElementById('pagination');
            
            // Only show pagination if total items > 10
            if (total <= 10 || totalPages <= 1) {
                pagination.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination-list">';
            
            // Previous button
            html += `<li class="${current === 1 ? 'disabled' : ''}">
                        <a href="#" onclick="loadItems(${current - 1}); return false;">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>`;
            
            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= current - 2 && i <= current + 2)) {
                    html += `<li class="${i === current ? 'active' : ''}">
                                <a href="#" onclick="loadItems(${i}); return false;">${i}</a>
                            </li>`;
                } else if (i === current - 3 || i === current + 3) {
                    html += '<li class="disabled"><span>...</span></li>';
                }
            }
            
            // Next button
            html += `<li class="${current === totalPages ? 'disabled' : ''}">
                        <a href="#" onclick="loadItems(${current + 1}); return false;">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>`;
            
            html += '</ul>';
            pagination.innerHTML = html;
        }

        function applyFilters() {
            loadItems(1);
        }

        function clearFilters() {
            document.getElementById('searchKeyword').value = '';
            document.querySelector('input[name="itemType"][value=""]').checked = true;
            document.getElementById('categoryFilter').value = '';
            document.getElementById('locationFilter').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            loadItems(1);
        }

        function getStatusClass(status) {
            const classes = {
                'OPEN': 'open',
                'CLAIM_PENDING': 'pending',
                'APPROVED': 'approved',
                'RETURNED': 'returned',
                'CLOSED': 'closed',
                'HIDDEN': 'hidden'
            };
            return classes[status] || 'default';
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

        function toggleAdvancedFilters() {
            const advancedFilters = document.getElementById('advancedFilters');
            if (advancedFilters.style.display === 'none') {
                advancedFilters.style.display = 'block';
            } else {
                advancedFilters.style.display = 'none';
            }
        }
    </script>
</body>
</html>
