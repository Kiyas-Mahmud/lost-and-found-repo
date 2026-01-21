/**
 * Item Details Page JavaScript
 * Handles loading and displaying item details, and claim submission
 */

let itemData = null;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    loadItemDetails();
    setupClaimForm();
});

/**
 * Load item details from API
 */
async function loadItemDetails() {
    const loadingState = document.getElementById('loadingState');
    const errorState = document.getElementById('errorState');
    const detailsContainer = document.getElementById('itemDetailsContainer');
    
    console.log('Starting to load item details for ID:', ITEM_ID);
    
    try {
        const response = await apiGet(`/api/public/item_details.php?id=${ITEM_ID}`);
        
        console.log('API Response:', response);
        
        if (response.success && response.data) {
            itemData = response.data;
            console.log('Item data loaded:', itemData);
            loadingState.style.display = 'none';
            errorState.style.display = 'none';
            detailsContainer.style.display = 'block';
            displayItemDetails(itemData);
        } else {
            console.error('API returned no data or unsuccessful');
            loadingState.style.display = 'none';
            errorState.style.display = 'block';
        }
    } catch (error) {
        console.error('Error loading item:', error);
        loadingState.style.display = 'none';
        errorState.style.display = 'block';
    }
}

/**
 * Display item details on the page
 */
function displayItemDetails(item) {
    console.log('displayItemDetails called with:', item);
    
    const imagePath = item.image_path ? `../../${item.image_path}` : null;
    const typeClass = item.item_type.toLowerCase();
    
    const eventDate = new Date(item.event_date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const postedDate = new Date(item.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const posterInitial = item.poster_name ? item.poster_name.charAt(0).toUpperCase() : 'U';
    
    // Check if user can claim
    const canClaim = IS_LOGGED_IN && IS_STUDENT && item.current_status === 'OPEN';
    
    const html = `
        <div class="item-details-page">
            <div class="item-detail-card" data-aos="fade-up">
                <div class="item-detail-image">
                    ${imagePath 
                        ? `<img src="${imagePath}" alt="${escapeHtml(item.title)}" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'no-image-placeholder\\'><i class=\\'fas fa-image\\'></i><span>Image not available</span></div>';">`
                        : `<div class="no-image-placeholder">
                             <i class="fas fa-image"></i>
                             <span>No Image Available</span>
                           </div>`
                    }
                    <span class="item-type-badge-absolute ${typeClass}">
                        <i class="fas fa-${item.item_type === 'LOST' ? 'search' : 'check-circle'}"></i>
                        ${item.item_type}
                    </span>
                </div>
                
                <div class="item-detail-content">
                    <div class="item-detail-header">
                        <h1 class="item-detail-title">${escapeHtml(item.title)}</h1>
                        <span class="item-status-badge status-${item.current_status.toLowerCase().replace('_', '-')}">
                            ${formatStatus(item.current_status)}
                        </span>
                    </div>
                    
                    <div class="item-detail-meta">
                        <div class="meta-item">
                            <i class="fas fa-tag"></i>
                            <span>${escapeHtml(item.category_name || 'Uncategorized')}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${escapeHtml(item.location_name || 'Unknown')}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span>${eventDate}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-user"></i>
                            <span>${escapeHtml(item.poster_name)}</span>
                        </div>
                    </div>
                    
                    <div class="item-detail-description">
                        <h3><i class="fas fa-align-left"></i> Description</h3>
                        <p>${escapeHtml(item.description)}</p>
                    </div>
                    
                    ${item.contact_info ? `
                        <div class="item-detail-contact">
                            <i class="fas fa-phone"></i>
                            <span>${escapeHtml(item.contact_info)}</span>
                        </div>
                    ` : ''}
                    
                    <div class="item-detail-footer">
                        <span class="posted-time">
                            <i class="fas fa-clock"></i>
                            Posted ${postedDate}
                        </span>
                        ${canClaim ? `
                            <button class="btn btn-primary" onclick="openClaimModal()">
                                <i class="fas fa-hand-paper"></i> Claim This Item
                            </button>
                        ` : !IS_LOGGED_IN ? `
                            <a href="../login.php" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Login to Claim
                            </a>
                        ` : `
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-info-circle"></i> ${item.current_status === 'CLAIM_PENDING' ? 'Claim Pending' : 'Not Available'}
                            </button>
                        `}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    console.log('Generated HTML length:', html.length);
    console.log('Setting innerHTML...');
    
    const container = document.getElementById('itemDetailsContainer');
    if (container) {
        container.innerHTML = html;
        console.log('innerHTML set successfully');
    } else {
        console.error('itemDetailsContainer not found!');
    }
    
    // Initialize AOS animations
    if (typeof AOS !== 'undefined') {
        AOS.refresh();
    }
}

/**
 * Format status text
 */
function formatStatus(status) {
    return status.replace(/_/g, ' ')
        .toLowerCase()
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

/**
 * Open claim modal
 */
function openClaimModal() {
    document.getElementById('claimItemId').value = ITEM_ID;
    document.getElementById('proofDescription').value = '';
    document.getElementById('claimAlertContainer').innerHTML = '';
    document.getElementById('claimModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

/**
 * Close claim modal
 */
function closeClaimModal() {
    document.getElementById('claimModal').style.display = 'none';
    document.body.style.overflow = '';
}

/**
 * Setup claim form submission
 */
function setupClaimForm() {
    const form = document.getElementById('claimForm');
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const proofDescription = document.getElementById('proofDescription').value.trim();
        const alertContainer = document.getElementById('claimAlertContainer');
        
        // Validate
        if (proofDescription.length < 20) {
            showClaimAlert('Please provide a more detailed proof (at least 20 characters)', 'error');
            return;
        }
        
        // Submit
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        
        try {
            const formData = new FormData();
            formData.append('item_id', ITEM_ID);
            formData.append('proof_description', proofDescription);
            
            const response = await apiPost('/api/student/submit_claim.php', formData);
            
            if (response.success) {
                showClaimAlert(response.message, 'success');
                setTimeout(() => {
                    closeClaimModal();
                    window.location.href = 'my_claims.php';
                }, 2000);
            } else {
                showClaimAlert(response.message || 'Failed to submit claim', 'error');
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Claim';
            }
        } catch (error) {
            console.error('Error submitting claim:', error);
            showClaimAlert('An error occurred. Please try again.', 'error');
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Claim';
        }
    });
}

/**
 * Show alert in claim modal
 */
function showClaimAlert(message, type = 'info') {
    const container = document.getElementById('claimAlertContainer');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
    
    const alertHTML = `
        <div class="alert ${alertClass}" role="alert" style="margin-bottom: 20px;">
            <i class="fas fa-${icon}"></i>
            <span>${escapeHtml(message)}</span>
        </div>
    `;
    
    container.innerHTML = alertHTML;
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close modal when clicking overlay
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        closeClaimModal();
    }
});
