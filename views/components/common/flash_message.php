<?php
$flash = get_flash();
if ($flash):
?>
<div class="flash-message flash-<?php echo $flash['type']; ?>" id="flashMessage">
    <div class="flash-content">
        <i class="fas fa-<?php 
            echo $flash['type'] === 'success' ? 'check-circle' : 
                ($flash['type'] === 'error' ? 'exclamation-circle' : 
                ($flash['type'] === 'warning' ? 'exclamation-triangle' : 'info-circle')); 
        ?>"></i>
        <span><?php echo htmlspecialchars($flash['message']); ?></span>
        <button class="flash-close" onclick="closeFlash()">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
<?php endif; ?>
