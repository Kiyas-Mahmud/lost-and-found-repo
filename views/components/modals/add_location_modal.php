<!-- Add Location Modal -->
<div id="addModal" class="modal-backdrop">
    <div class="modal-dialog" style="max-width: 450px;">
        <div class="modal-content-card" style="padding: 30px;">
            <button class="modal-close-button" onclick="closeModal('addModal')">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="modal-icon-wrapper info" style="width: 60px; height: 60px; margin-bottom: 16px;">
                <i class="fas fa-map-marker-alt" style="font-size: 1.5rem;"></i>
            </div>
            
            <h2 class="modal-heading" style="font-size: 1.5rem; margin-bottom: 20px;">Add New Location</h2>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="location_name" class="form-label">Location Name</label>
                    <input type="text" 
                           id="location_name" 
                           name="location_name" 
                           class="form-control" 
                           required
                           placeholder="Enter location name...">
                </div>
                <input type="hidden" name="action" value="add">
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Location
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
