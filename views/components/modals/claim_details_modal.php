<!-- Claim Details Modal -->
<div id="detailsModal" class="modal-backdrop">
    <div class="modal-dialog" style="max-width: 900px; max-height: 85vh;">
        <div class="modal-content-card" style="padding: 0; display: flex; flex-direction: column; max-height: 85vh;">
            <button class="modal-close-button" onclick="closeDetailsModal()" style="z-index: 10;">
                <i class="fas fa-times"></i>
            </button>
            
            <div style="padding: 24px 30px 16px; border-bottom: 1px solid var(--border-color); flex-shrink: 0;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="modal-icon-wrapper info" style="width: 50px; height: 50px; margin: 0;">
                        <i class="fas fa-info-circle" style="font-size: 1.3rem;"></i>
                    </div>
                    <h2 class="modal-heading" style="font-size: 1.4rem; margin: 0;">Claim Details</h2>
                </div>
            </div>
            
            <div class="modal-body" id="claimDetailsContent" style="overflow-y: auto; padding: 24px 30px; flex: 1;">
                <!-- Details will be loaded dynamically -->
            </div>
            
            <div class="modal-actions" style="padding: 16px 30px; border-top: 1px solid var(--border-color); flex-shrink: 0; margin: 0;">
                <button type="button" class="btn btn-secondary" onclick="closeDetailsModal()">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
