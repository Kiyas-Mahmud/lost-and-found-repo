/**
 * Admin Locations Management JavaScript
 * Handles modal operations and client-side filtering
 */

// Modal functions
function openEditModal(locationId, locationName) {
    document.getElementById('edit_location_id').value = locationId;
    document.getElementById('edit_location_name').value = locationName;
    const modal = document.getElementById('editModal');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

// Close modal on backdrop click
document.getElementById('editModal').addEventListener('click', function(e) {
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
    const filterStatus = document.getElementById('filter-status');
    const clearFiltersBtn = document.getElementById('clear-filters');
    const tableWrapper = document.getElementById('table-wrapper');
    const emptyState = document.getElementById('empty-state');
    
    let filterTimeout;

    function applyFilters() {
        const searchTerm = filterSearch.value.toLowerCase().trim();
        const statusValue = filterStatus.value;
        const rows = document.querySelectorAll('.location-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const locationName = row.dataset.locationName;
            const rowStatus = row.dataset.status;

            // Check search match
            const matchesSearch = !searchTerm || locationName.includes(searchTerm);

            // Check status match
            const matchesStatus = !statusValue || rowStatus === statusValue;

            if (matchesSearch && matchesStatus) {
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
        const hasFilters = filterSearch.value.trim() !== '' || filterStatus.value !== '';
        clearFiltersBtn.style.display = hasFilters ? 'inline-block' : 'none';
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

    // Initial state
    toggleClearButton();
});
