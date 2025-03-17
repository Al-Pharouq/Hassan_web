<?php
// temp4.php

// Define the headline id; default to 3 if not provided.
$headline_id = $headline_id ?? 3;

// Determine if we're in full view mode (from more.php); default to false (main page mode)
$isFullView = $isFullView ?? false;

// Query to fetch the headline details
$query1 = "SELECT title, logo_img FROM headline WHERE h_id = ?";
$stmt1 = $conn->prepare($query1);
$stmt1->bind_param('i', $headline_id);
$stmt1->execute();
$result1 = $stmt1->get_result();

if ($result1 && $result1->num_rows > 0) {
    $row1 = $result1->fetch_assoc();
    $title = htmlspecialchars($row1['title']);
    $logo_img = htmlspecialchars($row1['logo_img']);
} else {
    $title = "No title found";
}

// Set the LIMIT clause based on view mode
$limitClause = $isFullView ? "" : "LIMIT 3";

// Query to fetch related content
$query2 = "SELECT caption, detail, img FROM content WHERE h_id = ? $limitClause";
$stmt2 = $conn->prepare($query2);
$stmt2->bind_param('i', $headline_id);
$stmt2->execute();
$result2 = $stmt2->get_result();

// Begin rendering the HTML
echo '<section id="figures">
        <div class="container mt-5">
          <div class="content-box">
            <div id="card-header" data-sal="fade" data-sal-duration="1000">
               <img src="assets/images/icons/' . $logo_img . '" alt="Tree Icon" />
              ' . $title . '
            </div>';

$isImageRight = true;

// Check if related rows exist
if ($result2 && $result2->num_rows > 0) {
    while ($row = $result2->fetch_assoc()) {
        $caption = htmlspecialchars($row['caption']);
        $detail = htmlspecialchars($row['detail']);
        $img = htmlspecialchars($row['img']);

        if ($isImageRight) {
            // Image on the left
            echo '<div class="row align-items-center">
                    <h2 class="col-lg-10 col-md-12" data-sal="slide-up" data-sal-delay="200">' . $caption . '</h2>
                    <div class="row">
                      <div class="d-flex flex-column flex-lg-row">
                        <div class="col-lg-4 mb-3 mb-lg-0 order-1 order-lg-1" data-sal="zoom-in" data-sal-delay="400">
                          <img src="assets/images/img/' . $img . '" alt="قصيدة آل حسان" class="poem-image" />
                        </div>
                        <div class="col-12 col-lg-8 intro-text order-2 order-lg-2" data-sal="fade" data-sal-delay="600">
                          <p>' . $detail . '</p>
                        </div>
                      </div>
                    </div>
                  </div>';
        } else {
            // Image on the right
            echo '<div class="row align-items-center">
                    <h2 class="col-lg-10 col-md-12" data-sal="slide-up" data-sal-delay="200">' . $caption . '</h2>
                    <div class="row">
                      <div class="d-flex flex-column flex-lg-row">
                        <div class="col-lg-4 mb-3 mb-lg-0 order-1 order-lg-2" data-sal="zoom-in" data-sal-delay="400">
                          <img src="assets/images/img/' . $img . '" alt="قصيدة آل حسان" class="poem-image" />
                        </div>
                        <div class="col-12 col-lg-8 intro-text order-2 order-lg-1" data-sal="fade" data-sal-delay="600">
                          <p>' . $detail . '</p>
                        </div>
                      </div>
                    </div>
                  </div>';
        }

        $isImageRight = !$isImageRight;
    }
} else {
    echo '<p>No figures found for the specified section_id.</p>';
}

if (!$isFullView) {
    echo '<div class="intro-text"><a href="more.php?h_id=' . urlencode($headline_id) . '" data-sal="fade" data-sal-duration="1000">قراءة المزيد ...</a></div>';
}

echo '        </div>
            <hr id="style"/>
          </div>
        </section>';
?>
