<!-- Reject Claim Modal -->
<div id="rejectModal" class="modal-backdrop">
    <div class="modal-dialog" style="max-width: 500px;">
        <div class="modal-content-card" style="padding: 30px;">
            <button class="modal-close-button" onclick="closeModal('rejectModal')">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="modal-icon-wrapper danger" style="width: 60px; height: 60px; margin-bottom: 16px;">
                <i class="fas fa-times-circle" style="font-size: 1.5rem;"></i>
            </div>
            
            <h2 class="modal-heading" style="font-size: 1.5rem; margin-bottom: 8px;">Reject Claim</h2>
            <p class="modal-subtext" style="font-size: 0.9rem; margin-bottom: 20px;">
                Are you sure you want to reject this claim? The item will remain available for other claims.
            </p>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="reject_notes" class="form-label">Reason for Rejection (Optional)</label>
                    <textarea id="reject_notes" 
                              name="admin_notes" 
                              class="form-control" 
                              rows="3" 
                              placeholder="Explain why this claim is being rejected..."></textarea>
                </div>
                <input type="hidden" name="action" value="reject">
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('rejectModal')">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reject Claim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
