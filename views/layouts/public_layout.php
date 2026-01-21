<?php include VIEWS_PATH . '/components/common/head.php'; ?>

<?php include VIEWS_PATH . '/components/common/navbar_student.php'; ?>

<?php include VIEWS_PATH . '/components/common/flash_message.php'; ?>

<?php 
// Check if it's the home page - home page doesn't need main-content wrapper
if (isset($page) && $page === 'home'): 
    echo $content; 
else: 
?>
<main class="main-content">
    <?php echo $content; ?>
</main>
<?php endif; ?>

<?php include VIEWS_PATH . '/components/common/footer.php'; ?>
