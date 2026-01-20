<!-- Report Item Modal -->
<div id="reportModal" class="modal-backdrop">
    <div class="modal-dialog">
        <div class="modal-content-claim">
            <button class="modal-close-button" onclick="closeReportModal()">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="modal-icon-wrapper modal-icon-danger">
                <i class="fas fa-flag"></i>
            </div>
            
            <h2 class="modal-heading">Report This Post</h2>
            <p class="modal-subtext">Help us maintain quality by reporting suspicious or inappropriate posts</p>
            
            <form id="reportForm" class="claim-form">
                <div id="reportAlertContainer"></div>
                
                <div class="form-group">
                    <label for="reportReason" class="form-label">
                        Reason for Reporting <span class="text-danger">*</span>
                    </label>
                    <select 
                        id="reportReason" 
                        name="reason" 
                        class="form-control" 
                        required
                    >
                        <option value="">-- Select a reason --</option>
                        <option value="FAKE_POST">Fake or Fraudulent Post</option>
                        <option value="WRONG_INFO">Incorrect or Misleading Information</option>
                        <option value="SPAM">Spam or Duplicate Post</option>
                        <option value="SUSPICIOUS_CLAIM">Suspicious Claim Activity</option>
                        <option value="OTHER">Other (Please specify)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="reportComment" class="form-label">
                        Additional Details <span id="commentRequired" style="display:none;" class="text-danger">*</span>
                    </label>
                    <textarea 
                        id="reportComment" 
                        name="comment" 
                        class="form-control" 
                        rows="4" 
                        placeholder="Please provide additional information about why you're reporting this post..."
                    ></textarea>
                    <small class="form-text text-muted">Your report will be reviewed by our admin team.</small>
                </div>
                
                <input type="hidden" id="reportItemId" name="item_id">
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeReportModal()">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-flag"></i> Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Show/hide comment required indicator based on reason selection
document.getElementById('reportReason')?.addEventListener('change', function() {
    const commentRequired = document.getElementById('commentRequired');
    const commentField = document.getElementById('reportComment');
    
    if (this.value === 'OTHER') {
        commentRequired.style.display = 'inline';
        commentField.required = true;
    } else {
        commentRequired.style.display = 'none';
        commentField.required = false;
    }
});
</script>
