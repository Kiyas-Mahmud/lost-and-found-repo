/**
 * Admin Dashboard JavaScript
 * Handles loading and rendering dashboard statistics and recent activity
 */

// Load dashboard data on page load
document.addEventListener('DOMContentLoaded', async function() {
    console.log('Dashboard: DOM loaded, starting data fetch...');
    await loadDashboardData();
});

/**
 * Load dashboard data from API
 */
async function loadDashboardData() {
    const loading = document.getElementById('dashboardLoading');
    const statsGrid = document.getElementById('statsGrid');
    const activityContainer = document.getElementById('recentActivityContainer');

    try {
        console.log('Dashboard: Showing loading spinner...');
        // Show loading
        loading.style.display = 'flex';
        statsGrid.style.display = 'none';
        activityContainer.style.display = 'none';

        console.log('Dashboard: Fetching data from API...');
        // Fetch dashboard data
        const response = await apiGet('../../api/admin/dashboard.php');
        
        console.log('Dashboard: Response received:', response);
        
        if (response.success) {
            const data = response.data;
            console.log('Dashboard: Data:', data);
            
            // Render statistics
            renderStats(data);
            
            // Render recent activity
            renderRecentActivity(data.recentActivity);
            
            // Hide loading and show content
            loading.style.display = 'none';
            statsGrid.style.display = 'grid';
            activityContainer.style.display = 'block';
            
            console.log('Dashboard: Data loaded successfully');
            showToast('Dashboard loaded successfully', 'success');
        } else {
            console.error('Dashboard: API returned error:', response.message);
            showToast(response.message || 'Failed to load dashboard data', 'error');
            loading.style.display = 'none';
        }
    } catch (error) {
        console.error('Dashboard load error:', error);
        showToast('Failed to load dashboard data: ' + error.message, 'error');
        loading.style.display = 'none';
    }
}

/**
 * Render statistics cards
 */
function renderStats(data) {
    const statsGrid = document.getElementById('statsGrid');
    const stats = [
        { icon: 'users', value: data.totalUsers, label: 'Total Users', color: 'primary' },
        { icon: 'box', value: data.activePosts, label: 'Active Posts', color: 'info' },
        { icon: 'hand-paper', value: data.pendingClaims, label: 'Pending Claims', color: 'warning' },
        { icon: 'flag', value: data.openReports, label: 'Open Reports', color: 'warning' },
        { icon: 'eye-slash', value: data.hiddenPosts, label: 'Hidden Posts', color: 'danger' },
        { icon: 'chart-line', value: data.todayActivity, label: "Today's Activity", color: 'success' }
    ];

    statsGrid.innerHTML = stats.map(stat => `
        <div class="stat-card stat-card-${stat.color}">
            <div class="stat-icon">
                <i class="fas fa-${stat.icon}"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">${stat.value}</div>
                <div class="stat-label">${stat.label}</div>
            </div>
        </div>
    `).join('');
}

/**
 * Render recent activity table
 */
function renderRecentActivity(activities) {
    const container = document.getElementById('recentActivityContent');
    
    if (!activities || activities.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <h3 class="empty-title">No Recent Activity</h3>
                <p class="empty-text">There's no recent activity to display.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = `
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Activity</th>
                    <th>Description</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                ${activities.map(activity => `
                    <tr>
                        <td>${activity.activity_text || 'N/A'}</td>
                        <td>${activity.description || 'N/A'}</td>
                        <td>${formatDateTime(activity.activity_time)}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}
