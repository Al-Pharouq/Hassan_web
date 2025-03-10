<?php
// temp1.php

// Use the provided $headline_id; if not set, fallback to a default value.
if (!isset($headline_id)) {
    $headline_id = 1;
}

// Determine if we're in full view mode. Default to false (main page mode).
if (!isset($isFullView)) {
    $isFullView = false;
}

// Fetch headline details
$query1 = "SELECT title, intro, logo_img FROM headline WHERE h_id = $headline_id";
$result1 = $conn->query($query1);

if ($result1 && $result1->num_rows > 0) {
    $row1 = $result1->fetch_assoc();
    $title   = htmlspecialchars($row1['title']);
    $intro   = htmlspecialchars($row1['intro']);
    $logo_img= htmlspecialchars($row1['logo_img']);
} else {
    $title = "No title found";
}

echo '
    <section id="first" data-aos="flip-left" data-aos-delay="200">
        <div class="container py-5">
          <div>
            <div id="card-header" data-aos="zoom-in">
              <img src="assets/images/icons/' . $logo_img . '" alt="Tree Icon" />
              ' . $title . '
            </div>
            <div id="card-body">
              <p>' . $intro . '</p>
';

// Set LIMIT clause based on view mode
$limitClause = ($isFullView) ? "" : "LIMIT 3";

// Query to fetch related content items
$query2 = "SELECT caption, detail FROM content WHERE h_id = $headline_id $limitClause";
$result2 = $conn->query($query2);

if ($result2 && $result2->num_rows > 0) {
    // Alternating highlight classes and animation directions
    $highlight_class = 'highlight_r';
    $animation = 'fade-right';
    
    while ($row = $result2->fetch_assoc()) {
        echo '<div class="' . $highlight_class . '" data-aos="' . $animation . '">
                <p>' . htmlspecialchars($row['caption']) . '<br/>
                   <sub>' . htmlspecialchars($row['detail']) . '</sub>
                </p>
              </div>';
        // Toggle classes for alternating styling
        $highlight_class = ($highlight_class === 'highlight_r') ? 'highlight_l' : 'highlight_r';
        $animation = ($animation === 'fade-right') ? 'fade-left' : 'fade-right';
    }
} else {
    echo '<p>No news content found for the specified headline.</p>';
}
// Display a "Read More" link only on the main page view
if (!$isFullView) {
  echo '<div class="intro-text"><a href="more.php?h_id=' . urlencode($headline_id) . '">قرائدة المزيد ...</a></div>';
}
echo '      </div>
          </div>
        </div>
        <hr id="style" />
      </section>
';

?>
