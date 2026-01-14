<?php
/**
 * Status Badge Component
 * Displays item status badge with appropriate styling
 * 
 * Usage: include and pass $status variable
 * Example: $status = 'OPEN', 'CLAIM_PENDING', 'APPROVED', 'RETURNED', 'HIDDEN'
 */
$status = $status ?? 'OPEN';
$status_display = str_replace('_', ' ', $status);

$badge_class = match($status) {
    'OPEN' => 'badge-open',
    'CLAIM_PENDING' => 'badge-pending',
    'APPROVED' => 'badge-approved',
    'RETURNED', 'CLOSED' => 'badge-returned',
    'HIDDEN' => 'badge-hidden',
    default => 'badge-default'
};

$icon = match($status) {
    'OPEN' => 'fa-circle',
    'CLAIM_PENDING' => 'fa-clock',
    'APPROVED' => 'fa-check-circle',
    'RETURNED', 'CLOSED' => 'fa-box-archive',
    'HIDDEN' => 'fa-eye-slash',
    default => 'fa-circle'
};
?>
<span class="badge badge-status <?php echo $badge_class; ?>">
    <i class="fas <?php echo $icon; ?>"></i>
    <?php echo htmlspecialchars($status_display); ?>
</span>
