<?php
// temp1.php

// Use the provided $headline_id; if not set, fallback to a default value.
$headline_id = $headline_id ?? 1;

// Determine if we're in full view mode. Default to false (main page mode).
$isFullView = $isFullView ?? false;

// Fetch headline details
$query1 = "SELECT title, intro, logo_img FROM headline WHERE h_id = ?";
$stmt1 = $conn->prepare($query1);
$stmt1->bind_param('i', $headline_id);
$stmt1->execute();
$result1 = $stmt1->get_result();

if ($result1 && $result1->num_rows > 0) {
    $row1 = $result1->fetch_assoc();
    $title    = htmlspecialchars($row1['title']);
    $intro    = htmlspecialchars($row1['intro']);
    $logo_img = htmlspecialchars($row1['logo_img']);
} else {
    $title = "No title found";
}

echo '
    <section id="first">
        <div class="container py-5">
            <div>
                <div id="card-header" data-sal="fade" data-sal-duration="1200">
                    <img src="assets/images/icons/' . $logo_img . '" alt="Tree Icon" />
                    ' . $title . '
                </div>
                <div id="card-body">
                    <p data-sal="fade" data-sal-duration="1500">' . $intro . '</p>
';

$limitClause = $isFullView ? "" : "LIMIT 3";

// Query to fetch related content items
$query2 = "SELECT caption, detail FROM content WHERE h_id = ? $limitClause";
$stmt2 = $conn->prepare($query2);
$stmt2->bind_param('i', $headline_id);
$stmt2->execute();
$result2 = $stmt2->get_result();

// Check if related rows exist
if ($result2 && $result2->num_rows > 0) {
    // Alternating highlight classes and animations
    $highlight_class = 'highlight_r';
    $animation = 'slide-right';

    while ($row = $result2->fetch_assoc()) {
        echo '<div class="' . $highlight_class . '" data-sal="' . $animation . '" data-sal-duration="1500">
                <p>' . htmlspecialchars($row['caption']) . '<br/>
                   <sub>' . htmlspecialchars($row['detail']) . '</sub>
                </p>
              </div>';
        
        // Toggle highlight class and animation direction
        $highlight_class = ($highlight_class === 'highlight_r') ? 'highlight_l' : 'highlight_r';
        $animation = ($animation === 'slide-right') ? 'slide-left' : 'slide-right';
    }
} else {
    echo '<p>No news content found for the specified headline.</p>';
}

if (!$isFullView) {
    echo '<div class="intro-text">
            <a href="more.php?h_id=' . urlencode($headline_id) . '" data-sal="fade" data-sal-duration="1200">قراءة المزيد ...</a>
          </div>';
}

echo '      </div>
          </div>
        </div>
        <hr id="style" />
      </section>
';
?>
