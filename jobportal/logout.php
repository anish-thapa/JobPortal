<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy session and logout the user
session_destroy();

// Redirect to homepage
header("Location: index.php");
exit();
?>
