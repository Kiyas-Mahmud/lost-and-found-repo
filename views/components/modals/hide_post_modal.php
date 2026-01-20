<!-- Hide Post Modal -->
<div id="hideModal" class="modal-backdrop">
    <div class="modal-dialog" style="max-width: 500px;">
        <div class="modal-content-card" style="padding: 30px;">
            <button class="modal-close-button" onclick="closeModal('hideModal')">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="modal-icon-wrapper warning" style="width: 60px; height: 60px; margin-bottom: 16px;">
                <i class="fas fa-eye-slash" style="font-size: 1.5rem;"></i>
            </div>
            
            <h2 class="modal-heading" style="font-size: 1.5rem; margin-bottom: 8px;">Hide Post</h2>
            <p class="modal-subtext" style="font-size: 0.9rem; margin-bottom: 20px;">
                Are you sure you want to hide "<strong id="hideItemTitle"></strong>"? This post will no longer be visible to users.
            </p>
            
            <form id="hideForm">
                <div class="form-group">
                    <label for="hide_reason" class="form-label">Reason for Hiding (Required)</label>
                    <textarea id="hide_reason" 
                              class="form-control" 
                              rows="3" 
                              required
                              placeholder="Explain why this post is being hidden..."></textarea>
                </div>
                <input type="hidden" id="hideItemId">
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('hideModal')">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-eye-slash"></i> Hide Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
