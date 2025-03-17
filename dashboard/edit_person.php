<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/config.php';
require_once 'includes/user.php';

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>لم يتم تحديد الشخص للتعديل.</div>";
    exit;
}
$id = (int) $_GET['id'];

// جلب بيانات الشخص
$stmt = $conn->prepare("SELECT * FROM family_members WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$person = $result->fetch_assoc();
if (!$person) {
    echo "<div class='alert alert-danger'>الشخص غير موجود.</div>";
    exit;
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
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
        $stmt = $conn->prepare("UPDATE family_members SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        if ($stmt->execute()) {
            header("Location: search.php");
            exit();
        } else {
            $error = "حدث خطأ أثناء تحديث بيانات الشخص.";
        }
    }
}
?>

<div class="container my-4">
    <h3 class="mb-4">تعديل بيانات الشخص</h3>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label for="name">اسم الشخص:</label>
            <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($person['name']); ?>" required>
        </div>
        <br>
        <button type="submit" class="btn btn-warning">تحديث البيانات</button>
        <a href="javascript:history.back()" class="btn btn-secondary">عودة</a>
    </form>
</div>

<?php
require_once 'includes/footer.php';
$conn->close();
ob_end_flush();
?>
