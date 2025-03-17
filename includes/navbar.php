        <?php
        require_once 'includes/config.php';

        // Query the nav_items table for all navigation links, ordered by nav_order
        $sql_nav = "SELECT h_id, title FROM headline ORDER BY sort_order ASC";
        $result_nav = mysqli_query($conn, $sql_nav);

        if (!$result_nav) {
            die("Error fetching navbar items: " . mysqli_error($conn));
        }
        ?>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <!-- Logo -->
                <a class="navbar-brand logo" href="index.php" data-aos="fade-down-left">
                    <img src="assets/images/theme/logo.png" alt="Logo" />
                </a>
                <!-- Toggle button for mobile view -->
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNav"
                    aria-controls="navbarNav"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Navigation Links -->
                <div class="collapse navbar-collapse" id="navbarNav"  data-aos="fade-down-right">
                    <ul class="navbar-nav ms-auto box">
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="index.php#first">أهمية معرفة الانساب</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#intro">التعريف بآل حسان</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#figures"> أعيان آل حسان</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#tree"> شجرة العائلة</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#news"> أخبار آل حسان</a>
                    </li> -->
                    <?php while ($row = mysqli_fetch_assoc($result_nav)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#<?php echo htmlspecialchars($row['h_id']); ?>">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </a>
                    </li>
                    <?php endwhile; ?>

                    <li class="nav-item">
                        <a class="nav-link" href="search.php">ابحث عن اسمك</a>
                    </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- End of Navbar -->