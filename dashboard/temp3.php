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
        $temp_id = 3;
        $sql = "SELECT
                h.h_id, h.title, h.intro, h.temp_id, h.main_img, c.c_id, c.caption
            FROM
                headline h
            LEFT JOIN
                content c
            ON
                h.h_id = c.h_id
            WHERE
                h.h_id = $headline_id and h.temp_id =   $temp_id";
        $exe = mysqli_query($conn, $sql);
        if (!$exe) {
            die("Error: " . mysqli_error($conn));
        }

        // Fetch the intro
        $intro = '';
        if ($row = mysqli_fetch_assoc($exe)) {
            $head_id = $row['h_id'];
            $title = $row['title'];
            $intro = $row['intro'];
        }
        
echo '
    
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">إدارة صفحة " ' . $title . ' "</h1>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fa-solid fa-plus fa-sm text-white-50"></i> إضافة نص</a>
        </div>
        <!-- Page Heading -->
        <!-- Earnings (Monthly) Card Example -->
        <div class=" mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                              مقدمة :  </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">' .(strlen($intro) > 150 ? substr($intro, 0, strrpos(substr($intro, 0, 150), ' ')) . "..." : $intro). '</div>
                        </div>
                        <!-- <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Basic Card Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">صورة تعريفية :</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 25rem;"
                        src="data:image/jpeg;base64,'.base64_encode($row['main_img']).'" alt="لاتوجد صورة">
                </div>
            </div>
        </div>
       <div class="d-sm-flex align-items-center justify-content-end mb-4">
                <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fa-solid fa-pen-to-square fa-sm text-white-50"></i>  تعديل المقدمة</a>
                <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fa-solid fa-delete-left fa-sm text-white-50"></i>  حذف المقدمة</a>
        </div>';?>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">النصوص</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>النص</th>
                                <th>تعديل</th>
                                <th>حذف</th>
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
        $id = $row['c_id'];
        $caption = $row['caption'];

        // Only output rows when data exists
        if (!empty($id) && !empty($caption)) {
            echo '
                            <tr>
                                <td>' . (strlen($caption) > 300 ? substr($caption, 0, strrpos(substr($caption, 0, 300), ' ')) . " ..." : $caption) . '</td>
                                <td>تعديل</td>
                                <td>حذف</td>
                            </tr>';
        }else {
            // Display message if no rows are found
            echo "
            <tr>
                <td colspan='3' class='text-center'>لا توجد بيانات متاحة حاليًا.</td>
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