<?php
/**
 * Reusable Filter & Search Component for Admin Pages
 * 
 * Usage:
 * $filterConfig = [
 *     'searchPlaceholder' => 'Search by name...',
 *     'filters' => [
 *         [
 *             'id' => 'filter-status',
 *             'options' => [
 *                 '' => 'All Status',
 *                 'active' => 'Active',
 *                 'inactive' => 'Inactive'
 *             ]
 *         ],
 *         [
 *             'id' => 'filter-type',
 *             'options' => [
 *                 '' => 'All Types',
 *                 'LOST' => 'Lost',
 *                 'FOUND' => 'Found'
 *             ]
 *         ]
 *     ],
 *     'showDateFilter' => true
 * ];
 * include '../components/admin/filter_search.php';
 */

$searchPlaceholder = $filterConfig['searchPlaceholder'] ?? 'Search...';
$filters = $filterConfig['filters'] ?? [];
$showDateFilter = $filterConfig['showDateFilter'] ?? false;
?>

<div class="table-filters">
    <div class="filter-form">
        <div class="filter-group">
            <!-- Search Input -->
            <input 
                type="text" 
                id="filter-search"
                class="filter-input search-input-full" 
                placeholder="<?php echo htmlspecialchars($searchPlaceholder); ?>"
            >
            
            <!-- Dynamic Filter Dropdowns -->
            <?php foreach ($filters as $filter): ?>
                <select id="<?php echo htmlspecialchars($filter['id']); ?>" class="filter-select">
                    <?php foreach ($filter['options'] as $value => $label): ?>
                        <option value="<?php echo htmlspecialchars($value); ?>">
                            <?php echo htmlspecialchars($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endforeach; ?>
            
            <!-- Date Filter (Optional) -->
            <?php if ($showDateFilter): ?>
                <input 
                    type="date" 
                    id="filter-date"
                    class="filter-input"
                >
            <?php endif; ?>
            
            <!-- Clear Filters Button -->
            <button id="clear-filters" class="btn-secondary-sm" style="display: none;">
                Clear
            </button>
        </div>
    </div>
</div>
