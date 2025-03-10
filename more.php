<?php
// more.php

require_once "./includes/header.php";
require_once "./includes/navbar.php";
require_once "./includes/config.php";

// Check if the headline id is provided in the URL
if (isset($_GET['h_id'])) {
    $headline_id = intval($_GET['h_id']);
} else {
    echo "<p>No headline id provided.</p>";
    require_once "./includes/footer.php";
    exit;
}

// Query the headline table to get the type (and optionally other details)
$query = "SELECT temp_id, title FROM headline WHERE h_id = $headline_id";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $temp = $row['temp_id'];
    $title = htmlspecialchars($row['title']);
} else {
    echo "<p>Headline not found.</p>";
    require_once "./includes/footer.php";
    exit;
}

// Set the flag for full view mode so that the template file removes LIMIT and "Read More" link
$isFullView = true;

// Include the appropriate template based on the headline's type
switch ($temp) {
    case 1:
        require "temp1.php";
        break;
    case 2:
        require "temp2.php";
        break;
    case 3:
        require "temp3.php";
        break;
    case 4:
        require "temp4.php";
        break;
    default:
        echo "<p>Unknown headline type.</p>";
        break;
}

require_once "./includes/footer.php";
?>
