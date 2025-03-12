<?php
    require_once 'includes/header.php';
    require_once 'includes/navbar.php';
    require_once 'includes/user.php';
?>

<script>
    if (window.innerWidth < 768) {
        window.location.href = 'small_screen.php';
    }
</script>

<?php
    require_once 'content.php';
    require_once 'includes/footer.php';
?>
