<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/user.php';
require_once 'includes/config.php';

// Check if the headline id and temp_id are provided
if (!isset($_GET['h_id']) || !isset($_GET['temp_id'])) {
    echo "<p>لم يتم توفير معرف العنوان.</p>";

    require_once "includes/footer.php";
    exit;
}

$h_id = intval($_GET['h_id']);
$temp_id = intval($_GET['temp_id']);

// Retrieve the current intro. If temp_id==3, also retrieve main_img.
if ($temp_id == 3) {
    $select_query = "SELECT intro, main_img FROM headline WHERE h_id = $h_id AND temp_id = $temp_id";
} else {
    $select_query = "SELECT intro FROM headline WHERE h_id = $h_id AND temp_id = $temp_id";
}

$result = mysqli_query($conn, $select_query);
if (!$result) {
    die("Error: " . mysqli_error($conn));
}

if ($row = mysqli_fetch_assoc($result)) {
    $intro_value = $row['intro'];
    if ($temp_id == 3) {
        // Set default image to "poem.png" if no image is found
        $main_img_value = (isset($row['main_img']) && !empty($row['main_img'])) ? $row['main_img'] : 'poem.png';
    }
} else {
    echo "<p>السجل غير موجود.</p>";

    require_once "includes/footer.php";
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Escape the intro text
    $intro = mysqli_real_escape_string($conn, $_POST['intro']);
    
    if ($temp_id == 3) {
        // For temp_id == 3, update intro and main_img
        
        // Initialize new image value with the current one
        $new_img_value = $main_img_value;
        
        // If delete checkbox is checked and current image is not the default, delete it
        if (isset($_POST['delete_image']) && $main_img_value !== 'poem.png') {
            if (file_exists("../assets/images/img/" . $main_img_value)) {
                unlink("../assets/images/img/" . $main_img_value);
            }
            $new_img_value = "";
        }
        
        // Process file upload if a new file is provided
        if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] == 0) {
            $file_tmp  = $_FILES['img_file']['tmp_name'];
            $file_name = $_FILES['img_file']['name'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = array('svg', 'png', 'jpg', 'jpeg');
            
            if (!in_array($file_ext, $allowed_exts)) {
                $error_message = "مسموح بـ png, jpg, svg, jpeg فقط";
            } else {
                // Optionally delete the old image if not default
                if ($new_img_value !== 'poem.png' && !empty($new_img_value) && file_exists("../assets/images/img/" . $new_img_value)) {
                    unlink("../assets/images/img/" . $new_img_value);
                }
                // Rename file using current date/time
                $new_img_value = date("YmdHis") . '.' . $file_ext;
                if (!move_uploaded_file($file_tmp, "../assets/images/img/" . $new_img_value)) {
                    $error_message = "فشل في تحميل الصورة.";
                }
            }
        }
        
        // If no image remains, set default to "poem.png"
        if (empty($new_img_value)) {
            $new_img_value = "poem.png";
        }
        
        $update_query = "UPDATE headline SET intro = '$intro', main_img = '$new_img_value' WHERE h_id = $h_id AND temp_id = $temp_id";
    } else {
        $update_query = "UPDATE headline SET intro = '$intro' WHERE h_id = $h_id AND temp_id = $temp_id";
    }
    
    if (mysqli_query($conn, $update_query)) {
        header("Location: temp{$temp_id}.php?h_id=$h_id");
        exit;
    } else {
            echo "<p>Error updating record: " . mysqli_error($conn) . "</p>";

    }
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Introduction</h1>

    <!-- Include enctype for file uploads -->
    <form method="post" action="edit_intro.php?h_id=<?php echo $h_id; ?>&temp_id=<?php echo $temp_id; ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="intro">المقدمة</label>
            <textarea name="intro" id="intro" class="form-control" rows="5" required><?php echo htmlspecialchars($intro_value); ?></textarea>
        </div>
        <?php if ($temp_id == 3): ?>
        <div class="form-group">
            <?php if (!empty($main_img_value)): ?>
                <img src="../assets/images/img/<?php echo $main_img_value; ?>" height="100" class="d-block mb-2">
                <div class="form-check">
                    <label for="delete_image" class="form-check-label text-danger">حذف الصورة الحالية</label>
                    <input type="checkbox" name="delete_image" class="form-check-input" id="delete_image">
                </div>
            <?php endif; ?>
            <label for="img_file">رفع صورة جديدة</label>
            <input type="file" name="img_file" id="img_file" class="form-control-file">
            <p>مسموح بـ png, jpg, svg, jpeg فقط</p>
            <?php if (isset($error_message) && $error_message): ?>
                <p class="text-danger mt-2"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">تحديث</button>
    </form>
</div>

<?php
require_once 'includes/footer.php';
?>
