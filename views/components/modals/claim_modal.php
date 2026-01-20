<!-- Claim Item Modal -->
<div id="claimModal" class="modal-backdrop">
    <div class="modal-dialog">
        <div class="modal-content-claim">
            <button class="modal-close-button" onclick="closeClaimModal()">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="modal-icon-wrapper">
                <i class="fas fa-hand-paper"></i>
            </div>
            
            <h2 class="modal-heading">Claim This Item</h2>
            <p class="modal-subtext">Provide proof of ownership to verify this item belongs to you</p>
            
            <form id="claimForm" class="claim-form">
                <div id="claimAlertContainer"></div>
                
                <div class="form-group">
                    <label for="proofDescription" class="form-label">
                        Proof of Ownership <span class="text-danger">*</span>
                    </label>
                    <textarea 
                        id="proofDescription" 
                        name="proof_description" 
                        class="form-control" 
                        rows="5" 
                        required
                        placeholder="Describe unique features, serial numbers, receipts, or any other details that prove this item is yours..."
                    ></textarea>
                    <small class="form-text text-muted">Be as specific as possible to help verify your claim.</small>
                </div>
                
                <input type="hidden" id="claimItemId" name="item_id">
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeClaimModal()">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Submit Claim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
