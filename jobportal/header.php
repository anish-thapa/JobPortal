<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal</title>
    <!-- Font Awesome CSS from Cloudflare CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script>
        function toggleNav() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }
    </script>
</head>
<body>
<header>
    <div class="logo">
        <a href="index.php">Job Portal</a>
    </div>
    <!-- Hamburger Menu with Font Awesome Icon -->
    <div class="hamburger" onclick="toggleNav()">
        <i class="fa-solid fa-bars"></i>
    </div>
    <nav>
        <ul class="nav-links">
            <li><a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Home</a></li>
            <li><a href="jobs.php" class="<?= ($current_page == 'jobs.php') ? 'active' : '' ?>">Jobs</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="profile.php" class="<?= ($current_page == 'profile.php') ? 'active' : '' ?>">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" class="<?= ($current_page == 'login.php') ? 'active' : '' ?>">Login</a></li>
                <li><a href="register.php" class="<?= ($current_page == 'register.php') ? 'active' : '' ?>">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>