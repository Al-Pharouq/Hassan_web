<?php 
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/user.php';
require_once 'includes/config.php';


// Check if the headline id and temp_id are provided
if (isset($_GET['temp_id'])) {
    $temp_id = intval($_GET['temp_id']);
} else {
    echo "<p>لم يتم توفير القالب.</p>";

    require_once "./includes/footer.php";
    exit;
}

    // Query to fetch template data (name, etc.)
    $sql_template = "SELECT temp_img
                 FROM template
                 WHERE temp_id = $temp_id";
    $exe_template = mysqli_query($conn, $sql_template);
    if (! $exe_template) {
        die("Error: " . mysqli_error($conn));
    }

    if ($row = mysqli_fetch_assoc($exe_template)) {
        $temp_img = $row['temp_img'];
    } else {
        echo "<p>السجل غير موجود.</p>";

        require_once "./includes/footer.php";
        exit;
    }
?>
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Ready Templates</h1>

            <a href="create_page.php?temp_id=<?php echo urlencode($temp_id); ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fas fa-plus fa-sm text-white-50"></i> Create Page</a>

        </div>

        <!-- Content Row -->
        <div class="row">
        <img class="img-fluid mb-4" src="assets/img/temp_img/temp<?php echo $temp_id; ?>.png" alt="Template Image">
        </div>

    </div>
    <!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<?php
require_once 'includes/footer.php';
?>
