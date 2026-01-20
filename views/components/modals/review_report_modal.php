<!-- Review Report Modal -->
<div id="reviewModal" class="modal-backdrop">
    <div class="modal-dialog" style="max-width: 650px;">
        <div class="modal-content-card" style="padding: 30px;">
            <button class="modal-close-button" onclick="closeModal('reviewModal')">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="modal-icon-wrapper warning" style="width: 60px; height: 60px; margin-bottom: 16px;">
                <i class="fas fa-flag" style="font-size: 1.5rem;"></i>
            </div>
            
            <h2 class="modal-heading" style="font-size: 1.5rem; margin-bottom: 20px;">Review Report</h2>
            
            <div class="report-details" style="margin-bottom: 20px;">
                <div class="detail-row" style="margin-bottom: 12px;">
                    <strong style="color: var(--text-muted); font-size: 0.85rem; display: block; margin-bottom: 4px;">Item:</strong>
                    <span id="modalItemTitle" style="color: var(--text-primary); font-size: 1rem;"></span>
                </div>
                <div class="detail-row" style="margin-bottom: 12px;">
                    <strong style="color: var(--text-muted); font-size: 0.85rem; display: block; margin-bottom: 4px;">Reason:</strong>
                    <span id="modalReason" class="report-reason" style="color: var(--text-primary); font-size: 1rem;"></span>
                </div>
                <div class="detail-row">
                    <strong style="color: var(--text-muted); font-size: 0.85rem; display: block; margin-bottom: 4px;">Description:</strong>
                    <p id="modalDescription" class="report-description" style="color: var(--text-primary); line-height: 1.6; margin: 0;"></p>
                </div>
            </div>
            
            <form id="reportForm">
                <div class="form-group">
                    <label for="resolution_notes" class="form-label">Resolution Notes (Optional)</label>
                    <textarea id="resolution_notes" 
                              class="form-control" 
                              rows="3" 
                              placeholder="Add notes about your decision..."></textarea>
                </div>
                <input type="hidden" id="modalReportId">
            </form>
            
            <div class="modal-actions" style="display: flex; gap: 8px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('reviewModal')">
                    Cancel
                </button>
                <button type="button" class="btn btn-warning" onclick="handleReportAction('dismiss')">
                    <i class="fas fa-ban"></i> Dismiss Report
                </button>
                <button type="button" class="btn btn-danger" onclick="handleReportAction('resolve')">
                    <i class="fas fa-shield-alt"></i> Take Action & Resolve
                </button>
            </div>
        </div>
    </div>
</div>
