<!-- Hero Section -->
<section class="home-hero-section">
    <div class="container">
        <div class="home-hero-grid">
            <div class="home-hero-content" data-aos="fade-right">
                <div class="home-hero-badge">
                    <i class="fas fa-university"></i>
                    <span>Campus Lost & Found</span>
                </div>
                
                <h1 class="home-hero-title">
                    Find Your Lost Items
                    <span class="home-text-gradient">Easily</span>
                </h1>
                
                <p class="home-hero-subtitle">
                    Connect with your campus community to reunite lost items with their owners. 
                    Fast, secure, and verified by university administration.
                </p>
                
                <!-- CTA Buttons -->
                <div class="home-hero-actions">
                    <a href="views/student/browse.php" class="btn btn-primary">
                        <i class="fas fa-compass"></i>
                        Browse Items
                    </a>
                </div>
                
                <!-- Trust Stats -->
                <div class="home-trust-stats">
                    <div class="home-trust-stat">
                        <div class="home-trust-value" id="heroTotalItems">--</div>
                        <div class="home-trust-label">Items Posted</div>
                    </div>
                    <div class="home-trust-divider"></div>
                    <div class="home-trust-stat">
                        <div class="home-trust-value" id="heroReturned">--</div>
                        <div class="home-trust-label">Reunited</div>
                    </div>
                    <div class="home-trust-divider"></div>
                    <div class="home-trust-stat">
                        <div class="home-trust-value">24/7</div>
                        <div class="home-trust-label">Support</div>
                    </div>
                </div>
            </div>
            
            <div class="home-hero-image" data-aos="fade-left">
                <div class="home-hero-img-wrapper">
                    <img src="https://cdn.pixabay.com/photo/2017/08/06/12/06/people-2591874_1280.jpg" 
                         alt="Students on campus" 
                         class="home-hero-img">
                    
                    <!-- Floating Badge -->
                    <div class="home-floating-badge" data-aos="zoom-in" data-aos-delay="400">
                        <div class="home-floating-icon">
                            <i class="fas fa-shield-check"></i>
                        </div>
                        <div class="home-floating-content">
                            <div class="home-floating-title">Verified</div>
                            <div class="home-floating-text">Admin Reviewed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="home-how-section">
    <div class="container">
        <div class="home-section-header" data-aos="fade-up">
            <span class="home-section-tag">
                <i class="fas fa-route"></i>
                How It Works
            </span>
            <h2 class="home-section-title">Simple 3-Step Process</h2>
            <p class="home-section-desc">Reunite with your belongings in just a few clicks</p>
        </div>
        
        <div class="home-steps-grid">
            <div class="home-step-card" data-aos="fade-up" data-aos-delay="100">
                <div class="home-step-number">01</div>
                <div class="home-step-icon home-step-icon-primary">
                    <i class="fas fa-file-edit"></i>
                </div>
                <h3 class="home-step-title">Post Your Item</h3>
                <p class="home-step-desc">
                    Lost or found something? Create a detailed post with photos, location, and description.
                </p>
            </div>
            
            <div class="home-step-arrow" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-arrow-right"></i>
            </div>
            
            <div class="home-step-card" data-aos="fade-up" data-aos-delay="300">
                <div class="home-step-number">02</div>
                <div class="home-step-icon home-step-icon-success">
                    <i class="fas fa-search-location"></i>
                </div>
                <h3 class="home-step-title">Search & Claim</h3>
                <p class="home-step-desc">
                    Browse items using filters. Found yours? Submit a claim with proof of ownership.
                </p>
            </div>
            
            <div class="home-step-arrow" data-aos="fade-up" data-aos-delay="400">
                <i class="fas fa-arrow-right"></i>
            </div>
            
            <div class="home-step-card" data-aos="fade-up" data-aos-delay="500">
                <div class="home-step-number">03</div>
                <div class="home-step-icon home-step-icon-warning">
                    <i class="fas fa-user-check"></i>
                </div>
                <h3 class="home-step-title">Get Verified</h3>
                <p class="home-step-desc">
                    Admin reviews your claim. Once approved, arrange a safe pickup on campus.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="home-stats-section">
    <div class="container">
        <div class="home-stats-grid">
            <div class="home-stat-card" data-aos="zoom-in" data-aos-delay="100">
                <div class="home-stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="home-stat-content">
                    <div class="home-stat-number" id="totalItems">--</div>
                    <div class="home-stat-label">Total Posts</div>
                </div>
            </div>
            
            <div class="home-stat-card" data-aos="zoom-in" data-aos-delay="200">
                <div class="home-stat-icon home-stat-icon-danger">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="home-stat-content">
                    <div class="home-stat-number" id="totalLost">--</div>
                    <div class="home-stat-label">Lost Items</div>
                </div>
            </div>
            
            <div class="home-stat-card" data-aos="zoom-in" data-aos-delay="300">
                <div class="home-stat-icon home-stat-icon-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="home-stat-content">
                    <div class="home-stat-number" id="totalFound">--</div>
                    <div class="home-stat-label">Found Items</div>
                </div>
            </div>
            
            <div class="home-stat-card" data-aos="zoom-in" data-aos-delay="400">
                <div class="home-stat-icon home-stat-icon-info">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="home-stat-content">
                    <div class="home-stat-number" id="totalReturned">--</div>
                    <div class="home-stat-label">Reunited</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Found Items Section -->
