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

// Fetch user details
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle application deletion via GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_application_id'])) {
    $application_id = intval($_GET['delete_application_id']);

    // Fetch resume path before deleting the application
    $stmt = $conn->prepare("SELECT resume_path FROM applications WHERE id = ? AND candidate_id = ?");
    $stmt->bind_param("ii", $application_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $application = $result->fetch_assoc();

    if ($application) {
        // Delete the resume file if it exists
        if (!empty($application['resume_path']) && file_exists($application['resume_path'])) {
            unlink($application['resume_path']);
        }

        // Delete the application from the database
        $stmt = $conn->prepare("DELETE FROM applications WHERE id = ? AND candidate_id = ?");
        $stmt->bind_param("ii", $application_id, $user_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Application deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error deleting application.";
        }
    } else {
        $_SESSION['error_message'] = "Application not found.";
    }

    header("Location: profile.php");
    exit;
}

// Fetch applied jobs for the logged-in user
$query_applied_jobs = "SELECT applications.id AS application_id, jobs.title, applications.status, applications.resume_path 
                       FROM applications 
                       JOIN jobs ON applications.job_id = jobs.id 
                       WHERE applications.candidate_id = ?";
$stmt_applied_jobs = $conn->prepare($query_applied_jobs);
$stmt_applied_jobs->bind_param("i", $user_id);
$stmt_applied_jobs->execute();
$applied_jobs_result = $stmt_applied_jobs->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Job Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header.php'; ?>

<!-- Include the error component -->
<?php include 'error_component.php'; ?>

<!-- Include the success component -->
<?php include 'success_component.php'; ?>

<main>
    <div class="profile-container">
        <h1>My Profile</h1>
        <!-- Profile Section -->
        <div class="profile-section">
            <div class="profile-picture">
                <?php
                $profile_picture_path = 'images/default-profile.jpg'; // Default profile picture
                ?>
                <img src="<?php echo htmlspecialchars($profile_picture_path); ?>" alt="Profile Picture" class="circular-image">
            </div>
            <div class="profile-details">
                <form method="POST" action="update-profile.php" class="styled-form">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="bio">Bio:</label>
                        <textarea id="bio" name="bio"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="education">Education:</label>
                        <textarea id="education" name="education"><?php echo htmlspecialchars($user['education']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="past_jobs">Past Jobs:</label>
                        <textarea id="past_jobs" name="past_jobs"><?php echo htmlspecialchars($user['past_jobs']); ?></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Update Profile</button>
                </form>
            </div>
        </div>
        <!-- Applied Jobs Section -->
        <div class="applied-jobs-section">
            <h2>Applied Jobs</h2>
            <?php if ($applied_jobs_result->num_rows > 0) { ?>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Status</th>
                            <th>Resume</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($job = $applied_jobs_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($job['title']); ?></td>
                                <td><?php echo ucfirst($job['status']); ?></td>
                                <td>
                                    <?php if ($job['resume_path']) { ?>
                                        <a href="<?php echo htmlspecialchars($job['resume_path']); ?>" target="_blank" class="btn-resume">View Resume</a>
                                    <?php } else { ?>
                                        <span>No Resume</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a href="profile.php?delete_application_id=<?php echo $job['application_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this application?');">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p class="no-data">You have not applied for any jobs yet.</p>
            <?php } ?>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
