<?php
session_start();
require_once('includes/config.php');

// Check if this is the user's first visit in the session
if (!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true; // Mark session as visited

    // Increment visit count in database
    $conn->query("UPDATE visits SET visit_count = visit_count + 1 WHERE id = 1");
}

// Retrieve the current visit count
$result = $conn->query("SELECT visit_count FROM visits WHERE id = 1");
$row = $result->fetch_assoc();
$visit_count = $row['visit_count'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <title>موقع آل حسان</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css"
        rel="stylesheet"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400..700&display=swap"
        rel="stylesheet"
    />
    
    <!-- Sal.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sal.js@0.8.0/dist/sal.css">

    <!-- Sal.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/sal.js@0.8.0/dist/sal.js"></script>


    <!-- Load Google reCAPTCHA API -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <!-- Loading Page -->
    <div class="loading-page">
        <img src="assets/images/theme/logo.png" alt="Loading...">
    </div>
