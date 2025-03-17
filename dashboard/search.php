<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/user.php';
require_once 'includes/config.php';

// دالة لتطبيع النص العربي لضمان التناسق (إزالة التشكيل، التطويل، وتحويل أشكال الحروف)
function normalizeArabic($text) {
    $text = preg_replace('/[إأآا]/u', 'ا', $text);
    $text = preg_replace('/[ى]/u', 'ي', $text);
    $text = preg_replace('/[\x{064B}-\x{0652}]/u', '', $text);
    $text = preg_replace('/\x{0640}/u', '', $text);
    return $text;
}

// وظيفة لاسترجاع الأجداد فقط لشخص معين
function getAncestors($conn, $person) {
    $lineage = [$person['name']]; // ابدأ بالشخص نفسه
    $father_id = $person['father_id'];
    while ($father_id) {
        $stmt = $conn->prepare("SELECT * FROM family_members WHERE id = ?");
        $stmt->bind_param("i", $father_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $father = $result->fetch_assoc();
        if ($father) {
            $lineage[] = $father['name'];
            $father_id = $father['father_id']; // الانتقال إلى الجيل الأعلى
        } else {
            break;
        }
    }
    return $lineage;
}

// وظيفة للتحقق مما إذا كانت الأسماء الثلاثة هي أول ثلاثة أسماء في السلالة
function containsOrderedNames($lineage, $names) {
    if (count($lineage) < 3) {
        return false;
    }
    return ($lineage[0] === $names[0] && $lineage[1] === $names[1] && $lineage[2] === $names[2]);
}

// وظيفة للبحث عن الأشخاص الذين تبدأ سلالتهم بالأسماء الثلاثة المدخلة بالترتيب الصحيح
function findMatchingAncestors($conn, $names) {
    $stmt = $conn->query("SELECT * FROM family_members");
    $matchingResults = [];
    while ($person = $stmt->fetch_assoc()) {
        // البحث فقط إذا كان اسم الشخص يطابق الاسم الأول
        if ($person['name'] !== $names[0]) {
            continue;
        }
        $lineage = getAncestors($conn, $person);
        if (containsOrderedNames($lineage, $names)) {
            $matchingResults[] = [
                "id" => $person["id"],
                "person" => $person["name"],
                "full_name" => implode(" بن ", $lineage)
            ];
        }
    }
    return $matchingResults;
}

$error = '';
$results = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // تطبيع المدخلات للتناسق وتقسيمها بواسطة المسافة
    $names_input = normalizeArabic(trim($_POST["names"]));
    $names_arr = array_map('trim', explode(' ', $names_input));
    // تطبيع كل اسم
    $names_arr = array_map('normalizeArabic', $names_arr);
    
    if (count($names_arr) !== 3) {
        $error = "يجب إدخال ثلاثة أسماء مفصولة بمسافة.";
    } else if (!empty($names_arr[0]) && !empty($names_arr[1]) && !empty($names_arr[2])) {
        $results = findMatchingAncestors($conn, $names_arr);
    }
}
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Search Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0">بحث في شجرة آل حسان</h4>
                </div>
                <div class="card-body">
                    <form method="post" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="names" class="form-control" placeholder="أدخل ثلاثة أسماء مفصولة بمسافة" aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn" type="submit">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-warning"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error)): ?>
                        <h5 class="mb-3">نتائج البحث:</h5>
                        <?php if (!empty($results)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>الاسم</th>
                                            <th>السلالة</th>
                                            <th>إضافة ابن</th>
                                            <th>تعديل</th>
                                            <th>حذف</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $result): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($result["person"]); ?></td>
                                                <td><?php echo htmlspecialchars($result["full_name"]) . " حسان"; ?></td>
                                                <td>
                                                    <a href="add_child.php?parent_id=<?php echo urlencode($result['id']); ?>" class="btn btn-success btn-sm">
                                                        <i class="fas fa-plus"></i> إضافة ابن
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="edit_person.php?id=<?php echo urlencode($result['id']); ?>" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i> تعديل
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="delete_person.php?id=<?php echo urlencode($result['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا الشخص؟');">
                                                        <i class="fas fa-trash-alt"></i> حذف
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">لم يتم العثور على أي تطابق للأسماء المدخلة بالترتيب الصحيح.</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$conn->close();
ob_end_flush();
?>
