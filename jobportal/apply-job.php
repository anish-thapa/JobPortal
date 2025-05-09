<?php
session_start();
include 'db.php';

// Initialize message variables
$error_message = '';
$success_message = '';

// Retrieve job ID from the URL
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validate job ID
if ($job_id <= 0) {
    header("Location: jobs.php");
    exit;
}

// Fetch job details from the database
$query = "SELECT * FROM jobs WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();

if (!$job) {
    $error_message = "Job not found.";
}

// If form is submitted to apply
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the user is logged in as a candidate
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'candidate') {
        $error_message = "You must be logged in as a candidate to apply for this job.";
    } else {
        $candidate_id = $_SESSION['user_id'];

        // Handle file upload for resume
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
            $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $max_size = 5 * 1024 * 1024; // 5MB

            $file_type = $_FILES['resume']['type'];
            $file_size = $_FILES['resume']['size'];
            $file_name = basename($_FILES['resume']['name']);
            $target_dir = "uploads/";
            $resume_path = $target_dir . uniqid() . '_' . $file_name;

            if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
                if (move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path)) {
                    // Insert application into the database using prepared statements
                    $query = "INSERT INTO applications (candidate_id, job_id, resume_path) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("iis", $candidate_id, $job_id, $resume_path);

                    if ($stmt->execute()) {
                        $success_message = "Application submitted successfully!";
                    } else {
                        $error_message = "Error: " . $stmt->error;
                    }

                    $stmt->close();
                } else {
                    $error_message = "Error uploading resume.";
                }
            } else {
                $error_message = "Invalid file type or size. Only PDF, DOC, and DOCX files up to 5MB are allowed.";
            }
        } else {
            $error_message = "Error uploading resume.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Details - Job Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header.php'; ?>
<main>
    <div class="job-details">
        <h1><?php echo htmlspecialchars($job['title']); ?></h1>
        <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
        <p><strong>Salary:</strong> RS. <?php echo number_format($job['salary'], 2); ?></p>
        <p><strong>Type:</strong> <?php echo ucfirst($job['job_type']); ?></p>
        <p><strong>Position:</strong> <?php echo htmlspecialchars($job['position']); ?></p>
        <p><strong>Description:</strong></p>
        <p><?php echo nl2br(htmlspecialchars($job['requirements'])); ?></p>

        <!-- Include the error and success message components -->
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <!-- Apply Job Form (only visible to logged-in candidates) -->
        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'candidate'): ?>
            <h2>Apply for this Job</h2>
            <form method="POST" enctype="multipart/form-data" class="apply-job-form">
                <div class="form-group">
                    <label for="resume">Upload Resume:</label>
                    <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                    <small>Allowed formats: PDF, DOC, DOCX (Max size: 5MB)</small>
                </div>
                <button type="submit" class="btn-submit">Submit Application</button>
            </form>
        <?php else: ?>
            <p style="color:red;" class="error-message">You must be logged in as a candidate to apply for this job.</p>
        <?php endif; ?>
    </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
