<!-- Approve Claim Modal -->
<div id="approveModal" class="modal-backdrop">
    <div class="modal-dialog" style="max-width: 500px;">
        <div class="modal-content-card" style="padding: 30px;">
            <button class="modal-close-button" onclick="closeModal('approveModal')">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="modal-icon-wrapper success" style="width: 60px; height: 60px; margin-bottom: 16px;">
                <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
            </div>
            
            <h2 class="modal-heading" style="font-size: 1.5rem; margin-bottom: 8px;">Approve Claim</h2>
            <p class="modal-subtext" style="font-size: 0.9rem; margin-bottom: 20px;">
                Are you sure you want to approve this claim? This will mark the item as returned to the claimer.
            </p>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="approve_notes" class="form-label">Admin Notes (Optional)</label>
                    <textarea id="approve_notes" 
                              name="admin_notes" 
                              class="form-control" 
                              rows="3" 
                              placeholder="Add any notes about this decision..."></textarea>
                </div>
                <input type="hidden" name="action" value="approve">
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('approveModal')">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Approve Claim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
