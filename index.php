<?php
require_once "./includes/header.php";
?>
<header class="header" data-sal="slide-down" data-sal-duration="1200" data-sal-easing="ease-out">
<?php
require_once "./includes/navbar.php";
?>
</header>
<?php
// Include the configuration file
require_once "./includes/config.php";

// Query the headline table to fetch all needed rows
$query = "SELECT h_id, temp_id FROM headline ORDER BY sort_order ASC "; // Adjust the query as needed
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Set the headline id from the query result
        $headline_id = $row['h_id'];
        $temp = $row['temp_id'];

        // Use a switch statement to include the appropriate template
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
                // Optionally handle unknown temps
                echo "<p>Unknown temp for headline id {$headline_id}</p>";
                break;
        }
    }
} else {
    echo "<p>No headlines found.</p>";
}

require_once "./includes/footer.php";
?>
