<div class="container">
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-user-plus"></i> Register</h2>
            <p style="color: var(--gray-600); margin-top: 0.5rem;">Create your account</p>
        </div>
        
        <div class="card-body" style="max-width: 600px; margin: 0 auto;">
            <form method="POST" action="index.php?page=register" data-validate="true">
                <div class="grid grid-cols-2">
                    <div class="form-group">
                        <label for="full_name" class="form-label required">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" placeholder="John Doe" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="student_id" class="form-label">Student ID</label>
                        <input type="text" id="student_id" name="student_id" class="form-control" placeholder="STU123456">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label required">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="student@university.edu" required>
                    <small class="form-text">Use your university email address</small>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="+1 (555) 123-4567">
                </div>
                
                <div class="grid grid-cols-2">
                    <div class="form-group">
                        <label for="password" class="form-label required">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="form-label required">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: flex-start; gap: 0.5rem;">
                        <input type="checkbox" name="terms" style="width: auto; margin-top: 0.25rem;" required>
                        <span style="font-size: 0.875rem;">I agree to the terms and conditions of the University Lost & Found platform</span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
                
                <div style="text-align: center; margin-top: 1.5rem;">
                    <p style="color: var(--gray-600);">
                        Already have an account? 
                        <a href="index.php?page=login" style="font-weight: 600;">Login here</a>
                    </p>
                </div>
            </form>
            
            <div style="margin-top: 2rem; padding: 1rem; background-color: var(--gray-100); border-radius: var(--radius-md);">
                <p style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem;">
                    <strong>Phase 0 - Testing Mode:</strong>
                </p>
                <p style="font-size: 0.875rem; color: var(--gray-600);">
                    Registration will be implemented in Phase 2. This page demonstrates the form layout and validation structure.
                </p>
            </div>
        </div>
    </div>
</div>
