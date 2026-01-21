<div class="container" style="padding: 30px 0;">
    <div style="display: grid; grid-template-columns: 280px 1fr; gap: 30px;">
        
        <!-- Filter Sidebar -->
        <aside class="filter-sidebar">
            <div class="card" style="position: sticky; top: 20px;">
                <div class="card-header">
                    <h3 style="margin: 0; font-size: 1.25rem;">Filters</h3>
                </div>
                <div class="card-body">
                    <form id="filterForm">
                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" name="keyword" id="keyword" placeholder="Search items..." class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Item Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">All Items</option>
                                <option value="LOST">Lost</option>
                                <option value="FOUND">Found</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" id="category" class="form-control">
                                <option value="">All Categories</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Location</label>
                            <select name="location" id="location" class="form-control">
                                <option value="">All Locations</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="OPEN">Open</option>
                                <option value="CLAIM_PENDING">Pending</option>
                                <option value="APPROVED">Approved</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" id="date_from" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" id="date_to" class="form-control">
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 20px;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Apply</button>
                            <button type="button" id="clearFilters" class="btn btn-secondary">Clear</button>
                        </div>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Items Grid -->
        <main>
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="margin: 0;">Browse Items</h3>
                        <div id="resultCount" style="color: #6b7280;"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="loadingState" style="text-align: center; padding: 60px;">
                        <p>Loading items...</p>
                    </div>

                    <div id="itemsGrid" style="display: none; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 24px;"></div>

                    <div id="emptyState" style="display: none; text-align: center; padding: 60px;">
                        <p>No items found. Try adjusting your filters.</p>
                    </div>

                    <div id="paginationContainer" style="margin-top: 30px;"></div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    loadMasterData();
    loadFiltersFromURL();
    loadItems();

    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        loadItems();
    });

    document.getElementById('clearFilters').addEventListener('click', function() {
        document.getElementById('filterForm').reset();
        currentPage = 1;
        window.history.pushState({}, '', '?page=browse');
        loadItems();
    });

    let searchTimeout;
    document.getElementById('keyword').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            loadItems();
        }, 500);
    });
});

async function loadMasterData() {
    try {
        const response = await apiGet('/api/public/masterdata.php?action=all');
        if (response.success) {
            const categorySelect = document.getElementById('category');
            response.data.categories.forEach(cat => {
                categorySelect.innerHTML += `<option value="${cat.category_id}">${cat.category_name}</option>`;
            });

            const locationSelect = document.getElementById('location');
            response.data.locations.forEach(loc => {
                locationSelect.innerHTML += `<option value="${loc.location_id}">${loc.location_name}</option>`;
            });
        }
    } catch (error) {
        console.error('Failed to load master data:', error);
    }
}

function loadFiltersFromURL() {
    const params = new URLSearchParams(window.location.search);
    document.getElementById('keyword').value = params.get('keyword') || '';
    document.getElementById('type').value = params.get('type') || '';
    document.getElementById('category').value = params.get('category') || '';
    document.getElementById('location').value = params.get('location') || '';
    document.getElementById('status').value = params.get('status') || '';
    document.getElementById('date_from').value = params.get('date_from') || '';
    document.getElementById('date_to').value = params.get('date_to') || '';
    currentPage = parseInt(params.get('page')) || 1;
}

async function loadItems() {
    document.getElementById('loadingState').style.display = 'block';
    document.getElementById('itemsGrid').style.display = 'none';
    document.getElementById('emptyState').style.display = 'none';

    const filters = {
        keyword: document.getElementById('keyword').value.trim(),
        type: document.getElementById('type').value,
        category: document.getElementById('category').value,
        location: document.getElementById('location').value,
        status: document.getElementById('status').value,
        date_from: document.getElementById('date_from').value,
        date_to: document.getElementById('date_to').value,
        page: currentPage
    };

    const queryString = new URLSearchParams(Object.entries(filters).filter(([_, v]) => v)).toString();
    window.history.pushState({}, '', `?page=browse${queryString ? '&' + queryString : ''}`);

    try {
        const response = await apiGet(`/api/public/items.php?action=list&${queryString}`);
        
        if (response.success && response.data.items.length > 0) {
            renderItems(response.data.items);
            renderPagination(response.data.pagination);
            document.getElementById('resultCount').textContent = `${response.data.pagination.total_items} items found`;
            document.getElementById('loadingState').style.display = 'none';
        } else {
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('emptyState').style.display = 'block';
            document.getElementById('resultCount').textContent = '0 items found';
        }
    } catch (error) {
        console.error('Failed to load items:', error);
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('emptyState').style.display = 'block';
    }
}

function renderItems(items) {
    const grid = document.getElementById('itemsGrid');
    grid.style.display = 'grid';
    grid.innerHTML = items.map(item => {
        const imagePath = item.image_path ? `${BASE_URL}/uploads/items/${item.image_path}` : `${BASE_URL}/assets/images/placeholder.jpg`;
        const detailsUrl = `?page=item_details&id=${item.item_id}`;
        const eventDate = new Date(item.event_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        
        return `
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                <div style="position: relative; padding-top: 66.67%; overflow: hidden;">
                    <img src="${imagePath}" alt="${item.title}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                    <div style="position: absolute; top: 10px; left: 10px;">
                        <span class="badge ${item.item_type === 'LOST' ? 'badge-lost' : 'badge-found'}">${item.item_type}</span>
                    </div>
                </div>
                <div style="padding: 16px;">
                    <h3 style="font-size: 1rem; margin-bottom: 10px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${item.title}</h3>
                    <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 12px;">
                        <div>📍 ${item.location_name || 'Unknown'}</div>
                        <div>📅 ${eventDate}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="badge badge-${item.current_status.toLowerCase()}">${item.current_status}</span>
                        <a href="${detailsUrl}" class="btn btn-sm btn-primary">View</a>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function renderPagination(pagination) {
    const container = document.getElementById('paginationContainer');
    if (pagination.total_pages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '<div style="display: flex; justify-content: center; gap: 10px;">';
    
    if (pagination.current_page > 1) {
        html += `<button onclick="changePage(${pagination.current_page - 1})" class="btn btn-secondary">Previous</button>`;
    }

    for (let i = 1; i <= pagination.total_pages; i++) {
        if (i === pagination.current_page) {
            html += `<span style="padding: 8px 12px; background: #667eea; color: white; border-radius: 6px;">${i}</span>`;
        } else if (i === 1 || i === pagination.total_pages || Math.abs(i - pagination.current_page) <= 1) {
            html += `<button onclick="changePage(${i})" class="btn btn-secondary">${i}</button>`;
        } else if (Math.abs(i - pagination.current_page) === 2) {
            html += '<span style="padding: 8px 12px;">...</span>';
        }
    }

    if (pagination.current_page < pagination.total_pages) {
        html += `<button onclick="changePage(${pagination.current_page + 1})" class="btn btn-secondary">Next</button>`;
    }

    html += '</div>';
    container.innerHTML = html;
}

function changePage(page) {
    currentPage = page;
    loadItems();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
