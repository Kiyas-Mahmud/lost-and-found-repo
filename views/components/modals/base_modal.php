<!-- 
    Reusable Modal Component
    
    Usage:
    $modalId = 'myModal';
    $modalTitle = 'Modal Title';
    $modalIcon = 'fa-info-circle';
    $modalBodyContent = '<p>Your content here</p>';
    $modalFooterButtons = '<button class="btn btn-primary">Save</button>';
    include 'path/to/base_modal.php';
-->
<div id="<?= $modalId ?>" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="close<?= ucfirst($modalId) ?>()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <?php if (isset($modalIcon)): ?>
                    <i class="fas <?= $modalIcon ?>"></i>
                <?php endif; ?>
                <?= $modalTitle ?>
            </h3>
            <button class="modal-close" onclick="close<?= ucfirst($modalId) ?>()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <?= $modalBodyContent ?>
        </div>
        <?php if (isset($modalFooterButtons)): ?>
            <div class="modal-footer">
                <?= $modalFooterButtons ?>
            </div>
        <?php endif; ?>
    </div>
</div>
