<div class="hero-section">
    <div class="container">
        <div class="hero-content text-center">
            <h1 style="font-size: 3rem; font-weight: 700; color: var(--gray-900); margin-bottom: 1rem;">
                <i class="fas fa-search" style="color: var(--primary-color);"></i>
                University Lost & Found
            </h1>
            <p style="font-size: 1.25rem; color: var(--gray-600); max-width: 600px; margin: 0 auto 2rem;">
                Helping students reunite with their lost belongings. Post lost items, browse found items, and claim what's yours!
            </p>
            
            <div class="hero-actions" style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="index.php?page=browse" class="btn btn-primary btn-lg">
                    <i class="fas fa-search"></i> Browse Items
                </a>
                <?php if (!is_logged_in()): ?>
                    <a href="index.php?page=register" class="btn btn-outline btn-lg">
                        <i class="fas fa-user-plus"></i> Get Started
                    </a>
                <?php else: ?>
                    <?php if (is_student()): ?>
                        <a href="index.php?page=post_lost" class="btn btn-outline btn-lg">
                            <i class="fas fa-plus"></i> Post Lost Item
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container" style="margin-top: 4rem;">
    <div class="card">
        <div class="card-header text-center">
            <h2>How It Works</h2>
            <p style="color: var(--gray-600); margin-top: 0.5rem;">Simple steps to recover your lost items</p>
        </div>
        
        <div class="grid grid-cols-3" style="margin-top: 2rem;">
            <div class="how-it-works-step text-center">
                <div style="width: 80px; height: 80px; background-color: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 2rem;">
                    <i class="fas fa-search"></i>
                </div>
                <h3 style="margin-bottom: 0.5rem;">1. Browse Items</h3>
                <p style="color: var(--gray-600);">Search through found items or post your lost item with details and images.</p>
            </div>
            
            <div class="how-it-works-step text-center">
                <div style="width: 80px; height: 80px; background-color: var(--success-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 2rem;">
                    <i class="fas fa-hand-paper"></i>
                </div>
                <h3 style="margin-bottom: 0.5rem;">2. Submit Claim</h3>
                <p style="color: var(--gray-600);">Found your item? Submit a claim with proof to verify ownership.</p>
            </div>
            
            <div class="how-it-works-step text-center">
                <div style="width: 80px; height: 80px; background-color: var(--warning-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 2rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 style="margin-bottom: 0.5rem;">3. Collect Item</h3>
                <p style="color: var(--gray-600);">After admin verification, collect your item from the security desk.</p>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-2" style="margin-top: 3rem; gap: 2rem;">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-exclamation-circle" style="color: var(--danger-color);"></i> Lost Something?</h3>
            </div>
            <p style="color: var(--gray-600); margin-bottom: 1.5rem;">
                Post details about your lost item to increase chances of recovery. Include description, location, and date.
            </p>
            <?php if (is_logged_in() && is_student()): ?>
                <a href="index.php?page=post_lost" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Post Lost Item
                </a>
            <?php else: ?>
                <a href="index.php?page=login" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login to Post
                </a>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-check-circle" style="color: var(--success-color);"></i> Found Something?</h3>
            </div>
            <p style="color: var(--gray-600); margin-bottom: 1.5rem;">
                Help someone by posting found items. Upload a photo and provide details about where you found it.
            </p>
            <?php if (is_logged_in() && is_student()): ?>
                <a href="index.php?page=post_found" class="btn btn-success">
                    <i class="fas fa-plus"></i> Post Found Item
                </a>
            <?php else: ?>
                <a href="index.php?page=login" class="btn btn-success">
                    <i class="fas fa-sign-in-alt"></i> Login to Post
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
