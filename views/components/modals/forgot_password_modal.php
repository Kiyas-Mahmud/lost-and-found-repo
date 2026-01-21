<!-- Forgot Password Modal -->
<div id="forgotPasswordModal" class="modal-backdrop">
    <div class="modal-dialog">
        <div class="modal-content-claim">
            <button class="modal-close-button" onclick="closeForgotPasswordModal()">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="modal-icon-wrapper">
                <i class="fas fa-key"></i>
            </div>
            
            <h2 class="modal-heading">Reset Password</h2>
            <p class="modal-subtext">Enter your email and new password to reset your account</p>
            <div id="resetMessage" class="alert" style="display: none;"></div>
            <form id="forgotPasswordForm" class="claim-form">
                <div class="form-group">
                    <label for="resetEmail">Email Address <span class="required">*</span></label>
                    <input type="email" 
                           id="resetEmail" 
                           name="email" 
                           placeholder="Enter your registered email"
                           required>
                    <span class="error-message" id="resetEmail-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="newPassword">New Password <span class="required">*</span></label>
                    <div class="password-input">
                        <input type="password" 
                               id="newPassword" 
                               name="new_password" 
                               placeholder="Enter new password (min 6 characters)"
                               required>
                        <button type="button" class="toggle-password" onclick="togglePassword('newPassword')">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                    <span class="error-message" id="newPassword-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="confirmNewPassword">Confirm New Password <span class="required">*</span></label>
                    <div class="password-input">
                        <input type="password" 
                               id="confirmNewPassword" 
                               name="confirm_password" 
                               placeholder="Re-enter new password"
                               required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirmNewPassword')">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                    <span class="error-message" id="confirmNewPassword-error"></span>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeForgotPasswordModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
