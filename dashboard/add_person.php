<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/config.php';
require_once 'includes/user.php';

$error = '';
$success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $father_id = isset($_POST['father_id']) ? (int)$_POST['father_id'] : 0; // 0 أو تركه فارغ يعني لا يوجد أب
    if (empty($name)) {
        $error = "الرجاء إدخال اسم الشخص.";
    } else {
        function normalizeArabic($text) {
            $text = preg_replace('/[إأآا]/u', 'ا', $text);
            $text = preg_replace('/[ى]/u', 'ي', $text);
            $text = preg_replace('/[\x{064B}-\x{0652}]/u', '', $text);
            $text = preg_replace('/\x{0640}/u', '', $text);
            return $text;
        }
        $name = normalizeArabic($name);
        $stmt = $conn->prepare("INSERT INTO family_members (name, father_id) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $father_id);
        if ($stmt->execute()) {
            $success = "تم إضافة الشخص بنجاح.";
        } else {
            $error = "حدث خطأ أثناء إضافة الشخص.";
        }
    }
}
?>

<div class="container my-4">
    <h3 class="mb-4">إضافة شخص جديد</h3>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label for="name">اسم الشخص:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="father_id">معرف الأب (اختياري):</label>
            <input type="number" name="father_id" id="father_id" class="form-control">
        </div>
        <br>
        <button type="submit" class="btn btn-primary">إضافة الشخص</button>
        <a href="javascript:history.back()" class="btn btn-secondary">عودة</a>
    </form>
</div>

<?php
require_once 'includes/footer.php';
$conn->close();
ob_end_flush();
?>
