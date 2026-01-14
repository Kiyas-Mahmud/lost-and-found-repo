<?php
function renderPagination($currentPage, $totalPages, $baseUrl) {
    if ($totalPages <= 1) return;
    
    echo '<div class="pagination">';
    
    // Previous button
    if ($currentPage > 1) {
        echo '<a href="' . $baseUrl . '?page=' . ($currentPage - 1) . '" class="page-link">« Previous</a>';
    } else {
        echo '<span class="page-link disabled">« Previous</span>';
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1) {
        echo '<a href="' . $baseUrl . '?page=1" class="page-link">1</a>';
        if ($startPage > 2) {
            echo '<span class="page-link disabled">...</span>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        $activeClass = ($i == $currentPage) ? 'active' : '';
        echo '<a href="' . $baseUrl . '?page=' . $i . '" class="page-link ' . $activeClass . '">' . $i . '</a>';
    }
    
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            echo '<span class="page-link disabled">...</span>';
        }
        echo '<a href="' . $baseUrl . '?page=' . $totalPages . '" class="page-link">' . $totalPages . '</a>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        echo '<a href="' . $baseUrl . '?page=' . ($currentPage + 1) . '" class="page-link">Next »</a>';
    } else {
        echo '<span class="page-link disabled">Next »</span>';
    }
    
    echo '</div>';
}
?>
