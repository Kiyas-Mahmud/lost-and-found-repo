<?php include VIEWS_PATH . '/components/common/head.php'; ?>

<?php include VIEWS_PATH . '/components/common/navbar_admin.php'; ?>

<?php include VIEWS_PATH . '/components/common/flash_message.php'; ?>

<main class="main-content admin-content">
    <div class="container-fluid">
        <div class="admin-layout">
            <?php include VIEWS_PATH . '/components/admin/sidebar.php'; ?>
            
            <div class="admin-main">
                <?php echo $content; ?>
            </div>
        </div>
    </div>
</main>

<?php include VIEWS_PATH . '/components/common/footer.php'; ?>
