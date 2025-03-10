<?php
    require_once 'includes/header.php';
    require_once 'includes/navbar.php';
    require_once 'includes/user.php';
    require_once 'includes/config.php';

    if (isset($_GET['h_id'])) {
        $headline_id = intval($_GET['h_id']);
    } else {
        echo "<p>No headline id provided.</p>";
        require_once "./includes/footer.php";
        exit;
    }

    $temp_id = 1;

    // Query to fetch headline data (title, intro, etc.)
    $sql_headline = "SELECT h.h_id, h.title, h.intro, h.temp_id
                 FROM headline h
                 WHERE h.h_id = $headline_id AND h.temp_id = $temp_id";
    $exe_headline = mysqli_query($conn, $sql_headline);
    if (! $exe_headline) {
        die("Error: " . mysqli_error($conn));
    }

    if ($row = mysqli_fetch_assoc($exe_headline)) {
        $head_id = $row['h_id'];
        $title   = $row['title'];
        $intro   = $row['intro'];
    } else {
        echo "<p>Record not found.</p>";
        require_once "./includes/footer.php";
        exit;
    }

    // Query to fetch content rows related to the headline
    $sql_content = "SELECT c.c_id, c.caption, c.detail
                FROM content c
                WHERE c.h_id = $headline_id";
    $exe_content = mysqli_query($conn, $sql_content);
    if (! $exe_content) {
        die("Error: " . mysqli_error($conn));
    }
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة صفحة "<?php echo $title; ?> "</h1>

        <div>
        <a href="add_content.php?headline_id=<?php echo urlencode($headline_id); ?>&temp_id=<?php echo urlencode($temp_id); ?>"

        class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fa-solid fa-plus fa-sm text-white-50"></i> إضافة نص
        </a>
        <a href="edit_page.php?headline_id=<?php echo urlencode($headline_id);?>&temp_id=<?php echo urlencode($temp_id);?>"

        class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fa-solid fa-pen-to-square fa-sm text-white-50"></i>  تعديل/ حذف الصفحة
        </a>
        </div>
    </div>

    <!-- Headline Intro Card -->
    <div class="mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            مقدمة :</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php
                                echo(strlen($intro) > 200 ? substr($intro, 0, strrpos(substr($intro, 0, 200), ' ')) . " ..." : $intro);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-sm-flex align-items-center justify-content-end mb-4">
        <div>
        <a href="edit_intro.php?h_id=<?php echo $headline_id; ?>&temp_id=<?php echo $temp_id; ?>"
           class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fa-solid fa-pen-to-square fa-sm text-white-50"></i> تعديل المقدمة
        </a>
        <a href="delete_intro.php?h_id=<?php echo $headline_id; ?>&temp_id=<?php echo $temp_id; ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fa-solid fa-delete-left fa-sm text-white-50"></i> حذف المقدمة
        </a>
        </div>
    </div>

    <!-- Content Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">نصوص الصفحة</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>النص</th>
                            <th>المصدر</th>
                            <th>حذف</th>
                            <th>تعديل</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        // Check if the content query returned any rows
                        if (mysqli_num_rows($exe_content) > 0) {
                            while ($row = mysqli_fetch_assoc($exe_content)) {
                                $c_id      = $row['c_id'];
                                $caption = $row['caption'];
                                $detail  = $row['detail'];

                                // Only output rows when data exists
                                if (! empty($c_id) && ! empty($caption) && ! empty($detail)) {
                                    echo '
                                <tr>
                                    <td>' . (strlen($caption) > 300 ? substr($caption, 0, strrpos(substr($caption, 0, 300), ' ')) . " ..." : $caption) . '</td>
                                    <td>' . (strlen($detail) > 100 ? substr($detail, 0, strrpos(substr($detail, 0, 100), ' ')) . "..." : $detail) . '</td>
                                    <td><a href="delete_table.php?headline_id=' .urlencode($head_id).'&c_id='.urlencode($c_id).'&temp_id='.urlencode($temp_id).'" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                            <i class="fa-solid fa-delete-left  fa-sm text-white-50"></i> حذف
                                        </a></td>
                                    <td>
                                        <a href="edit_table.php?headline_id=' .urlencode($head_id).'&c_id='.urlencode($c_id).'&temp_id='.urlencode($temp_id).'" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                            <i class="fa-solid fa-pen-to-square fa-sm text-white-50"></i> تعديل
                                        </a>
                                    </td>
                                </tr>';
                                }
                            }
                        } else {
                            echo "
                        <tr>
                            <td colspan='4' class='text-center'>لا توجد بيانات متاحة حاليًا.</td>
                        </tr>";
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php
    require_once 'includes/footer.php';
?>