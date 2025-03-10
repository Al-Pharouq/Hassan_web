<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/user.php';
require_once 'includes/config.php';

// Check if the headline id is provided
if (!isset($_GET['h_id']) || !isset($_GET['temp_id'])) {
    echo "<p>No headline id provided.</p>";
    require_once "includes/footer.php";
    exit;
}


$h_id = intval($_GET['h_id']);
$temp_id = intval($_GET['temp_id']);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get updated intro text and escape it to prevent SQL injection
    $intro = mysqli_real_escape_string($conn, $_POST['intro']);

    // Update the headline record with the new intro
    $update_query = "UPDATE headline SET intro = '$intro' WHERE h_id = $h_id AND temp_id = $temp_id";
    if (mysqli_query($conn, $update_query)) {
        // Redirect back to the headline page after a successful update
        header("Location: temp$temp_id.php?h_id=$h_id");
        exit;
    } else {
        echo "<p>Error updating record: " . mysqli_error($conn) . "</p>";
    }
}

// Retrieve the current intro to pre-fill the form
$select_query = "SELECT intro FROM headline WHERE h_id = $h_id AND temp_id = $temp_id";
$result = mysqli_query($conn, $select_query);
if (!$result) {
    die("Error: " . mysqli_error($conn));
}

if ($row = mysqli_fetch_assoc($result)) {
    $intro_value = $row['intro'];
} else {
    echo "<p>Record not found.</p>";
    require_once "includes/footer.php";
    exit;
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">تعديل المقدمة</h1>
    <form method="post" action="edit_intro.php?h_id=<?php echo $h_id; ?>&temp_id=<?php echo $temp_id; ?>">
        <div class="form-group">
            <label for="intro">المقدمة</label>
            <textarea name="intro" id="intro" class="form-control" rows="5" required><?php echo htmlspecialchars($intro_value); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">تعديل</button>
    </form>
</div>

<?php
require_once 'includes/footer.php';