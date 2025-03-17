<?php
session_start();
if(!isset($_SESSION['user_id']) || 
   $_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR'] || 
   $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    header("Location: login.php");
    exit;
}

// Set session timeout to 30 minutes (1800 seconds)
$timeout = 1800;

// Check if the session is expired
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    // Last activity was more than 30 minutes ago
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header("Location: login.php?timeout=1"); // Redirect to login page
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

?>