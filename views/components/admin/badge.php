<?php
function renderBadge($text, $type = 'default') {
    $badgeClass = 'badge badge-' . $type;
    echo '<span class="' . $badgeClass . '">' . htmlspecialchars($text) . '</span>';
}

function renderTypeBadge($type) {
    $class = ($type === 'LOST') ? 'badge-lost' : 'badge-found';
    echo '<span class="badge ' . $class . '">' . htmlspecialchars($type) . '</span>';
}

function renderStatusBadge($status) {
    $statusMap = [
        'OPEN' => 'badge-open',
        'CLAIM_PENDING' => 'badge-pending',
        'APPROVED' => 'badge-approved',
        'RETURNED' => 'badge-returned',
        'HIDDEN' => 'badge-hidden',
        'PENDING' => 'badge-pending',
        'REJECTED' => 'badge-rejected',
        'RESOLVED' => 'badge-resolved'
    ];
    $class = $statusMap[$status] ?? 'badge-default';
    echo '<span class="badge ' . $class . '">' . htmlspecialchars($status) . '</span>';
}
?>
