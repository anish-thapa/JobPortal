<?php
// Database configuration
$host = 'localhost'; // Replace with your database host (e.g., localhost)
$username = 'root';  // Replace with your database username
$password = '';      // Replace with your database password (leave empty if no password)
$dbname = 'job-db';  // Your database name

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set charset to UTF-8 for better compatibility
$conn->set_charset("utf8");
?>