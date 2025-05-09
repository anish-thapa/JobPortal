<?php
// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect non-recruiters to the homepage
if ($_SESSION['role'] !== 'recruiter') {
    header("Location: index.php");
    exit;
}

include 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recruiter_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $company_name = trim($_POST['company_name']);
    $location = trim($_POST['location']);
    $salary = trim($_POST['salary']);
    $job_type = trim($_POST['job_type']);
    $position = trim($_POST['position']);
    $requirements = trim($_POST['requirements']);

    // Prevent SQL Injection by using prepared statements
    $stmt = $conn->prepare("INSERT INTO jobs (recruiter_id, title, company_name, location, salary, job_type, position, requirements) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdsss", $recruiter_id, $title, $company_name, $location, $salary, $job_type, $position, $requirements);

    if ($stmt->execute()) {
        // Set success message in session
        $_SESSION['success_message'] = "Job created successfully!";
    } else {
        // Set error message in session
        $_SESSION['error_message'] = "Error: " . $stmt->error;
    }

    $stmt->close();

    // Redirect back to the same page to display the message
    header("Location: create-job.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job - Job Portal</title>
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
        <h1>Create Job</h1>
        <form method="POST" class="styled-form">
            <div class="form-group">
                <label for="title">Job Title:</label>
                <input type="text" id="title" name="title" placeholder="Enter job title" required>
            </div>
            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" id="company_name" name="company_name" placeholder="Enter Company Name" required>
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" placeholder="Enter Location" required>
            </div>
            <div class="form-group">
                <label for="salary">Salary:</label>
                <input type="number" id="salary" name="salary" placeholder="Enter Salary in RS" step="1" required>
            </div>
            <div class="form-group">
                <label for="job_type">Job Type:</label>
                <select id="job_type" name="job_type">
                    <option value="full-time">Full-Time</option>
                    <option value="part-time">Part-Time</option>
                    <option value="contract">Contract</option>
                    <option value="internship">Internship</option>
                </select>
            </div>
            <div class="form-group">
                <label for="position">Position:</label>
                <input type="text" id="position" name="position" placeholder="Enter total no. of opened positions" required>
            </div>
            <div class="form-group">
                <label for="requirements">Requirements:</label>
                <textarea id="requirements" name="requirements" placeholder="Enter Requirements or Description" rows="5"></textarea>
            </div>
            <button type="submit" class="btn-submit">Create Job</button>
        </form>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>