<?php
require_once 'includes/config.php';

// Ensure the required parameters are provided
if (!isset($_GET['h_id']) || !isset($_GET['temp_id'])) {
    echo "المعلمات المطلوبة مفقودة.";



    exit;
}

$h_id = intval($_GET['h_id']);
$temp_id = intval($_GET['temp_id']);

// For temp_id = 3, delete both intro and main_img; otherwise, delete only intro.
if ($temp_id == 3) {
    $sql = "UPDATE headline SET intro = NULL, main_img = NULL WHERE h_id = $h_id AND temp_id = $temp_id;";
} else {
    $sql = "UPDATE headline SET intro = NULL WHERE h_id = $h_id AND temp_id = $temp_id;";
}

$exe = mysqli_query($conn, $sql);
if (!$exe) {
    echo "خطأ في الحذف: " . mysqli_error($conn);



    exit;
}

// Redirect back to the referring page
header("Location: " . $_SERVER['HTTP_REFERER']);
mysqli_close($conn);
?>
