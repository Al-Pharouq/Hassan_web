<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar" style="
    padding-right: 0px;
">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <div class="sidebar-brand-text mx-3">موقع آل حسان</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>الرئيسية</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        لوحة التحكم
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-solid fa-pager"></i>
            <span>الصفحات</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">الصفحات المتاحة:</h6>
                <?php
                require_once 'config.php';
                 $sql = "SELECT h_id, title, temp_id FROM headline ORDER BY sort_order ASC";
                    $exe = mysqli_query($conn, $sql);
                    if (!$exe) {
                        die("Error: " . mysqli_error($conn));
                    }
                    while ($row = mysqli_fetch_assoc($exe)) {
                        $h_id = $row['h_id'];
                        $title = $row['title'];
                        $temp_id = $row['temp_id'];
                        echo '<a class="collapse-item" href="temp' . $temp_id . '.php?h_id=' . urlencode($h_id) . '">' . $title . '</a>';
                    }
                ?>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="reorder_headlines.php">
            <i class="fas fa-solid fa-sort"></i>
            <span> ترتيب الصفحات </span></a>
    </li>

    <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
            aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-solid fa-passport"></i>
            <span>القوالب</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">القوالب المتاحة:</h6>
                <?php
                 $sql = "SELECT temp_id, temp_name FROM template";
                    $exe = mysqli_query($conn, $sql);
                    if (!$exe) {
                        die("Error: " . mysqli_error($conn));
                    }
                    while ($row = mysqli_fetch_assoc($exe)) {
                        $temp_id = $row['temp_id'];
                        $temp_name = $row['temp_name'];

                        echo '<a class="collapse-item" href="template.php?temp_id=' . urlencode($temp_id) . '">' . $temp_name . '</a>';
                    }
                ?>
            </div>
        </div>
    </li>

        <!-- Nav Item - Tables -->
        <li class="nav-item">
        <a class="nav-link" href="search.php">
            <i class="fas fa-fw fa-solid fa-sitemap"></i>
            <span>شجرة العائلة</span></a>
    </li>
    </li>

        <!-- Nav Item - Tables -->
        <li class="nav-item">
        <a class="nav-link" href="links.php">
            <i class="fas fa-solid fa-link"></i>
            <span> المصادر </span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->