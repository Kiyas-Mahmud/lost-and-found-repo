<?php
/**
 * Type Badge Component
 * Displays LOST or FOUND badge
 * 
 * Usage: include and pass $type variable
 * Example: $type = 'LOST' or 'FOUND'
 */
$type = $type ?? 'LOST';
$badge_class = $type === 'LOST' ? 'badge-lost' : 'badge-found';
?>
<span class="badge badge-type <?php echo $badge_class; ?>">
    <i class="fas fa-<?php echo $type === 'LOST' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
    <?php echo htmlspecialchars($type); ?>
</span>
