<div class="container">
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
            <p style="color: var(--gray-600); margin-top: 0.5rem;">Sign in to your account</p>
        </div>
        
        <div class="card-body" style="max-width: 500px; margin: 0 auto;">
            <form method="POST" action="index.php?page=login" data-validate="true">
                <div class="form-group">
                    <label for="email" class="form-label required">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="student@university.edu" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label required">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" name="remember" style="width: auto;">
                        <span>Remember me</span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                
                <div style="text-align: center; margin-top: 1.5rem;">
                    <p style="color: var(--gray-600);">
                        Don't have an account? 
                        <a href="index.php?page=register" style="font-weight: 600;">Register here</a>
                    </p>
                </div>
            </form>
            
            <div style="margin-top: 2rem; padding: 1rem; background-color: var(--gray-100); border-radius: var(--radius-md);">
                <p style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem;">
                    <strong>Phase 0 - Testing Mode:</strong>
                </p>
                <p style="font-size: 0.875rem; color: var(--gray-600);">
                    Authentication will be implemented in Phase 2. For now, this is a placeholder login page to test routing and layouts.
                </p>
            </div>
        </div>
    </div>
</div>
