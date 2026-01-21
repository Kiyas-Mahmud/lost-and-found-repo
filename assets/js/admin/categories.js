/**
 * Admin Categories Management JavaScript
 * Handles modal operations and client-side filtering
 */

// Modal functions
function openAddModal() {
    const modal = document.getElementById('addModal');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeAddModal() {
    const modal = document.getElementById('addModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
    document.getElementById('category_name').value = '';
}

function openEditModal(categoryId, categoryName) {
    document.getElementById('edit_category_id').value = categoryId;
    document.getElementById('edit_category_name').value = categoryName;
    const modal = document.getElementById('editModal');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

// Close modals on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
    }
});

// Filter & Search Logic
let filterTimeout;
const filterSearch = document.getElementById('filter-search');
const filterStatus = document.getElementById('filter-status');
const clearFiltersBtn = document.getElementById('clear-filters');
const tableRows = document.querySelectorAll('.admin-table tbody tr');

function applyFilters() {
    const searchTerm = filterSearch.value.toLowerCase().trim();
    const statusValue = filterStatus.value.toLowerCase();
    let visibleCount = 0;

    tableRows.forEach(row => {
        const categoryName = row.querySelector('.table-item-title')?.textContent.toLowerCase() || '';
        const statusBadge = row.querySelector('.badge-success, .badge-secondary');
        const rowStatus = statusBadge?.textContent.toLowerCase().trim() || '';

        // Check search match
        const matchesSearch = !searchTerm || categoryName.includes(searchTerm);

        // Check status match
        const matchesStatus = !statusValue || 
                             (statusValue === 'active' && rowStatus === 'active') ||
                             (statusValue === 'inactive' && rowStatus === 'inactive');

        if (matchesSearch && matchesStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Show/hide clear button
    toggleClearButton();

    // Show empty state if no results
    updateEmptyState(visibleCount);
}

function toggleClearButton() {
    const hasFilters = filterSearch.value.trim() !== '' || filterStatus.value !== '';
    clearFiltersBtn.style.display = hasFilters ? 'inline-block' : 'none';
}

function updateEmptyState(count) {
    const tbody = document.querySelector('.admin-table tbody');
    let emptyRow = tbody.querySelector('.empty-state-row');

    if (count === 0) {
        if (!emptyRow) {
            emptyRow = document.createElement('tr');
            emptyRow.className = 'empty-state-row';
            emptyRow.innerHTML = `
                <td colspan="5" class="text-center">
                    <div class="empty-state">
                        <p>No categories found matching your filters</p>
                    </div>
                </td>
            `;
            tbody.appendChild(emptyRow);
        }
    } else {
        if (emptyRow) {
            emptyRow.remove();
        }
    }
}

// Search with debounce
filterSearch.addEventListener('input', function() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(applyFilters, 300);
});

// Status filter change
filterStatus.addEventListener('change', applyFilters);

// Clear filters
clearFiltersBtn.addEventListener('click', function() {
    filterSearch.value = '';
    filterStatus.value = '';
    applyFilters();
});
