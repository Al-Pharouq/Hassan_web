<?php
require_once 'includes/config.php';

// Ensure the required parameters are provided
if (!isset($_GET['h_id']) || !isset($_GET['temp_id'])) {
    echo "Missing parameters.";
    exit;
}

$h_id = intval($_GET['h_id']);
$temp_id = intval($_GET['temp_id']);

// Update the headline record: set intro to NULL for the provided h_id and temp_id
$sql = "UPDATE headline SET intro = NULL WHERE h_id = $h_id AND temp_id = $temp_id;";
$exe = mysqli_query($conn, $sql);

if (!$exe) {
    echo "Delete Error: " . mysqli_error($conn);
    exit;
}

// Redirect back to the referring page
header("Location: " . $_SERVER['HTTP_REFERER']);
mysqli_close($conn);
?>
