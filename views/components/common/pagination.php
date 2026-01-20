<?php
/**
 * Pagination Component
 * 
 * Usage: include 'views/components/common/pagination.php';
 * 
 * @param int $currentPage - Current page number
 * @param int $totalPages - Total number of pages
 * @param array $filters - Optional array of filters to maintain in URL
 */

if ($totalPages <= 1) return; // Don't show pagination if only 1 page

// Build query string from filters
$queryParams = $_GET;
$queryString = http_build_query(array_filter($queryParams, function($key) {
    return $key !== 'page';
}, ARRAY_FILTER_USE_KEY));

$baseUrl = '?' . ($queryString ? $queryString . '&' : '') . 'page=';

// Calculate page range
$range = 2; // Show 2 pages before and after current
$startPage = max(1, $currentPage - $range);
$endPage = min($totalPages, $currentPage + $range);
?>

<div class="pagination">
    <div class="pagination-info">
        Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?>
    </div>
    
    <div class="pagination-buttons">
        <?php if ($currentPage > 1): ?>
            <a href="<?php echo $baseUrl . ($currentPage - 1); ?>" class="pagination-btn">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Previous
            </a>
        <?php else: ?>
            <span class="pagination-btn disabled">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Previous
            </span>
        <?php endif; ?>
        
        <div class="pagination-numbers">
            <?php if ($startPage > 1): ?>
                <a href="<?php echo $baseUrl . '1'; ?>" class="pagination-number">1</a>
                <?php if ($startPage > 2): ?>
                    <span class="pagination-ellipsis">...</span>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <?php if ($i == $currentPage): ?>
                    <span class="pagination-number active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="<?php echo $baseUrl . $i; ?>" class="pagination-number"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($endPage < $totalPages): ?>
                <?php if ($endPage < $totalPages - 1): ?>
                    <span class="pagination-ellipsis">...</span>
                <?php endif; ?>
                <a href="<?php echo $baseUrl . $totalPages; ?>" class="pagination-number"><?php echo $totalPages; ?></a>
            <?php endif; ?>
        </div>
        
        <?php if ($currentPage < $totalPages): ?>
            <a href="<?php echo $baseUrl . ($currentPage + 1); ?>" class="pagination-btn">
                Next
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
            </a>
        <?php else: ?>
            <span class="pagination-btn disabled">
                Next
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
            </span>
        <?php endif; ?>
    </div>
</div>
