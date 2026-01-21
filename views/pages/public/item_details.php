<div class="container" style="padding: 30px 0;">
    <!-- Back Button -->
    <div style="margin-bottom: 20px;">
        <a href="?page=browse" style="color: #667eea; text-decoration: none; font-weight: 600;">
            ← Back to Browse
        </a>
    </div>

    <!-- Loading State -->
    <div id="loadingState" style="text-align: center; padding: 60px;">
        <p>Loading item details...</p>
    </div>

    <!-- Item Details -->
    <div id="itemDetails" style="display: none;">
        <div class="card">
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                    
                    <!-- Left Column - Image -->
                    <div>
                        <div id="itemImage" style="width: 100%; padding-top: 75%; position: relative; border-radius: 12px; overflow: hidden; background: #f3f4f6;">
                            <!-- Image will be inserted here -->
                        </div>
                    </div>

                    <!-- Right Column - Details -->
                    <div>
                        <div style="display: flex; gap: 10px; margin-bottom: 15px;" id="badges">
                            <!-- Badges will be inserted here -->
                        </div>

                        <h1 id="itemTitle" style="font-size: 2rem; margin-bottom: 20px; color: #2c3e50;"></h1>

                        <div style="display: grid; gap: 20px; margin-bottom: 30px;">
                            <div style="display: flex; align-items: start; gap: 12px;">
                                <div style="flex-shrink: 0; width: 40px; height: 40px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    📦
                                </div>
                                <div>
                                    <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 2px;">Category</div>
                                    <div id="itemCategory" style="font-weight: 600; color: #2c3e50;"></div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: start; gap: 12px;">
                                <div style="flex-shrink: 0; width: 40px; height: 40px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    📍
                                </div>
                                <div>
                                    <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 2px;">Location</div>
                                    <div id="itemLocation" style="font-weight: 600; color: #2c3e50;"></div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: start; gap: 12px;">
                                <div style="flex-shrink: 0; width: 40px; height: 40px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    📅
                                </div>
                                <div>
                                    <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 2px;">Event Date</div>
                                    <div id="itemDate" style="font-weight: 600; color: #2c3e50;"></div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: start; gap: 12px;">
                                <div style="flex-shrink: 0; width: 40px; height: 40px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    👤
                                </div>
                                <div>
                                    <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 2px;">Posted By</div>
                                    <div id="itemPoster" style="font-weight: 600; color: #2c3e50;"></div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: start; gap: 12px;">
                                <div style="flex-shrink: 0; width: 40px; height: 40px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    🕒
                                </div>
                                <div>
                                    <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 2px;">Posted Date</div>
                                    <div id="itemPostedDate" style="font-weight: 600; color: #2c3e50;"></div>
                                </div>
                            </div>
                        </div>

                        <div style="padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 20px;">
                            <h3 style="font-size: 1rem; margin-bottom: 10px; color: #2c3e50;">Description</h3>
                            <p id="itemDescription" style="color: #6b7280; line-height: 1.6; margin: 0;"></p>
                        </div>

                        <!-- Action Buttons -->
                        <div id="actionButtons" style="display: flex; gap: 12px;">
                            <!-- Buttons will be inserted here based on item type and status -->
                        </div>

                        <!-- Status Message -->
                        <div id="statusMessage" style="display: none; padding: 15px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px; margin-top: 20px;">
                            <p style="margin: 0; color: #92400e;"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error State -->
    <div id="errorState" style="display: none; text-align: center; padding: 60px;">
        <p style="font-size: 1.2rem; color: #ef4444; margin-bottom: 10px;">Item not found</p>
        <p style="color: #6b7280;">The item you're looking for doesn't exist or has been removed.</p>
        <a href="?page=browse" class="btn btn-primary" style="margin-top: 20px; display: inline-block;">Browse Items</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const itemId = params.get('id');
    
    if (!itemId) {
        showError();
        return;
    }
    
    loadItemDetails(itemId);
});

async function loadItemDetails(itemId) {
    try {
        const response = await apiGet(`/api/public/items.php?action=details&id=${itemId}`);
        
        if (response.success) {
            renderItemDetails(response.data);
        } else {
            showError();
        }
    } catch (error) {
        console.error('Failed to load item details:', error);
        showError();
    }
}

function renderItemDetails(item) {
    // Image
    const imagePath = item.image_path ? `${BASE_URL}/uploads/items/${item.image_path}` : `${BASE_URL}/assets/images/placeholder.jpg`;
    document.getElementById('itemImage').innerHTML = `
        <img src="${imagePath}" alt="${item.title}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
    `;

    // Badges
    const typeClass = item.item_type === 'LOST' ? 'badge-lost' : 'badge-found';
    const statusClass = {
        OPEN: 'badge-open',
        CLAIM_PENDING: 'badge-pending',
        APPROVED: 'badge-approved',
        RETURNED: 'badge-returned',
        CLOSED: 'badge-returned'
    }[item.current_status] || 'badge';
    const statusText = {
        OPEN: 'Open',
        CLAIM_PENDING: 'Pending',
        APPROVED: 'Approved',
        RETURNED: 'Returned',
        CLOSED: 'Closed'
    }[item.current_status] || item.current_status;

    document.getElementById('badges').innerHTML = `
        <span class="badge ${typeClass}">${item.item_type}</span>
        <span class="badge ${statusClass}">${statusText}</span>
    `;

    // Details
    document.getElementById('itemTitle').textContent = item.title;
    document.getElementById('itemCategory').textContent = item.category_name || 'N/A';
    document.getElementById('itemLocation').textContent = item.location_name || 'N/A';
    document.getElementById('itemDate').textContent = new Date(item.event_date).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    document.getElementById('itemPoster').textContent = item.poster_name || 'Anonymous';
    document.getElementById('itemPostedDate').textContent = new Date(item.posted_at).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    document.getElementById('itemDescription').textContent = item.description || 'No description provided.';

    // Action Buttons
    renderActionButtons(item);

    // Show details
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('itemDetails').style.display = 'block';
}

function renderActionButtons(item) {
    const buttonsContainer = document.getElementById('actionButtons');
    const statusMessage = document.getElementById('statusMessage');

    if (item.current_status === 'OPEN') {
        if (item.item_type === 'FOUND') {
            buttonsContainer.innerHTML = `
                <a href="?page=claim&item_id=${item.item_id}" class="btn btn-primary" style="flex: 1;">
                    Claim This Item
                </a>
            `;
        } else if (item.item_type === 'LOST') {
            buttonsContainer.innerHTML = `
                <a href="?page=claim&item_id=${item.item_id}" class="btn btn-primary" style="flex: 1;">
                    I Found This Item
                </a>
            `;
        }
    } else {
        statusMessage.style.display = 'block';
        const messages = {
            CLAIM_PENDING: 'This item has a pending claim. The admin is reviewing it.',
            APPROVED: 'This item claim has been approved.',
            RETURNED: 'This item has been successfully returned to its owner.',
            CLOSED: 'This case has been closed.'
        };
        statusMessage.querySelector('p').textContent = messages[item.current_status] || 'Action not available at this time.';
    }
}

function showError() {
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('errorState').style.display = 'block';
}
</script>
