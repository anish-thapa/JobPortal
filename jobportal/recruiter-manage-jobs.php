<?php
session_start();

// Redirect if not logged in or not a recruiter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruiter') {
    header("Location: login.php");
    exit;
}

include 'db.php';
$recruiter_id = $_SESSION['user_id'];

// Handle Delete Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_job'])) {
    $job_id = intval($_POST['job_id']);
    $query = "DELETE FROM jobs WHERE id = ? AND recruiter_id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        // Set an error message in the session
        $_SESSION['error_message'] = "Error preparing statement: " . $conn->error;
        header("Location: recruiter-manage-jobs.php");
        exit;
    }

    $stmt->bind_param("ii", $job_id, $recruiter_id);

    if ($stmt->execute()) {
        // Set a success message in the session
        $_SESSION['success_message'] = "Job deleted successfully!";
    } else {
        // Set an error message in the session
        $_SESSION['error_message'] = "Error deleting job: " . $stmt->error;
    }

    $stmt->close();
    header("Location: recruiter-manage-jobs.php");
    exit;
}

// Fetch jobs posted by the recruiter
$query = "SELECT * FROM jobs WHERE recruiter_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $recruiter_id);
$stmt->execute();
$result = $stmt->get_result();
$jobs = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs - Job Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'recruiter-header.php'; ?>

<!-- Include the error component -->
<?php include 'error_component.php'; ?>

<!-- Include the success component -->
<?php include 'success_component.php'; ?>

<main>
    <div class="table-container">
        <h1>Manage Jobs</h1>
        <?php if (!empty($jobs)): ?>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Company</th>
                        <th>Location</th>
                        <th>Salary</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($job['title']); ?></td>
                            <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($job['location']); ?></td>
                            <td>RS. <?php echo number_format($job['salary'], 2); ?></td>
                            <td><?php echo ucfirst($job['job_type']); ?></td>
                            <td>
                                <a href="edit-job.php?id=<?php echo $job['id']; ?>" class="btn-action btn-update">Edit</a>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                    <button type="submit" name="delete_job" class="btn-delete" onclick="return confirm('Are you sure you want to delete this job?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No jobs found.</p>
        <?php endif; ?>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>