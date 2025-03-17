<footer class="footer py-4">
  <div class="container">
    <div class="row">
      <!-- Logo Section -->
      <div class="col-lg-4 col-md-12 order-md-2 order-lg-3 text-center mb-2 d-lg-flex justify-content-center align-items-center">
        <img src="assets/images/theme/logo.png" alt="Logo" class="img-fluid mb-2" />
      </div>

      <div class="col-lg-4 col-md-6 order-lg-2 order-md-1 mb-4 mb-lg-0 text-center text-lg-start">
        <h5 class="text-white mb-3">روابط مفيدة</h5>
        <ul class="list-unstyled">
          <?php
          require_once 'includes/config.php';

          $sql = "SELECT * FROM resources";
          $result = mysqli_query($conn, $sql);
          if (!$result) {
              die("Error: " . mysqli_error($conn));
          }

          if (mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                  $name = $row['name'];
                  $link = $row['link'];
                  // Display the resource name as a clickable link that opens in a new tab
                  echo '<p><a class="text-white text-decoration-none" href="' . htmlspecialchars($link) . '" target="_blank">' . htmlspecialchars($name) . '</a></p>';
              }
          } else {
              echo "<p>No resources available.</p>";
          }
          ?>
        </ul>
      </div>

      <!-- Contact Section -->
      <div class="col-lg-4 col-md-6 order-md-3 text-md-end text-center">
        <h5 class="text-white">للتواصل</h5>
        <p class="text-white">admin@hassaan.net</p>
        <!-- Contact Us button added -->
        <a href="contact_us.php" class="custom-btn mt-2 mb-4">تواصل بنا</a>
      </div>
    </div>

    <!-- Copyright Notice -->
    <p class="text-white mb-0 ms-lg-3 text-center">© جميع الحقوق محفوظة لعائلة آل حسان</p>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script src="assets/main.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    sal({
        threshold: 0.2,  // Trigger animation when 20% of the element is visible
        once: false,      // Re-run animations when scrolling back
    });
});
</script>
</body>
</html>
