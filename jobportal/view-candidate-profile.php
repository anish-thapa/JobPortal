<?php
session_start();

// Redirect if the user is not logged in or is not a recruiter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruiter') {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Retrieve candidate ID from the URL
$candidate_id = isset($_GET['candidate_id']) ? intval($_GET['candidate_id']) : 0;

// Validate candidate ID
if ($candidate_id <= 0) {
    $_SESSION['error_message'] = "Invalid candidate ID.";
    header("Location: view-applicants.php");
    exit;
}

// Fetch candidate details from the database
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$result = $stmt->get_result();
$candidate = $result->fetch_assoc();

if (!$candidate) {
    $_SESSION['error_message'] = "Candidate not found.";
    header("Location: view-applicants.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Profile - Job Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'recruiter-header.php'; ?>

<!-- Include the error component -->
<?php include 'error_component.php'; ?>

<main>
    <div class="profile-container">
        <h1>Candidate Profile</h1>
        <div class="profile-section">
            <div class="profile-picture">
                <?php
                // Default profile picture
                $profile_picture_path = 'images/default-profile.jpg';
                ?>
                <img src="<?php echo $profile_picture_path; ?>" alt="Profile Picture" class="circular-image">
            </div>
            <div class="profile-details">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($candidate['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($candidate['email']); ?></p>
                <p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($candidate['bio'])); ?></p>
                <p><strong>Education:</strong> <?php echo nl2br(htmlspecialchars($candidate['education'])); ?></p>
                <p><strong>Past Jobs:</strong> <?php echo nl2br(htmlspecialchars($candidate['past_jobs'])); ?></p>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>