<div class="container">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1><i class="fas fa-search"></i> Browse Items</h1>
        <p style="color: var(--gray-600);">Search and filter through lost and found items</p>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem;">
                <input type="text" id="searchInput" placeholder="Search items..." class="form-control" style="flex: 1; min-width: 250px;">
                
                <select class="form-control" style="width: 200px;">
                    <option value="">All Types</option>
                    <option value="LOST">Lost Items</option>
                    <option value="FOUND">Found Items</option>
                </select>
                
                <select class="form-control" style="width: 200px;">
                    <option value="">All Categories</option>
                    <option value="1">ID Card</option>
                    <option value="2">Wallet</option>
                    <option value="3">Phone</option>
                </select>
                
                <button class="btn btn-primary">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 2rem;">
        <?php if (empty($items)): ?>
            <div class="card text-center" style="padding: 3rem;">
                <i class="fas fa-inbox" style="font-size: 4rem; color: var(--gray-300); margin-bottom: 1rem;"></i>
                <h3 style="color: var(--gray-600);">No Items Found</h3>
                <p style="color: var(--gray-500);">Check back later or try adjusting your filters.</p>
                <?php if (is_logged_in() && is_student()): ?>
                    <div style="margin-top: 1.5rem;">
                        <a href="index.php?page=post_lost" class="btn btn-primary">Post Lost Item</a>
                        <a href="index.php?page=post_found" class="btn btn-success">Post Found Item</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-3">
                <?php foreach ($items as $item): ?>
                    <?php include VIEWS_PATH . '/components/ui/item_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
