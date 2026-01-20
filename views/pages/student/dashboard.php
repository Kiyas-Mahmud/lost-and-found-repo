<!-- Student Dashboard Page -->
<div class="dashboard-page">
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Welcome back, <span id="studentName"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Student'); ?></span></p>
        </div>
        <div class="page-actions">
            <a href="?page=post_lost" class="btn btn-primary">
                <i class="fas fa-exclamation-circle"></i>
                Report Lost Item
            </a>
            <a href="?page=post_found" class="btn btn-success">
                <i class="fas fa-check-circle"></i>
                Report Found Item
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card" data-aos="fade-up" data-aos-delay="0">
            <div class="stat-icon bg-primary">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value" id="myItemsCount">--</h3>
                <p class="stat-label">My Posted Items</p>
            </div>
        </div>

        <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-icon bg-warning">
                <i class="fas fa-hand-paper"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value" id="activeClaimsCount">--</h3>
                <p class="stat-label">Active Claims</p>
            </div>
        </div>

        <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-icon bg-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value" id="approvedClaimsCount">--</h3>
                <p class="stat-label">Approved Claims</p>
            </div>
        </div>

        <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-icon bg-info">
                <i class="fas fa-bell"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value" id="notificationsCount">--</h3>
                <p class="stat-label">Unread Notifications</p>
            </div>
        </div>
    </div>

    <!-- Recent Items & Claims Section -->
    <div class="dashboard-content-grid">
        <!-- My Recent Items -->
        <div class="dashboard-section" data-aos="fade-up">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-box"></i>
                    My Recent Items
                </h2>
                <a href="?page=my_posts" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="section-body">
                <div id="recentItemsList" class="items-list">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p>Loading items...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Recent Claims -->
        <div class="dashboard-section" data-aos="fade-up" data-aos-delay="100">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-hand-paper"></i>
                    My Recent Claims
                </h2>
                <a href="?page=my_claims" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="section-body">
                <div id="recentClaimsList" class="claims-list">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p>Loading claims...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load dashboard data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
});

async function loadDashboardData() {
    try {
        const response = await apiGet('/api/student/dashboard.php');
        
        if (response.success) {
            const data = response.data;
            
            // Update statistics
            document.getElementById('myItemsCount').textContent = data.myItems || 0;
            document.getElementById('activeClaimsCount').textContent = data.activeClaims || 0;
            document.getElementById('approvedClaimsCount').textContent = data.approvedClaims || 0;
            document.getElementById('notificationsCount').textContent = data.unreadNotifications || 0;
            
            // Render recent items
            renderRecentItems(data.recentItems || []);
            
            // Render recent claims
            renderRecentClaims(data.recentClaims || []);
        }
    } catch (error) {
        console.error('Failed to load dashboard data:', error);
        showError('Failed to load dashboard data');
    }
}

function renderRecentItems(items) {
    const container = document.getElementById('recentItemsList');
    
    if (items.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No items posted yet</p>
                <a href="?page=post_lost" class="btn btn-primary btn-sm">Post Your First Item</a>
            </div>
        `;
        return;
    }
    
    container.innerHTML = items.map(item => {
        const imagePath = item.image_path ? 
            (item.image_path.startsWith('uploads/') ? `${BASE_URL}/${item.image_path}` : `${BASE_URL}/uploads/items/${item.image_path}`) : 
            `${BASE_URL}/assets/images/placeholder.svg`;
            
        const itemDate = new Date(item.event_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        
        return `
            <div class="item-list-card">
                <img src="${imagePath}" alt="${item.title}" class="item-list-image" onerror="this.src='${BASE_URL}/assets/images/placeholder.svg'">
                <div class="item-list-content">
                    <h4 class="item-list-title">${item.title}</h4>
                    <div class="item-list-meta">
                        <span class="badge ${item.item_type === 'LOST' ? 'badge-lost' : 'badge-found'}">
                            <i class="fas ${item.item_type === 'LOST' ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i>
                            ${item.item_type}
                        </span>
                        <span class="badge badge-${item.current_status.toLowerCase()}">${item.current_status}</span>
                    </div>
                    <p class="item-list-info">
                        <i class="fas fa-map-marker-alt"></i> ${item.location_name || 'Unknown'} • 
                        <i class="fas fa-calendar"></i> ${itemDate}
                    </p>
                </div>
                <a href="?page=item_details&id=${item.item_id}" class="btn btn-outline btn-sm">View</a>
            </div>
        `;
    }).join('');
}

function renderRecentClaims(claims) {
    const container = document.getElementById('recentClaimsList');
    
    if (claims.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-hand-paper"></i>
                <p>No claims submitted yet</p>
                <a href="?page=browse" class="btn btn-primary btn-sm">Browse Items to Claim</a>
            </div>
        `;
        return;
    }
    
    container.innerHTML = claims.map(claim => {
        const imagePath = claim.image_path ? 
            (claim.image_path.startsWith('uploads/') ? `${BASE_URL}/${claim.image_path}` : `${BASE_URL}/uploads/items/${claim.image_path}`) : 
            `${BASE_URL}/assets/images/placeholder.svg`;
            
        const claimDate = new Date(claim.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        
        const statusClass = {
            'PENDING': 'badge-pending',
            'APPROVED': 'badge-approved',
            'REJECTED': 'badge-rejected'
        }[claim.claim_status] || 'badge';
        
        return `
            <div class="item-list-card">
                <img src="${imagePath}" alt="${claim.item_title}" class="item-list-image" onerror="this.src='${BASE_URL}/assets/images/placeholder.svg'">
                <div class="item-list-content">
                    <h4 class="item-list-title">${claim.item_title}</h4>
                    <div class="item-list-meta">
                        <span class="badge ${claim.item_type === 'LOST' ? 'badge-lost' : 'badge-found'}">
                            <i class="fas ${claim.item_type === 'LOST' ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i>
                            ${claim.item_type}
                        </span>
                        <span class="badge ${statusClass}">${claim.claim_status}</span>
                    </div>
                    <p class="item-list-info">
                        <i class="fas fa-calendar"></i> Claimed on ${claimDate}
                    </p>
                </div>
                <a href="?page=claim_details&id=${claim.claim_id}" class="btn btn-outline btn-sm">View</a>
            </div>
        `;
    }).join('');
}

function showError(message) {
    // You can implement a toast notification here
    console.error(message);
}
</script>
