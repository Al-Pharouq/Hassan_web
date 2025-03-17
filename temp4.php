<?php
// temp3.php

// Define the headline id; default to 2 if not provided.
$headline_id = $headline_id ?? 2;

// Set the full view flag; default to false (main page mode)
$isFullView = $isFullView ?? false;

// Query to fetch the headline title and logo image.
$query1 = "SELECT title, logo_img FROM headline WHERE h_id = ?";
$stmt1 = $conn->prepare($query1);
$stmt1->bind_param('i', $headline_id);
$stmt1->execute();
$result1 = $stmt1->get_result();

if ($result1 && $result1->num_rows > 0) {
    $row1    = $result1->fetch_assoc();
    $title   = htmlspecialchars($row1['title']);
    $logo_img = htmlspecialchars($row1['logo_img']);
} else {
    $title = "No title found";
}

// Determine the LIMIT clause based on view mode.
$limitClause = $isFullView ? "" : "LIMIT 3";

// Query to fetch related content rows.
$query2 = "SELECT caption, detail FROM content WHERE h_id = ? $limitClause";
$stmt2 = $conn->prepare($query2);
$stmt2->bind_param('i', $headline_id);
$stmt2->execute();
$result2 = $stmt2->get_result();

// Begin rendering the HTML.
echo '<section id="news">
        <div class="container py-5">
          <div>
            <div id="card-header" data-sal="fade" data-sal-duration="1000">
              <img src="assets/images/icons/' . $logo_img . '" alt="Tree Icon" />
              ' . $title . '
            </div>
            <div id="card-body-news">';

// Check if content exists and display it.
if ($result2 && $result2->num_rows > 0) {
    // Alternating classes and animations.
    $highlight_class = 'highlight_r';
    $animation = 'slide-right';
    
    while ($row = $result2->fetch_assoc()) {
        echo '<div class="' . $highlight_class . '" data-sal="' . $animation . '" data-sal-duration="1000">
                <h3>' . htmlspecialchars($row['caption']) . '</h3>
                <p>' . htmlspecialchars($row['detail']) . '</p>
              </div>';
        // Toggle alternating classes and animation directions.
        $highlight_class = ($highlight_class === 'highlight_r') ? 'highlight_l' : 'highlight_r';
        $animation = ($animation === 'slide-right') ? 'slide-left' : 'slide-right';
    }
} else {
    echo '<p>No news content found for the specified headline.</p>';
}

echo '      </div>';

// Display the "Read More" link only if not in full view.
if (!$isFullView) {
  echo '<div class="intro-text"><a href="more.php?h_id=' . urlencode($headline_id) . '" data-sal="fade" data-sal-duration="1000">قراءة المزيد ...</a></div>';
}

echo '    </div>
        </div>
        <hr id="style" />
      </section>';
?>
