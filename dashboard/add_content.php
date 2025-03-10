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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve posted values and escape them to prevent SQL injection
    $caption = mysqli_real_escape_string($conn, $_POST['caption']);
    $detail  = mysqli_real_escape_string($conn, $_POST['detail']);

    // Insert the new content record into the content table
    $sql = "INSERT INTO content (h_id, caption, detail) VALUES ($headline_id, '$caption', '$detail')";
    if (mysqli_query($conn, $sql)) {
        // Redirect back to the headline content page after successful insertion
        header("Location: temp{$temp_id}.php?h_id=$headline_id");
        exit;
    } else {
        echo "<p>Error inserting record: " . mysqli_error($conn) . "</p>";
    }
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Add New Content</h1>
    <!-- Include both headline_id and temp_id in the action URL -->
    <form method="post" action="add_content.php?headline_id=<?php echo $headline_id; ?>&temp_id=<?php echo $temp_id; ?>">
        <div class="form-group">
            <label for="caption">Caption</label>
            <textarea name="caption" id="caption" class="form-control" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="detail">Detail</label>
            <textarea name="detail" id="detail" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<?php
require_once 'includes/footer.php';
?>
