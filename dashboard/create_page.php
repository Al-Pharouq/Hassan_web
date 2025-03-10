<?php 
ob_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/user.php';
require_once 'includes/config.php';

// Check if temp_id is provided
if (isset($_GET['temp_id'])) {
    $temp_id = intval($_GET['temp_id']);
} else {
    echo "<p>No template provided.</p>";
    require_once "includes/footer.php";
    exit;
}

$error_message = ''; // For logo image upload errors

// Process form submission to create a new headline
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Escape the input values
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    
    // Only capture the intro if temp_id is 1 or 3
    if ($temp_id == 1 || $temp_id == 3) {
        $intro = mysqli_real_escape_string($conn, $_POST['intro']);
    } else {
        $intro = ''; // Use empty string if intro is not applicable
    }
    
    // Process logo image upload (optional)
    $logo_img_value = "default_logo.png"; // default value
    if (isset($_FILES['logo_img']) && $_FILES['logo_img']['error'] == 0) {
        $file_tmp  = $_FILES['logo_img']['tmp_name'];
        $file_name = $_FILES['logo_img']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = array('svg', 'png', 'jpg', 'jpeg');
        
        if (!in_array($file_ext, $allowed_exts)) {
            $error_message = "Only SVG, PNG, JPG, and JPEG files are allowed for the logo.";
        } else {
            // Rename file using current date/time
            $logo_img_value = date("YmdHis") . '.' . $file_ext;
            // Adjust the destination folder as needed
            if (!move_uploaded_file($file_tmp, "../assets/images/icons/" . $logo_img_value)) {
                $error_message = "Failed to upload logo image.";
            }
        }
    }
    
    // Only proceed with INSERT if no error occurred during file upload
    if (empty($error_message)) {
        // Insert the new headline into the database, including the logo image
        $sql = "INSERT INTO headline (title, intro, temp_id, logo_img) VALUES ('$title', '$intro', $temp_id, '$logo_img_value')";
        if (mysqli_query($conn, $sql)) {
            // Get the newly inserted headline id
            $headline_id = mysqli_insert_id($conn);
            // Redirect to the temp page (e.g., temp1.php, temp2.php, etc.) with the new headline id
            header("Location: temp{$temp_id}.php?h_id=" . urlencode($headline_id));
            exit;
        } else {
            echo "<p>Error inserting record: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p class='text-danger'>$error_message</p>";
    }
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Create New Headline</h1>
    <!-- Form action points to the same file and includes enctype for file uploads -->
    <form method="post" action="new_headline.php?temp_id=<?php echo $temp_id; ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Headline Title</label>
            <input type="text" name="title" id="title" class="form-control" placeholder="Enter headline title" required>
        </div>
        <?php if ($temp_id == 1 || $temp_id == 3): ?>
        <div class="form-group">
            <label for="intro">Introduction</label>
            <textarea name="intro" id="intro" class="form-control" rows="5" placeholder="Enter introduction" required></textarea>
        </div>
        <?php endif; ?>
        <div class="form-group">im
            <label for="logo_img">Logo Image</label>
            <input type="file" name="logo_img" id="logo_img" class="form-control-file">
            <small class="form-text text-muted">Allowed file types: SVG, PNG, JPG, JPEG. Default: default_logo.png</small>
        </div>
        <button type="submit" class="btn btn-primary">Create Headline</button>
    </form>
</div>

<?php
require_once 'includes/footer.php';
?>
