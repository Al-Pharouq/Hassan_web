<?php
require_once('includes/config.php');

// Check if the content id is provided
if (!isset($_GET['c_id'])) {
    echo "لم يتم توفير معرف المحتوى.";



    exit;
}

$c_id = intval($_GET['c_id']);

// Delete the content record from the content table
$sql = "DELETE FROM content WHERE c_id = $c_id;";
$exe = mysqli_query($conn, $sql);

if (!$exe) {
    echo "خطأ في الحذف: " . mysqli_error($conn);



    exit;
}

// Redirect back to the referring page
header("Location: " . $_SERVER['HTTP_REFERER']);
mysqli_close($conn);
?>
