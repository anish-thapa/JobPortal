<?php
session_start();

// Redirect if the user is not logged in or is not a recruiter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruiter') {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Handle status update via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id']) && is_array($_POST['application_id'])) {
    $success = true; // Flag to track if all updates were successful

    foreach ($_POST['application_id'] as $id) {
        if (isset($_POST['status'][$id])) {
            $application_id = intval($id);
            $status = trim($_POST['status'][$id]);

            // Update the application status in the database
            $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("si", $status, $application_id);

                if (!$stmt->execute()) {
                    $success = false; // Mark failure if any update fails
                }

                $stmt->close();
            } else {
                $success = false; // Mark failure if prepare fails
            }
        }
    }

    // Set success or error message based on the outcome
    if ($success) {
        $_SESSION['success_message'] = "Application status updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating application status.";
    }

    // Redirect back to the same page to reflect changes
    header("Location: view-applicants.php");
    exit;
}

// Handle application deletion via GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_application_id'])) {
    $application_id = intval($_GET['delete_application_id']);

    // Fetch resume path before deleting the application
    $stmt = $conn->prepare("SELECT resume_path FROM applications WHERE id = ?");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $application = $result->fetch_assoc();

    if ($application) {
        // Delete the resume file if it exists
        if (!empty($application['resume_path']) && file_exists($application['resume_path'])) {
            unlink($application['resume_path']); // Delete the resume file
        }

        // Delete the application from the database
        $stmt = $conn->prepare("DELETE FROM applications WHERE id = ?");
        $stmt->bind_param("i", $application_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Applicant's application deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error deleting application.";
        }
    } else {
        $_SESSION['error_message'] = "Application not found.";
    }

    // Redirect back to the same page to reflect changes
    header("Location: view-applicants.php");
    exit;
}

// Fetch applications for the logged-in recruiter
$recruiter_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT applications.id, users.name, users.email, jobs.title, applications.status, applications.resume_path, users.id AS candidate_id 
                         FROM applications 
                         JOIN users ON applications.candidate_id = users.id 
                         JOIN jobs ON applications.job_id = jobs.id 
                         WHERE jobs.recruiter_id = ?");
$stmt->bind_param("i", $recruiter_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applicants - Job Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        <style>
    /* General table styling */
    .styled-table {
        width: 100%;
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 0.9em;
        font-family: sans-serif;
        min-width: 400px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }

    /* Button styling */
    .btn-resume, .btn-view-profile, .btn-update, .btn-delete {
        padding: 5px 10px;
        text-decoration: none;
        border-radius: 4px;
        font-size: 0.8em;
        margin: 2px;
        display: inline-block;
        text-align: center;
    }


    /* Align buttons in the same line */
    .btn-container {
        display: flex;
        gap: 5px;
        align-items: center;
    }

    /* Ensure select dropdowns are styled */
    select {
        padding: 5px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
</style>
    </style>
</head>
<body>
<?php include 'recruiter-header.php'; ?>

<!-- Include the error component -->
<?php include 'error_component.php'; ?>

<!-- Include the success component -->
<?php include 'success_component.php'; ?>

<main>
    <div class="table-container">
        <h1>View Applicants</h1>
        <?php if ($result->num_rows > 0) { ?>
            <form method="POST">
                <table class="styled-table">
                    <thead>
                        <tr>
                        <th>Candidate Name</th>
                            <th>Email</th>
                            <th>Job Title</th>
                            <th>Resume</th>
                            <th>Profile</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td>
                                    <?php if ($row['resume_path']) { ?>
                                        <a href="<?php echo htmlspecialchars($row['resume_path']); ?>" target="_blank" class="btn-resume">View Resume</a>
                                    <?php } else { ?>
                                        <span>No Resume</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <!-- Link to view candidate profile -->
                                    <a href="view-candidate-profile.php?candidate_id=<?php echo $row['candidate_id']; ?>" class="btn-view-profile">View Profile</a>
                                </td>
                                <td>
                                    <select name="status[<?php echo $row['id']; ?>]">
                                        <option value="pending" <?php echo (strtolower($row['status']) === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="interviewing" <?php echo (strtolower($row['status']) === 'interviewing') ? 'selected' : ''; ?>>Interviewing</option>
                                        <option value="hired" <?php echo (strtolower($row['status']) === 'hired') ? 'selected' : ''; ?>>Hired</option>
                                        <option value="rejected" <?php echo (strtolower($row['status']) === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="hidden" name="application_id[<?php echo $row['id']; ?>]" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn-update">Update Status</button>
                                    <!-- Delete Application Button -->
                                    <a href="view-applicants.php?delete_application_id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this application?');">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </form>
        <?php } else { ?>
            <p>No applicants found.</p>
        <?php } ?>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>