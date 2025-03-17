<?php
require_once 'includes/config.php';

// دالة لتطبيع النص العربي لضمان التناسق (إزالة التشكيل، التطويل، وتحويل أشكال الحروف)
function normalizeArabic($text) {
    // تحويل أشكال الألف المختلفة إلى ألف قياسية
    $text = preg_replace('/[إأآا]/u', 'ا', $text);
    // تحويل الياء المختلفة (مثلاً ى) إلى ي
    $text = preg_replace('/[ى]/u', 'ي', $text);
    // إزالة التشكيل (الحركات)
    $text = preg_replace('/[\x{064B}-\x{0652}]/u', '', $text);
    // إزالة التطويل
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
    
    return $lineage; // إرجاع الأجداد فقط
}

// وظيفة للتحقق مما إذا كانت الأسماء الثلاثة هي أول ثلاثة أسماء في السلالة
function containsOrderedNames($lineage, $names) {
    // التأكد من وجود ثلاثة عناصر على الأقل في السلالة
    if (count($lineage) < 3) {
        return false;
    }
    // تحقق من أن الأسماء الثلاثة الأولى تتطابق مع المدخلات بالترتيب
    return (
        $lineage[0] === $names[0] &&
        $lineage[1] === $names[1] &&
        $lineage[2] === $names[2]
    );
}

// وظيفة للبحث عن الأشخاص الذين تبدأ سلالتهم بالأسماء الثلاثة المدخلة بالترتيب الصحيح
function findMatchingAncestors($conn, $names) {
    $stmt = $conn->query("SELECT * FROM family_members");
    $matchingResults = [];

    while ($person = $stmt->fetch_assoc()) {
        // متابعة البحث فقط إذا كان اسم الشخص يطابق الاسم الأول للمدخلات
        if ($person['name'] !== $names[0]) {
            continue;
        }
        $lineage = getAncestors($conn, $person); // استرجاع السلالة (الشخص + الأجداد)
        if (containsOrderedNames($lineage, $names)) {
            $matchingResults[] = [
                "person" => $person["name"],
                "full_name" => implode(" بن ", $lineage) // عرض السلالة الكاملة
            ];
        }
    }
    
    return $matchingResults;
}

// تنفيذ البحث عند تقديم النموذج
$results = [];
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // تطبيع المدخلات للتناسق
    $names_input = normalizeArabic(trim($_POST["names"]));
    // تقسيم المدخلات باستخدام المسافة
    $names_arr = array_map(function($name) {
        return normalizeArabic(trim($name));
    }, explode(' ', $names_input));
    
    if (count($names_arr) !== 3) {
        $error = "يجب إدخال ثلاثة أسماء مفصولة بمسافة.";
    } else if (!empty($names_arr[0]) && !empty($names_arr[1]) && !empty($names_arr[2])) {
        $results = findMatchingAncestors($conn, $names_arr);
    }
}

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Search Card -->
            <div class="card shadow mb-4">
                <div class="card-header form_c text-white">
                    <h4 class="mb-0">بحث في شجرة آل حسان</h4>
                </div>
                <div class="card-body">
                    <form method="post" class="mb-3">
                        <div class="input-group">
                            <!-- Updated placeholder to match splitting by space -->
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
                                            <th>السلالة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $result): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($result["full_name"]) . " حسان "; ?></td>
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
?>
