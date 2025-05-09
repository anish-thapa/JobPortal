<?php
include 'db.php';


$limit = 5; // Number of jobs per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $query = "SELECT * FROM jobs WHERE title LIKE '%$search%' OR company_name LIKE '%$search%' OR location LIKE '%$search%' LIMIT $limit OFFSET $offset";
    $total_jobs_query = "SELECT COUNT(*) AS total FROM jobs WHERE title LIKE '%$search%' OR company_name LIKE '%$search%' OR location LIKE '%$search%'";
} else {
    $query = "SELECT * FROM jobs LIMIT $limit OFFSET $offset";
    $total_jobs_query = "SELECT COUNT(*) AS total FROM jobs";
}

$result = mysqli_query($conn, $query);
$total_jobs_result = mysqli_query($conn, $total_jobs_query);
$total_jobs = mysqli_fetch_assoc($total_jobs_result)['total'];
$total_pages = ceil($total_jobs / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse available jobs on our job portal.">
    <title>Available Jobs - Job Portal</title>
    <link rel="stylesheet" href="css/style.css">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<main role="main">
    <h1 class="page-title">Available Jobs</h1>

    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search jobs..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">
            <i class="fas fa-search"></i> 
        </button>
    </form>

    <div class="job-list" aria-label="List of available jobs">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="job-card">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><strong>Company:</strong> <?php echo htmlspecialchars($row['company_name']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                    <p><strong>Salary:</strong> RS. <?php echo number_format($row['salary'], 2); ?></p>
                    <p><strong>Type:</strong> <?php echo ucfirst($row['job_type']); ?></p>
                    <a href="detail_job.php?id=<?php echo $row['id']; ?>" class="btn-applyjob">View Details</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-jobs">No jobs available at the moment.</p>
        <?php endif; ?>
    </div>


    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" <?php echo ($i === $page) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
        <?php endif; ?>
    </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
