<?php
/**
 * Item Card Component
 * Displays item in card format for browse/listing pages
 * 
 * Required variables:
 * - $item: array with item data (item_id, title, item_type, category_name, location_name, event_date, image_path, current_status)
 */
?>
<div class="item-card">
    <div class="item-image">
        <?php if (!empty($item['image_path'])): ?>
            <img src="<?php echo upload_url($item['image_path']); ?>" alt="<?php echo e($item['title']); ?>">
        <?php else: ?>
            <div class="item-image-placeholder">
                <i class="fas fa-image"></i>
            </div>
        <?php endif; ?>
        
        <div class="item-badges">
            <?php 
            $type = $item['item_type'];
            include VIEWS_PATH . '/components/ui/type_badge.php'; 
            ?>
        </div>
    </div>
    
    <div class="item-details">
        <h3 class="item-title"><?php echo e($item['title']); ?></h3>
        
        <div class="item-meta">
            <div class="meta-item">
                <i class="fas fa-tag"></i>
                <span><?php echo e($item['category_name'] ?? 'N/A'); ?></span>
            </div>
            <div class="meta-item">
                <i class="fas fa-map-marker-alt"></i>
                <span><?php echo e($item['location_name'] ?? 'N/A'); ?></span>
            </div>
            <div class="meta-item">
                <i class="fas fa-calendar"></i>
                <span><?php echo format_date($item['event_date']); ?></span>
            </div>
        </div>
        
        <div class="item-status">
            <?php 
            $status = $item['current_status'];
            include VIEWS_PATH . '/components/ui/status_badge.php'; 
            ?>
        </div>
        
        <div class="item-actions">
            <a href="index.php?page=item_details&id=<?php echo $item['item_id']; ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-eye"></i> View Details
            </a>
        </div>
    </div>
</div>