<section class="home-recent-section">
    <div class="container">
        <div class="home-header-flex" data-aos="fade-up">
            <div class="home-header-left">
                <span class="home-section-tag">
                    <i class="fas fa-clock"></i>
                    Latest
                </span>
                <h2 class="home-section-title">Recently Found Items</h2>
                <p class="home-section-desc">Help reunite these items with their owners</p>
            </div>
            <a href="views/student/browse.php?type=FOUND" class="btn btn-outline">
                View All
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div id="recentItemsGrid" class="home-items-grid">
            <!-- Loading State -->
            <div class="home-loading">
                <div class="spinner"></div>
                <p>Loading items...</p>
            </div>
        </div>
        
        <!-- Empty State -->
        <div id="emptyState" class="home-empty" style="display: none;">
            <div class="home-empty-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <h3>No Items Yet</h3>
            <p>Be the first to post a found item!</p>
            <a href="views/login.php" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Post Item
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="home-cta-section">
    <div class="container">
        <div class="home-cta-box" data-aos="zoom-in">
            <div class="home-cta-content">
                <h2 class="home-cta-title">Lost Something on Campus?</h2>
                <p class="home-cta-desc">
                    Post it now and let the community help you find it. It's free and takes just a minute.
                </p>
            </div>
            <div class="home-cta-actions">
                <a href="views/student/browse.php" class="home-btn-outline-white">
                    <i class="fas fa-search"></i>
                    Browse Items
                </a>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
    loadRecentItems();
    
    // Quick Search Form
    const searchForm = document.getElementById('quickSearchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const keyword = this.querySelector('[name="keyword"]').value.trim();
            window.location.href = keyword ? '?page=browse&keyword=' + encodeURIComponent(keyword) : '?page=browse';
        });
    }
});

async function loadStatistics() {
    try {
        const response = await apiGet('/api/public/home.php?action=stats');
        if (response.success) {
            document.getElementById('totalItems').textContent = response.data.total_items || 0;
            document.getElementById('totalLost').textContent = response.data.total_lost || 0;
            document.getElementById('totalFound').textContent = response.data.total_found || 0;
            document.getElementById('totalReturned').textContent = response.data.total_returned || 0;
            document.getElementById('heroTotalItems').textContent = response.data.total_items || 0;
            document.getElementById('heroReturned').textContent = response.data.total_returned || 0;
        }
    } catch (error) {
        console.error('Failed to load statistics:', error);
    }
}

async function loadRecentItems() {
    try {
        const response = await apiGet('/api/public/home.php?action=recent&limit=6');
        if (response.success && response.data.length > 0) {
            renderRecentItems(response.data);
        } else {
            showEmptyState();
        }
    } catch (error) {
        console.error('Failed to load recent items:', error);
        showEmptyState();
    }
}

function renderRecentItems(items) {
    const grid = document.getElementById('recentItemsGrid');
    grid.innerHTML = items.map(item => {
        // Handle image path - if it starts with 'uploads/', prepend BASE_URL, otherwise assume it's just the filename
        let imagePath;
        if (item.image_path) {
            if (item.image_path.startsWith('uploads/')) {
                imagePath = `${BASE_URL}/${item.image_path}`;
            } else {
                imagePath = `${BASE_URL}/uploads/items/${item.image_path}`;
            }
        } else {
            imagePath = `${BASE_URL}/assets/images/placeholder.svg`;
        }
        
        const detailsUrl = `?page=item_details&id=${item.item_id}`;
        const eventDate = new Date(item.event_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        const statusClass = {
            OPEN: 'badge-open',
            CLAIM_PENDING: 'badge-pending',
            APPROVED: 'badge-approved',
            RETURNED: 'badge-returned'
        }[item.current_status] || 'badge';
        const statusText = {
            OPEN: 'Open',
            CLAIM_PENDING: 'Pending',
            APPROVED: 'Approved',
            RETURNED: 'Returned'
        }[item.current_status] || item.current_status;
        
        return `
            <div class="item-card" data-aos="fade-up">
                <div class="item-image">
                    <img src="${imagePath}" alt="${item.title}" onerror="this.src='${BASE_URL}/assets/images/placeholder.svg'">
                    <div class="item-badges">
                        <span class="badge ${item.item_type === 'LOST' ? 'badge-lost' : 'badge-found'}">
                            <i class="fas ${item.item_type === 'LOST' ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i>
                            ${item.item_type}
                        </span>
                        <span class="badge ${statusClass}">${statusText}</span>
                    </div>
                </div>
                <div class="item-details">
                    <h3 class="item-title">${item.title}</h3>
                    <div class="item-meta">
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${item.location_name || 'Unknown Location'}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>${eventDate}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-user"></i>
                            <span>Posted by ${item.full_name || 'Anonymous'}</span>
                        </div>
                    </div>
                    <div class="item-actions">
                        <a href="${detailsUrl}" class="btn btn-primary btn-block">
                            <i class="fas fa-eye"></i>
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    // Reinitialize AOS for new elements
    if (typeof AOS !== 'undefined') {
        AOS.refresh();
    }
}

function showEmptyState() {
    document.getElementById('recentItemsGrid').style.display = 'none';
    document.getElementById('emptyState').style.display = 'block';
}
</script>
