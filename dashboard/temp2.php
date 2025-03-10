<?php
    require_once 'includes/header.php';
    require_once 'includes/navbar.php';
    require_once 'includes/user.php';
    ?>
<?php
        require_once 'includes/config.php';
        if (isset($_GET['h_id'])) {
            $headline_id = intval($_GET['h_id']);
        } else {
            echo "<p>No headline id provided.</p>";
            require_once "./includes/footer.php";
            exit;
        }
        $temp_id = 2;
        $sql = "SELECT
                h.h_id, h.title, h.temp_id, c.c_id, c.caption, c.detail, c.img
            FROM
                headline h
            LEFT JOIN
                content c
            ON
                h.h_id = c.h_id
            WHERE
                h.h_id = $headline_id and h.temp_id = $temp_id";
        $exe = mysqli_query($conn, $sql);
        if (!$exe) {
            die("Error: " . mysqli_error($conn));
        }

        // Fetch the intro
        $intro = '';
        if ($row = mysqli_fetch_assoc($exe)) {
            $head_id = $row['h_id'];
            $title = $row['title'];
        }
echo'
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">إدارة صفحة " ' . $title . ' "</h1>
                   <div>
      <div>
        <a href="edit_intro.php?h_id='. $headline_id.'&temp_id='. $temp_id.'"
           class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fa-solid fa-pen-to-square fa-sm text-white-50"></i> تعديل المقدمة
        </a>
        <a href="delete_intro.php?h_id='.$headline_id.'&temp_id='. $temp_id.'" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fa-solid fa-delete-left fa-sm text-white-50"></i> حذف المقدمة
        </a>
        </div>
        </div>
        </div>';
        ?>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">النصوص</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>الترجمة</th>
                                <th>صورة</th>
                                <th>حذف</th>
                                <th>تعديل</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php
// Check if the query returned any rows
if (mysqli_num_rows($exe) > 0) {
    // Reset the result pointer
    mysqli_data_seek($exe, 0);

    // Loop through all content rows
    while ($row = mysqli_fetch_assoc($exe)) {
        $c_id = $row['c_id'];
        $caption = $row['caption'];
        $detail = $row['detail'];
        $img = $row['img'];

        // Only output rows when data exists
        if (!empty($c_id) && !empty($caption) && !empty($detail)) {
            echo '
                            <tr>
                                <td>' . $caption . '</td>
                                <td>' . (strlen($detail) > 100 ? substr($detail, 0, strrpos(substr($detail, 0, 100), ' ')) . "..." : $detail) . '</td>
                                <td><img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 7rem;" src="../assets/images/img/' . $img . '" alt=".."></td>
                                <td><a href="delete_table.php?headline_id=' .urlencode($head_id).'&c_id='.urlencode($c_id).'&temp_id='.urlencode($temp_id).'" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                            <i class="fa-solid fa-delete-left  fa-sm text-white-50"></i> حذف
                                        </a>
                                </td>
                                <td>
                                    <a href="edit_table.php?headline_id=' .urlencode($head_id).'&c_id='.urlencode($c_id).'&temp_id='.urlencode($temp_id).'" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                        <i class="fa-solid fa-pen-to-square fa-sm text-white-50"></i> تعديل
                                    </a>
                                </td>
                            </tr>';
        }else {
            // Display message if no rows are found
            echo "
            <tr>
                <td colspan='5' class='text-center'>لا توجد بيانات متاحة حاليًا.</td>
              </tr>";
        }
    }
}
?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>
    <!-- /.container-fluid -->

</div>
<!-- End of Main Content -->


<?php
    require_once 'includes/footer.php';
