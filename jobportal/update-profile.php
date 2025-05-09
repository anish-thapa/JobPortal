<?php
session_start();

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if the user has the 'candidate' role
if ($_SESSION['role'] !== 'candidate') {
    header("Location: login.php");
    exit;
}

include 'db.php';

$user_id = $_SESSION['user_id'];

// Get form data
$name = trim($_POST['name']);
$bio = trim($_POST['bio']);
$education = trim($_POST['education']);
$past_jobs = trim($_POST['past_jobs']);

// Update the user's profile in the database
$query = "UPDATE users SET name = ?, bio = ?, education = ?, past_jobs = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssi", $name, $bio, $education, $past_jobs, $user_id);

if ($stmt->execute()) {
    // Set success message
    $_SESSION['success_message'] = "Profile updated successfully!";
} else {
    // Set error message
    $_SESSION['error_message'] = "There was an error updating your profile.";
}

$stmt->close();
$conn->close();

// Redirect back to the profile page
header("Location: profile.php");
exit;
?>