<!-- Cancel Claim Modal -->
<div id="cancelModal" class="modal-backdrop">
    <div class="modal-dialog" style="max-width: 450px;">
        <div class="modal-content-card" style="padding: 30px;">
            <button class="modal-close-button" onclick="closeCancelModal()">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="modal-icon-wrapper warning" style="width: 60px; height: 60px; margin-bottom: 16px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem;"></i>
            </div>
            
            <h2 class="modal-heading" style="font-size: 1.5rem; margin-bottom: 8px;">Cancel Claim</h2>
            <p class="modal-subtext" style="font-size: 0.9rem; margin-bottom: 20px;">Are you sure you want to cancel this claim? This action cannot be undone.</p>
            
            <p class="text-center text-muted mb-3" id="cancelItemTitle" style="font-size: 0.85rem;"></p>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeCancelModal()">
                    Keep Claim
                </button>
                <button type="button" class="btn btn-warning" onclick="confirmCancel()">
                    <i class="fas fa-ban"></i> Cancel Claim
                </button>
            </div>
        </div>
    </div>
</div>
