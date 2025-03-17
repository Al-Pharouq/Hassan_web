<?php
ob_start();
require_once 'includes/config.php';

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>لم يتم تحديد الشخص للحذف.</div>";
    exit;
}
$id = (int) $_GET['id'];

$stmt = $conn->prepare("DELETE FROM family_members WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: search.php");
    exit();
} else {
    echo "<div class='alert alert-danger'>حدث خطأ أثناء حذف الشخص.</div>";
}

$conn->close();
ob_end_flush();
?>
