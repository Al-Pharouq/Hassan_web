<?php 
ob_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/user.php';
require_once 'includes/config.php';

// Check if headline_id and temp_id are provided
if (!isset($_GET['headline_id']) || !isset($_GET['temp_id'])) {
    echo "<p>No headline id provided.</p>";
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
    $logo_value  = (isset($row['logo_img']) && !empty($row['logo_img'])) ? $row['logo_img'] : 'default_logo.png';
} else {
    echo "<p>Record not found.</p>";
    require_once 'includes/footer.php';
    exit;
}

// Process form submission for update or deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        // Update action
        
        // Escape the input values
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        if ($temp_id == 1 || $temp_id == 3) {
            $intro = mysqli_real_escape_string($conn, $_POST['intro']);
        } else {
            $intro = '';
        }
        
        // Initialize new logo value with the current logo value
        $new_logo_value = $logo_value;
        
        // If the delete checkbox for logo is checked, delete the current logo (if it's not already the default)
        if (isset($_POST['delete_logo']) && $logo_value !== 'default_logo.png') {
            if (file_exists("../assets/images/icons/" . $logo_value)) {
                unlink("../assets/images/icons/" . $logo_value);
            }
            $new_logo_value = "";
        }
        
        // Process new logo file upload if provided
        if (isset($_FILES['logo_img']) && $_FILES['logo_img']['error'] == 0) {
            // Optionally delete the old logo if it exists and is not default
            if (!empty($logo_value) && $logo_value !== 'default_logo.png' && file_exists("../assets/images/icons/" . $logo_value)) {
                unlink("../assets/images/ic/" . $logo_value);
            }
            $file_tmp  = $_FILES['logo_img']['tmp_name'];
            $file_name = $_FILES['logo_img']['name'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = array('svg', 'png', 'jpg', 'jpeg');
            
            if (!in_array($file_ext, $allowed_exts)) {
                $error_message = "Only SVG, PNG, JPG, and JPEG files are allowed for the logo.";
            } else {
                $new_logo_value = date("YmdHis") . '.' . $file_ext;
                if (!move_uploaded_file($file_tmp, "../assets/images/icons/" . $new_logo_value)) {
                    $error_message = "Failed to upload logo image.";
                }
            }
        }
        
        // If no new logo is set, default to "default_logo.png"
        if (empty($new_logo_value)) {
            $new_logo_value = "default_logo.png";
        }
        
        // Build the UPDATE query
        if ($temp_id == 1 || $temp_id == 3) {
            $sql_update = "UPDATE headline SET title = '$title', intro = '$intro', logo_img = '$new_logo_value' WHERE h_id = $h_id AND temp_id = $temp_id";
        } else {
            $sql_update = "UPDATE headline SET title = '$title', logo_img = '$new_logo_value' WHERE h_id = $h_id AND temp_id = $temp_id";
        }
        
        if (mysqli_query($conn, $sql_update)) {
            header("Location: temp{$temp_id}.php?h_id=$h_id");
            exit;
        } else {
            echo "<p>Error updating record: " . mysqli_error($conn) . "</p>";
        }
    } elseif (isset($_POST['delete'])) {
        // Delete action
        $sql_delete = "DELETE FROM headline WHERE h_id = $h_id AND temp_id = $temp_id";
        if (mysqli_query($conn, $sql_delete)) {
            header("Location: index.php");
            exit;
        } else {
            echo "<p>Error deleting record: " . mysqli_error($conn) . "</p>";
        }
    }
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Headline</h1>
    
    <!-- Update Form -->
    <form method="post" action="edit_headline.php?headline_id=<?php echo $h_id; ?>&temp_id=<?php echo $temp_id; ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Headline Title</label>
            <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($title_value); ?>" required>
        </div>
        <?php if ($temp_id == 1 || $temp_id == 3): ?>
        <div class="form-group">
            <label for="intro">Introduction</label>
            <textarea name="intro" id="intro" class="form-control" rows="5" required><?php echo htmlspecialchars($intro_value); ?></textarea>
        </div>
        <?php endif; ?>
        
        <!-- Logo Image Section -->
        <div class="form-group">
            <label for="logo_img">Logo Image</label>
            <?php if (!empty($logo_value)): ?>
                <img src="../assets/images/icons/<?php echo $logo_value; ?>" height="100" class="d-block mb-2">
                <div class="form-check">
                    <label for="delete_logo" class="form-check-label text-danger">Delete current logo</label>
                    <input type="checkbox" name="delete_logo" class="form-check-input" id="delete_logo">
                </div>
            <?php endif; ?>
            <input type="file" name="logo_img" id="logo_img" class="form-control-file">
            <?php if (!empty($error_message)): ?>
                <p class="text-danger mt-2"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </div>
        
        <button type="submit" name="update" class="btn btn-primary">Update Headline</button>
    </form>
    
    <hr>
    
    <!-- Delete Form with confirmation -->
    <form method="post" onsubmit="return confirm('Are you sure you want to delete this headline?');" action="edit_headline.php?headline_id=<?php echo $h_id; ?>&temp_id=<?php echo $temp_id; ?>">
        <button type="submit" name="delete" class="btn btn-danger">Delete Headline</button>
    </form>
</div>

<?php
require_once 'includes/footer.php';
?>
