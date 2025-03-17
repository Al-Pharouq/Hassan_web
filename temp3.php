<?php
// temp2.php

// Use the provided $headline_id; if not set, default to 4
$headline_id = $headline_id ?? 4;

// Determine if we're in full view mode (from more.php)
// Defaults to false (main page mode)
$isFullView = $isFullView ?? false;

// Query to fetch the headline details
$query1 = "SELECT title, intro, logo_img, main_img FROM headline WHERE h_id = ?";
$stmt1 = $conn->prepare($query1);
$stmt1->bind_param('i', $headline_id);
$stmt1->execute();
$result1 = $stmt1->get_result();

if ($result1 && $result1->num_rows > 0) {
    $row1    = $result1->fetch_assoc();
    $title   = htmlspecialchars($row1['title']);
    $intro   = htmlspecialchars($row1['intro']);
    $logo_img= htmlspecialchars($row1['logo_img']);
    $main_img= htmlspecialchars($row1['main_img']);
} else {
    $title = "No title found"; // Fallback if no title exists
}

// Set LIMIT clause based on view mode: no limit for full view
$limitClause = $isFullView ? "" : "LIMIT 3";

// Query to fetch related rows from content with appropriate limit
$query2 = "SELECT caption, detail FROM content WHERE h_id = ? $limitClause";
$stmt2 = $conn->prepare($query2);
$stmt2->bind_param('i', $headline_id);
$stmt2->execute();
$result2 = $stmt2->get_result();

// Begin rendering the HTML
echo '<section id="intro" data-sal="fade" data-sal-duration="1000">
        <div class="container py-5">
          <div>
            <div id="card-header" data-sal="fade" data-sal-duration="1200">
              <img src="assets/images/icons/' . $logo_img . '" alt="Tree Icon" />
              ' . $title . '
            </div>
            <div class="row">
              <h2 class="col-lg-9 col-md-12" data-sal="slide-up" data-sal-duration="1000">
                ' . $intro . '
              </h2>
              <div class="col-lg-4 mb-3 mb-lg-0" data-sal="zoom-in" data-sal-delay="300">
                <img src="assets/images/img/' . $main_img . '" alt="قصيدة آل حسان" class="poem-image" />
              </div>
              <div class="col-lg-8 intro-text" data-sal="fade" data-sal-duration="1200">';

// Check if related rows exist and output them
if ($result2 && $result2->num_rows > 0) {  
  while ($row = $result2->fetch_assoc()) {
    echo '<p data-sal="slide-up" data-sal-duration="1000">' . htmlspecialchars($row['caption']) . '</p>';
  }
} else {
    echo '<p>No news content found for the specified section_id.</p>';
}

// On main page view, show the "Read More" link
if (!$isFullView) {
  echo '<a href="more.php?h_id=' . urlencode($headline_id) . '" data-sal="fade" data-sal-duration="1200">قرائة المزيد ...</a>';
}
echo '      </div>
          </div>
        </div>
        <hr id="style" />
      </section>';
?>
