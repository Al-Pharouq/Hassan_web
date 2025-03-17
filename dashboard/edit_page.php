<?php 
ob_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/user.php';
require_once 'includes/config.php';

// Check if headline_id and temp_id are provided
if (!isset($_GET['headline_id']) || !isset($_GET['temp_id'])) {
    echo "<p>لم يتم توفير معرف العنوان.</p>";
    require_once 'includes/footer.php';
    exit;
}

$h_id = intval($_GET['headline_id']);
$temp_id = intval($_GET['temp_id']);
$error_message = '';

// Retrieve current headline details, including logo_img
$sql = "SELECT title, intro, logo_img FROM headline WHERE h_id = $h_id AND temp_id = $temp_id LIMIT 1";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error: " . mysqli_error($conn));
}

if ($row = mysqli_fetch_assoc($result)) {
    $title_value = $row['title'];
    $intro_value = isset($row['intro']) ? $row['intro'] : '';
    $logo_value  = (!empty($row['logo_img'])) ? $row['logo_img'] : 'default_logo.png';
} else {
    echo "<p>السجل غير موجود.</p>";
    require_once 'includes/footer.php';
    exit;
}

// Process form submission for update or deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        // Update action
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $intro = ($temp_id == 1 || $temp_id == 3) ? mysqli_real_escape_string($conn, $_POST['intro']) : '';
        
        $new_logo_value = $logo_value;

        // If the delete checkbox for logo is checked, delete the current logo (if it's not default)
        if (isset($_POST['delete_logo']) && $logo_value !== 'default_logo.png') {
            if (file_exists("../assets/images/icons/" . $logo_value)) {
                unlink("../assets/images/icons/" . $logo_value);
            }
            $new_logo_value = "";
        }
        
        // Process new logo file upload
        if (isset($_FILES['logo_img']) && $_FILES['logo_img']['error'] == 0) {
            if (!empty($logo_value) && $logo_value !== 'default_logo.png' && file_exists("../assets/images/icons/" . $logo_value)) {
                unlink("../assets/images/icons/" . $logo_value);
            }
            $file_tmp  = $_FILES['logo_img']['tmp_name'];
            $file_name = $_FILES['logo_img']['name'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['svg', 'png', 'jpg', 'jpeg'];
            
            if (!in_array($file_ext, $allowed_exts)) {
                $error_message = "فقط ملفات SVG و PNG و JPG و JPEG مسموح بها للشعار.";
            } else {
                $new_logo_value = date("YmdHis") . '.' . $file_ext;
                if (!move_uploaded_file($file_tmp, "../assets/images/icons/" . $new_logo_value)) {
                    $error_message = "فشل في تحميل صورة الشعار.";
                }
            }
        }
        
        if (empty($new_logo_value)) {
            $new_logo_value = "default_logo.png";
        }
        
        // Update query
        if ($temp_id == 1 || $temp_id == 3) {
            $sql_update = "UPDATE headline SET title = '$title', intro = '$intro', logo_img = '$new_logo_value' WHERE h_id = $h_id AND temp_id = $temp_id";
        } else {
            $sql_update = "UPDATE headline SET title = '$title', logo_img = '$new_logo_value' WHERE h_id = $h_id AND temp_id = $temp_id";
        }
        
        if (mysqli_query($conn, $sql_update)) {
            header("Location: temp{$temp_id}.php?h_id=$h_id");
            exit;
        } else {
            echo "<p>خطأ في تحديث السجل: " . mysqli_error($conn) . "</p>";
        }
    } elseif (isset($_POST['delete'])) {
        // Delete action - Remove dependent records first
        $sql_delete_content = "DELETE FROM content WHERE h_id = $h_id";
        if (!mysqli_query($conn, $sql_delete_content)) {
            echo "<p>خطأ في حذف المحتوى المرتبط: " . mysqli_error($conn) . "</p>";
            exit;
        }

        // Delete the headline record after removing related content
        $sql_delete_headline = "DELETE FROM headline WHERE h_id = $h_id AND temp_id = $temp_id";
        if (mysqli_query($conn, $sql_delete_headline)) {
            header("Location: index.php");
            exit;
        } else {
            echo "<p>خطأ في حذف العنوان: " . mysqli_error($conn) . "</p>";
        }
    }
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">تعديل العنوان</h1>

    <!-- Update Form -->
    <form method="post" action="edit_headline.php?headline_id=<?php echo $h_id; ?>&temp_id=<?php echo $temp_id; ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">عنوان العنوان</label>
            <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($title_value); ?>" required>
        </div>
        <?php if ($temp_id == 1 || $temp_id == 3): ?>
        <div class="form-group">
            <label for="intro">مقدمة</label>
            <textarea name="intro" id="intro" class="form-control" rows="5" required><?php echo htmlspecialchars($intro_value); ?></textarea>
        </div>
        <?php endif; ?>
        
        <!-- Logo Image Section -->
        <div class="form-group">
            <label for="logo_img">صورة الشعار</label>
            <?php if (!empty($logo_value)): ?>
                <img src="../assets/images/icons/<?php echo $logo_value; ?>" height="100" class="d-block mb-2">
                <div class="form-check">
                    <label for="delete_logo" class="form-check-label text-danger">حذف الشعار الحالي</label>
                    <input type="checkbox" name="delete_logo" class="form-check-input" id="delete_logo">
                </div>
            <?php endif; ?>
            <input type="file" name="logo_img" id="logo_img" class="form-control-file">
            <?php if (!empty($error_message)): ?>
                <p class="text-danger mt-2"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </div>
        
        <button type="submit" name="update" class="btn btn-primary">تحديث العنوان</button>
    </form>
    
    <hr>
    
    <!-- Delete Form with confirmation -->
    <form method="post" onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا العنوان؟');">
        <button type="submit" name="delete" class="btn btn-danger">حذف العنوان</button>
    </form>
</div>

<?php
require_once 'includes/footer.php';
?>
