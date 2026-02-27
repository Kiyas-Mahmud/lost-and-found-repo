/**
 * Admin Staff Management JavaScript
 * Handles modal operations, filtering, and staff actions
 */

// Modal functions
function openEditModal(userId, fullName, username, role) {
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_full_name').value = fullName;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_role').value = role;
    const modal = document.getElementById('editModal');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

function confirmDelete(userId, fullName) {
    if (confirm(`Are you sure you want to delete ${fullName}? This action cannot be undone.`)) {
        document.getElementById('delete_user_id').value = userId;
        document.getElementById('deleteForm').submit();
    }
}

// Close modal on backdrop click
document.getElementById('editModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
    }
});

// Filter & Search Logic
document.addEventListener('DOMContentLoaded', function() {
    const filterSearch = document.getElementById('filter-search');
    const filterRole = document.getElementById('filter-role');
    const clearFiltersBtn = document.getElementById('clear-filters');
    const tableWrapper = document.getElementById('table-wrapper');
    const emptyState = document.getElementById('empty-state');
    
    let filterTimeout;

    function applyFilters() {
        const searchTerm = filterSearch.value.toLowerCase().trim();
        const roleValue = filterRole.value;
        const rows = document.querySelectorAll('.staff-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const searchData = row.dataset.search;
            const rowRole = row.dataset.role;

            // Check search match
            const matchesSearch = !searchTerm || searchData.includes(searchTerm);

            // Check role match
            const matchesRole = !roleValue || rowRole === roleValue;

            if (matchesSearch && matchesRole) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide clear button
        toggleClearButton();

        // Show/hide empty state
        if (visibleCount === 0) {
            tableWrapper.style.display = 'none';
            emptyState.style.display = 'flex';
        } else {
            tableWrapper.style.display = 'block';
            emptyState.style.display = 'none';
        }
    }

    function toggleClearButton() {
        const hasFilters = filterSearch.value.trim() !== '' || filterRole.value !== '';
        clearFiltersBtn.style.display = hasFilters ? 'inline-block' : 'none';
    }

    // Search with debounce
    filterSearch.addEventListener('input', function() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(applyFilters, 300);
    });

    // Role filter change
    filterRole.addEventListener('change', applyFilters);

    // Clear filters
    clearFiltersBtn.addEventListener('click', function() {
        filterSearch.value = '';
        filterRole.value = '';
        applyFilters();
    });

    // Initial state
    toggleClearButton();
});
