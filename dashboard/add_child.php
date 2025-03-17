<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/config.php';
require_once 'includes/user.php';

// التأكد من تواجد معرف الأب
if (!isset($_GET['parent_id'])) {
    echo "<div class='alert alert-danger'>لم يتم تحديد الأب لإضافة ابن.</div>";
    exit;
}

$parent_id = (int) $_GET['parent_id'];

// جلب بيانات الأب لعرضها (اختياري)
$stmt = $conn->prepare("SELECT * FROM family_members WHERE id = ?");
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$parent = $stmt->get_result()->fetch_assoc();
if (!$parent) {
    echo "<div class='alert alert-danger'>الأب غير موجود.</div>";
    exit;
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $child_name = trim($_POST['child_name']);
    if (empty($child_name)) {
        $error = "الرجاء إدخال اسم الابن.";
    } else {
        // تطبيع الاسم
        function normalizeArabic($text) {
            $text = preg_replace('/[إأآا]/u', 'ا', $text);
            $text = preg_replace('/[ى]/u', 'ي', $text);
            $text = preg_replace('/[\x{064B}-\x{0652}]/u', '', $text);
            $text = preg_replace('/\x{0640}/u', '', $text);
            return $text;
        }
        $child_name = normalizeArabic($child_name);
        // إدخال الابن مع تحديد معرف الأب
        $stmt = $conn->prepare("INSERT INTO family_members (name, father_id) VALUES (?, ?)");
        $stmt->bind_param("si", $child_name, $parent_id);
        if ($stmt->execute()) {
            header("Location: search.php");
            exit();
        } else {
            $error = "حدث خطأ أثناء إضافة الابن.";
        }
    }
}
?>

<div class="container my-4">
    <h3 class="mb-4">إضافة ابن لـ <?php echo htmlspecialchars($parent['name']); ?></h3>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label for="child_name">اسم الابن:</label>
            <input type="text" name="child_name" id="child_name" class="form-control" required>
        </div>
        <br>
        <button type="submit" class="btn btn-success">إضافة الابن</button>
        <a href="javascript:history.back()" class="btn btn-secondary">عودة</a>
    </form>
</div>

<?php
require_once 'includes/footer.php';
$conn->close();
ob_end_flush();
?>
