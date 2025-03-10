<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/user.php';
require_once 'includes/config.php';

// Check if required parameters are provided
if (!isset($_GET['c_id']) || !isset($_GET['headline_id']) || !isset($_GET['temp_id'])) {
    echo "<p>المعلمات المطلوبة مفقودة</p>";
    require_once "includes/footer.php";
    exit;
}

$c_id = intval($_GET['c_id']);
$headline_id = intval($_GET['headline_id']);
$temp_id = intval($_GET['temp_id']);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get new values and escape them to prevent SQL injection
    $caption = mysqli_real_escape_string($conn, $_POST['caption']);
    $detail  = mysqli_real_escape_string($conn, $_POST['detail']);

    // Update the content record with the new caption and detail
    $update_query = "UPDATE content SET caption = '$caption', detail = '$detail' WHERE c_id = $c_id";
    if (mysqli_query($conn, $update_query)) {
        // Redirect back to the headline page (or list page) after a successful update
        header("Location: temp$temp_id.php?h_id=$headline_id");
        exit;
    } else {
        echo "<p>خطأ أثناء تحديث السجل: " . mysqli_error($conn) . "</p>";
    }
}

// Retrieve the existing record to pre-fill the form
$select_query = "SELECT caption, detail FROM content WHERE c_id = $c_id";
$result = mysqli_query($conn, $select_query);
if (!$result) {
    die("خطأ: " . mysqli_error($conn));
}

if ($row = mysqli_fetch_assoc($result)) {
    $caption_value = $row['caption'];
    $detail_value  = $row['detail'];
} else {
    echo "<p>السجل غير موجود.</p>";
    require_once "includes/footer.php";
    exit;
}
?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">تعديل النص</h1>
    <!-- Note the updated action URL with temp_id included -->
    <form method="post" action="edit_table.php?c_id=<?php echo $c_id; ?>&headline_id=<?php echo $headline_id; ?>&temp_id=<?php echo $temp_id; ?>">
        <div class="form-group">
            <label for="caption">النص</label>
            <textarea name="caption" id="caption" class="form-control" rows="3" required><?php echo htmlspecialchars($caption_value); ?></textarea>
        </div>
        <div class="form-group">
            <label for="detail">المصدر</label>
            <textarea name="detail" id="detail" class="form-control" rows="3" required><?php echo htmlspecialchars($detail_value); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">تحديث</button>
    </form>
</div>
<?php
require_once 'includes/footer.php';
?>
