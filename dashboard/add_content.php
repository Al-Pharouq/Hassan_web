<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/user.php';
require_once 'includes/config.php';

// Check if the headline id and temp_id are provided
if (!isset($_GET['headline_id']) || !isset($_GET['temp_id'])) {
    echo "<p>No headline id or temp_id provided.</p>";
    require_once "includes/footer.php";
    exit;
}

$headline_id = intval($_GET['headline_id']);
$temp_id = intval($_GET['temp_id']);
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $caption = mysqli_real_escape_string($conn, $_POST['caption']);
    
    if ($temp_id == 2) {
        $detail = mysqli_real_escape_string($conn, $_POST['detail']);
        
        // Process file upload for image
        if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] == 0) {
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
        // If no new file was uploaded or if there's an error, default to "sheikh.png"
        if (!isset($new_img_value) || empty($new_img_value)) {
            $new_img_value = "sheikh.png";
        }
        
        if (empty($error_message)) {
            $sql = "INSERT INTO content (h_id, caption, detail, img) VALUES ($headline_id, '$caption', '$detail', '$new_img_value')";
        }
    } elseif ($temp_id == 3) {
        $sql = "INSERT INTO content (h_id, caption) VALUES ($headline_id, '$caption')";
    } else {
        $detail = mysqli_real_escape_string($conn, $_POST['detail']);
        $sql = "INSERT INTO content (h_id, caption, detail) VALUES ($headline_id, '$caption', '$detail')";
    }
    
    if (empty($error_message)) {
        if (mysqli_query($conn, $sql)) {
            header("Location: temp{$temp_id}.php?h_id=$headline_id");
            exit;
        } else {
            echo "<p>Error inserting record: " . mysqli_error($conn) . "</p>";
        }
    }
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Add New Content</h1>
    <form method="post" action="add_content.php?headline_id=<?php echo $headline_id; ?>&temp_id=<?php echo $temp_id; ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="caption">Caption</label>
            <textarea name="caption" id="caption" class="form-control" rows="3" required></textarea>
        </div>
        <?php if ($temp_id != 3): // For temp_id values other than 3, show detail field ?>
        <div class="form-group">
            <label for="detail">Detail</label>
            <textarea name="detail" id="detail" class="form-control" rows="4" required></textarea>
        </div>
        <?php endif; ?>
        <?php if ($temp_id == 2): // For temp_id = 2, handle image upload ?>
        <div class="form-group">
            <label for="img_file">Upload Image</label>
            <input type="file" name="img_file" id="img_file" class="form-control-file">
            <?php if (!empty($error_message)): ?>
                <p class="text-danger mt-2"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<?php
require_once 'includes/footer.php';
?>
