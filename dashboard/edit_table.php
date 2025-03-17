<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/user.php';
require_once 'includes/config.php';

// Check if required parameters are provided
if (!isset($_GET['c_id']) || !isset($_GET['headline_id']) || !isset($_GET['temp_id'])) {
    echo "<p>Required parameters are missing</p>";

    require_once "includes/footer.php";
    exit;
}

$c_id = intval($_GET['c_id']);
$headline_id = intval($_GET['headline_id']);
$temp_id = intval($_GET['temp_id']);

// Retrieve the existing record to pre-fill the form based on temp_id
if ($temp_id == 2) {
    $select_query = "SELECT caption, detail, img FROM content WHERE c_id = $c_id";
} elseif ($temp_id == 3) {
    $select_query = "SELECT caption FROM content WHERE c_id = $c_id";
} else {
    $select_query = "SELECT caption, detail FROM content WHERE c_id = $c_id";
}

$result = mysqli_query($conn, $select_query);
if (!$result) {
    die("Error: " . mysqli_error($conn));

}

if ($row = mysqli_fetch_assoc($result)) {
    $caption_value = $row['caption'];
    $detail_value  = isset($row['detail']) ? $row['detail'] : '';
    // For temp_id 2, if no image is stored, set default to "sheikh.png"
    if ($temp_id == 2) {
        $img_value = (isset($row['img']) && !empty($row['img'])) ? $row['img'] : 'sheikh.png';
    }
} else {
    echo "<p>Record not found.</p>";

    require_once "includes/footer.php";
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Always update caption
    $caption = mysqli_real_escape_string($conn, $_POST['caption']);
    
    if ($temp_id == 2) {
        // For temp_id = 2, update caption, detail, and img
        $detail = mysqli_real_escape_string($conn, $_POST['detail']);
        // Initialize new image value with the current one
        $new_img_value = $img_value;
        
        // If delete checkbox is checked, delete the current image (if it's not already the default)
        if (isset($_POST['delete_image']) && !empty($img_value) && $img_value !== 'sheikh.png') {
            if (file_exists("../assets/images/img/" . $img_value)) {
                unlink("../assets/images/img/" . $img_value);
            }
            $new_img_value = "";
        }
        
        // If a new file was uploaded, process it
        if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] == 0) {
            // Optionally delete the old image if it exists and is not the default
            if (!empty($img_value) && $img_value !== 'sheikh.png' && file_exists("../assets/images/img/" . $img_value)) {
                unlink("../assets/images/img/" . $img_value);
            }
            $file_tmp  = $_FILES['img_file']['tmp_name'];
            $file_name = $_FILES['img_file']['name'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = array('svg', 'png', 'jpg', 'jpeg');
        
            if (!in_array($file_ext, $allowed_exts)) {
                $error_message = "Only SVG, PNG, JPG, and JPEG files are allowed.";
            } else {
                // Rename file using current date/time
                $new_img_value = date("YmdHis") . '.' . $file_ext;
                if (!move_uploaded_file($file_tmp, "../assets/images/img/" . $new_img_value)) {
                    $error_message = "Failed to upload image.";
                }
            }
        }
        
        
        // If no image is set after processing, default to "sheikh.png"
        if (empty($new_img_value)) {
            $new_img_value = "sheikh.png";
        }
        
        $update_query = "UPDATE content SET caption = '$caption', detail = '$detail', img = '$new_img_value' WHERE c_id = $c_id";
    } elseif ($temp_id == 3) {
        // For temp_id = 3, update only caption
        $update_query = "UPDATE content SET caption = '$caption' WHERE c_id = $c_id";
    } else {
        // Default: update caption and detail
        $detail = mysqli_real_escape_string($conn, $_POST['detail']);
        $update_query = "UPDATE content SET caption = '$caption', detail = '$detail' WHERE c_id = $c_id";
    }
    
    if (mysqli_query($conn, $update_query)) {
        header("Location: temp{$temp_id}.php?h_id=$headline_id");
        exit;
    } else {
        echo "<p>Error updating record: " . mysqli_error($conn) . "</p>";

    }
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Text</h1>

    <!-- Include enctype for file uploads -->
    <form method="post" action="edit_table.php?c_id=<?php echo $c_id; ?>&headline_id=<?php echo $headline_id; ?>&temp_id=<?php echo $temp_id; ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="caption">Text</label>

            <textarea name="caption" id="caption" class="form-control" rows="3" required><?php echo htmlspecialchars($caption_value); ?></textarea>
        </div>
        <?php if ($temp_id != 3): // For temp_id 1 and 2, show detail field ?>
        <div class="form-group">
            <label for="detail">Source</label>

            <textarea name="detail" id="detail" class="form-control" rows="3" required><?php echo htmlspecialchars($detail_value); ?></textarea>
        </div>
        <?php endif; ?>
        <?php if ($temp_id == 2): // For temp_id 2, handle image upload dynamically ?>
        <div class="form-group">
            <?php if (!empty($img_value)): ?>
                <img src="../assets/images/img/<?php echo $img_value; ?>" height="100" class="d-block mb-2">
                <div class="form-check">
                    <label for="delete_image" class="form-check-label text-danger">حذف الصورة الحالية</label>
                    <input type="checkbox" name="delete_image" class="form-check-input" id="delete_image">
                </div>
            <?php endif; ?>
            <label for="img_file">Upload New Image</label>

            <input type="file" name="img_file" id="img_file" class="form-control-file">
            <p>مسموح بـ png, jpg, svg, jpeg فقط</p>
            <?php if (isset($error_message) && $error_message): ?>
                <p class="text-danger mt-2"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Update</button>

    </form>
</div>

<?php
require_once 'includes/footer.php';
?>
