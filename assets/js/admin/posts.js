/**
 * Admin Posts Management JavaScript
 * Handles loading, filtering, hiding/unhiding posts
 */

let currentPage = 1;
let currentFilters = {
    type: '',
    status: '',
    date: '',
    search: ''
};

// Load posts on page load
document.addEventListener('DOMContentLoaded', () => {
    loadPosts();

    // Set up filter listeners
    document.getElementById('filter-type').addEventListener('change', (e) => {
        currentFilters.type = e.target.value;
        currentPage = 1;
        loadPosts();
        toggleClearButton();
    });

    document.getElementById('filter-status').addEventListener('change', (e) => {
        currentFilters.status = e.target.value;
        currentPage = 1;
        loadPosts();
        toggleClearButton();
    });

    document.getElementById('filter-date').addEventListener('change', (e) => {
        currentFilters.date = e.target.value;
        currentPage = 1;
        loadPosts();
        toggleClearButton();
    });

    document.getElementById('filter-search').addEventListener('input', debounce((e) => {
        currentFilters.search = e.target.value;
        currentPage = 1;
        loadPosts();
        toggleClearButton();
    }, 500));

    document.getElementById('clear-filters').addEventListener('click', () => {
        document.getElementById('filter-type').value = '';
        document.getElementById('filter-status').value = '';
        document.getElementById('filter-date').value = '';
        document.getElementById('filter-search').value = '';
        currentFilters = { type: '', status: '', date: '', search: '' };
        currentPage = 1;
        loadPosts();
        toggleClearButton();
    });

    // Hide form submission
    document.getElementById('hideForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const itemId = document.getElementById('hideItemId').value;
        const reason = document.getElementById('hide_reason').value;
        
        if (!reason.trim()) {
            showToast('Please provide a reason for hiding this post', 'error');
            return;
        }

        await hidePost(itemId, reason);
    });
});

/**
 * Load posts from API
 */
async function loadPosts() {
    const loadingSpinner = document.getElementById('loading-spinner');
    const postsContainer = document.getElementById('posts-container');
    const emptyState = document.getElementById('empty-state');

    // Show loading
    loadingSpinner.style.display = 'flex';
    postsContainer.style.display = 'none';
    emptyState.style.display = 'none';

    try {
        const params = new URLSearchParams({
            ...currentFilters,
            page: currentPage,
            perPage: 20
        });

        const response = await apiGet(`../../api/admin/posts.php?${params}`);

        if (response.success) {
            const { posts, total, totalPages } = response.data;

            // Update total count
            document.getElementById('total-count').textContent = `${total} Total`;

            if (posts.length > 0) {
                renderPostsTable(posts);
                renderPagination(totalPages);
                postsContainer.style.display = 'block';
            } else {
                emptyState.style.display = 'flex';
            }
        } else {
            showToast(response.message || 'Failed to load posts', 'error');
            emptyState.style.display = 'flex';
        }
    } catch (error) {
        console.error('Error loading posts:', error);
        showToast('Error loading posts', 'error');
        emptyState.style.display = 'flex';
    } finally {
        loadingSpinner.style.display = 'none';
    }
}

/**
 * Render posts table
 */
function renderPostsTable(posts) {
    const tbody = document.getElementById('posts-tbody');
    tbody.innerHTML = posts.map(post => {
        const postDate = new Date(post.created_at);
        const formattedDate = postDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

        const typeBadge = post.item_type === 'LOST' 
            ? '<span class="badge-lost">Lost</span>' 
            : '<span class="badge-found">Found</span>';

        const statusBadge = getStatusBadge(post.current_status);
        const isHidden = post.current_status === 'HIDDEN';
        const rowClass = isHidden ? 'row-hidden' : '';
        const hiddenIndicator = isHidden ? '<span class="hidden-indicator">🔒</span>' : '';

        const actionButton = isHidden 
            ? `<button type="button" class="btn-secondary-sm" onclick="unhidePost(${post.item_id})">Unhide</button>`
            : `<button type="button" class="btn-danger-sm" onclick="openHideModal(${post.item_id}, '${escapeHtml(post.title).replace(/'/g, "\\'")}')">Hide</button>`;

        return `
            <tr class="${rowClass}" id="post-row-${post.item_id}">
                <td><span class="text-mono">#${post.item_id}</span></td>
                <td>
                    <div class="table-item-title">
                        ${escapeHtml(post.title)}${hiddenIndicator}
                    </div>
                </td>
                <td>${typeBadge}</td>
                <td>${escapeHtml(post.category_name)}</td>
                <td>${statusBadge}</td>
                <td>
                    ${post.claim_count > 0 
                        ? `<span class="badge-warning">${post.claim_count}</span>` 
                        : '<span class="text-muted">0</span>'}
                </td>
                <td>
                    <div class="table-user-info">
                        <div class="user-name">${escapeHtml(post.poster_name)}</div>
                    </div>
                </td>
                <td>
                    <div class="table-date">${formattedDate}</div>
                </td>
                <td>
                    <div class="table-actions">
                        ${actionButton}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

/**
 * Hide post
 */
async function hidePost(itemId, reason) {
    try {
        const response = await apiPost('../../api/admin/posts.php', {
            action: 'hide',
            item_id: itemId,
            reason: reason
        });

        if (response.success) {
            showToast('Post hidden successfully', 'success');
            closeModal('hideModal');
            document.getElementById('hide_reason').value = '';
            loadPosts(); // Reload the table
        } else {
            showToast(response.message || 'Failed to hide post', 'error');
        }
    } catch (error) {
        console.error('Error hiding post:', error);
        showToast('Error hiding post', 'error');
    }
}

/**
 * Unhide post
 */
async function unhidePost(itemId) {
    if (!confirm('Are you sure you want to unhide this post?')) {
        return;
    }

    try {
        const response = await apiPost('../../api/admin/posts.php', {
            action: 'unhide',
            item_id: itemId
        });

        if (response.success) {
            showToast('Post unhidden successfully', 'success');
            loadPosts(); // Reload the table
        } else {
            showToast(response.message || 'Failed to unhide post', 'error');
        }
    } catch (error) {
        console.error('Error unhiding post:', error);
        showToast('Error unhiding post', 'error');
    }
}

/**
 * Open hide modal
 */
function openHideModal(itemId, itemTitle) {
    document.getElementById('hideItemId').value = itemId;
    document.getElementById('hideItemTitle').textContent = itemTitle;
    openModal('hideModal');
}

/**
 * Render pagination
 */
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

/**
 * Change page
 */
function changePage(page) {
    currentPage = page;
    loadPosts();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/**
 * Get status badge HTML
 */
function getStatusBadge(status) {
    switch(status) {
        case 'OPEN':
            return '<span class="badge-success">Open</span>';
        case 'CLAIMED':
            return '<span class="badge-warning">Claimed</span>';
        case 'RETURNED':
            return '<span class="badge-info">Returned</span>';
        case 'HIDDEN':
            return '<span class="badge-danger">Hidden</span>';
        default:
            return '<span class="badge-secondary">' + status + '</span>';
    }
}

/**
 * Toggle clear button visibility
 */
function toggleClearButton() {
    const clearBtn = document.getElementById('clear-filters');
    const hasFilters = currentFilters.type || currentFilters.status || currentFilters.date || currentFilters.search;
    clearBtn.style.display = hasFilters ? 'inline-block' : 'none';
}

/**
 * Escape HTML
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Debounce function
 */
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
