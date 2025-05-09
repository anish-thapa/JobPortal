<?php
session_start();

// Redirect if the user is not logged in as a recruiter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruiter') {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Check if the job ID is provided
if (isset($_GET['id'])) {
    $job_id = intval($_GET['id']); // Sanitize the job ID to prevent SQL injection

    // Prepare the delete query using a prepared statement
    $stmt = $conn->prepare("DELETE FROM jobs WHERE id = ?");
    $stmt->bind_param("i", $job_id);

    if ($stmt->execute()) {
        // Set a success message in the session
        $_SESSION['success_message'] = "Job deleted successfully!";
    } else {
        // Set an error message in the session
        $_SESSION['error_message'] = "Error: Unable to delete the job.";
    }

    $stmt->close();
} else {
    // If no job ID is provided, set an error message
    $_SESSION['error_message'] = "Error: Invalid job ID.";
}

// Redirect back to the jobs page
header("Location: jobs.php");
exit;
?>