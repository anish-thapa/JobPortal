<?php
session_start();

// Redirect if not logged in or not a recruiter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruiter') {
    header("Location: login.php");
    exit;
}

include 'db.php';

$recruiter_id = $_SESSION['user_id'];
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validate job ID
if ($job_id <= 0) {
    $_SESSION['error_message'] = "Invalid job ID.";
    header("Location: recruiter-manage-jobs.php");
    exit;
}

// Fetch job details for editing
$query = "SELECT * FROM jobs WHERE id = ? AND recruiter_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $job_id, $recruiter_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();

if (!$job) {
    $_SESSION['error_message'] = "Job not found.";
    header("Location: recruiter-manage-jobs.php");
    exit;
}

// Handle Update Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $company_name = trim($_POST['company_name']);
    $location = trim($_POST['location']);
    $salary = floatval($_POST['salary']);
    $job_type = trim($_POST['job_type']);
    $position = trim($_POST['position']);
    $requirements = trim($_POST['requirements']);

    // Prepare the update query using prepared statements
    $query = "UPDATE jobs SET title = ?, company_name = ?, location = ?, salary = ?, job_type = ?, position = ?, requirements = ? WHERE id = ? AND recruiter_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssdssssi", $title, $company_name, $location, $salary, $job_type, $position, $requirements, $job_id, $recruiter_id);

    if ($stmt->execute()) {
        // Set success message in session
        $_SESSION['success_message'] = "Job updated successfully!";
        header("Location: recruiter-manage-jobs.php");
        exit;
    } else {
        // Set error message in session
        $_SESSION['error_message'] = "Error updating job: " . $stmt->error;
        header("Location: edit-job.php?id=" . $job_id);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job - Job Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'recruiter-header.php'; ?>

<!-- Include the error component -->
<?php include 'error_component.php'; ?>

<!-- Include the success component -->
<?php include 'success_component.php'; ?>

<main>
    <div class="form-container">
        <h1>Edit Job</h1>
        <form method="POST" class="styled-form">
            <div class="form-group">
                <label for="title">Job Title</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($job['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="company_name">Company Name</label>
                <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($job['company_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($job['location']); ?>" required>
            </div>
            <div class="form-group">
                <label for="salary">Salary</label>
                <input type="number" step="0.01" id="salary" name="salary" value="<?php echo htmlspecialchars($job['salary']); ?>" required>
            </div>
            <div class="form-group">
                <label for="job_type">Job Type</label>
                <select id="job_type" name="job_type" required>
                    <option value="full-time" <?php echo ($job['job_type'] === 'full-time') ? 'selected' : ''; ?>>Full-Time</option>
                    <option value="part-time" <?php echo ($job['job_type'] === 'part-time') ? 'selected' : ''; ?>>Part-Time</option>
                    <option value="contract" <?php echo ($job['job_type'] === 'contract') ? 'selected' : ''; ?>>Contract</option>
                    <option value="internship" <?php echo ($job['job_type'] === 'internship') ? 'selected' : ''; ?>>Internship</option>
                </select>
            </div>
            <div class="form-group">
                <label for="position">Position</label>
                <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($job['position']); ?>" required>
            </div>
            <div class="form-group">
                <label for="requirements">Requirements</label>
                <textarea id="requirements" name="requirements" rows="5" required><?php echo htmlspecialchars($job['requirements']); ?></textarea>
            </div>
            <button type="submit" class="btn-submit">Update Job</button>
        </form>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>