<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <style>
        /* Header */
        header {
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo a {
            font-family: Cinzel;
            font-size: 24px;
            font-weight: bold;
            color: #333;
            transition: color 0.3s ease;
        }

        .logo a:hover {
            color: #0056b3;
        }

        /* Navigation Links - Shared Styles */
        .nav-links {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .nav-links li a {
            position: relative;
            padding: 10px 15px;
            border-radius: 5px;
            transition: color 0.3s ease;
        }

        /* Underline Animation - Base Styles */
        .nav-links li a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #006ce8;
            transition: width 0.3s ease;
        }

        /* Regular Header Active/Hover States */
        .nav-links li a:hover,
        .nav-links li a.active {
            color: #006ce8;
        }

        .nav-links li a:hover::after,
        .nav-links li a.active::after {
            width: 100%;
        }

        /* Hamburger Menu */
        .hamburger {
            display: none;
            font-size: 24px;
            cursor: pointer;
            color: #333;
            transition: color 0.3s ease;
        }

        .hamburger:hover {
            color: #0056b3;
        }

        .hamburger span {
            display: block;
            width: 25px;
            height: 3px;
            background-color: #333;
            margin: 5px 0;
            border-radius: 5px;
        }

        /* Mobile Styles (<= 768px) */
        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }

            .nav-links {
                position: absolute;
                top: 0;
                left: -100%;
                flex-direction: column;
                background-color: #fff;
                width: 70%;
                height: 100vh;
                padding-top: 60px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: left 0.3s ease;
            }

            .nav-links.active {
                left: 0;
            }

            .nav-links li {
                margin: 15px 20px;
            }

            .nav-links li a {
                padding: 10px 20px;
                display: block;
            }

            header {
                padding-left: 10px;
                padding-right: 0;
            }

            .logo {
                margin-right: auto;
            }

            .hamburger {
                margin-left: auto;
            }
        }

        /* Recruiter Navbar */
        .recruiter-navbar {
            color: #000000;
            padding: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            width: 100%;
            margin: 0;
        }

        .recruiter-navbar .logo a {
            font-size: 24px;
            font-weight: bold;
            color: #000000;
            text-decoration: none;
            margin-left: 20px;
        }

        .recruiter-navbar .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
            margin-right: 20px;
        }

        /* Recruiter Link Colors */
        .recruiter-navbar .nav-links li a {
            color: #000000;
        }

        /* Recruiter Active/Hover States */
        .recruiter-navbar .nav-links li a:hover,
        .recruiter-navbar .nav-links li a.active {
            color: #006ce8;
        }

        .recruiter-navbar .nav-links li a:hover::after,
        .recruiter-navbar .nav-links li a.active::after {
            width: 100%;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                align-items: center;
            }

            .recruiter-navbar .nav-links {
                flex-direction: column;
                align-items: flex-start;
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="recruiter-navbar">
        <!-- Logo -->
        <div class="logo">
            <a href="create-job.php">Job Portal</a>
        </div>

        <!-- Navigation Links -->
        <nav>
            <ul class="nav-links">
                <!-- Create Job Link -->
                <li>
                    <a href="create-job.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'create-job.php' ? 'active' : ''; ?>">
                        Create Job
                    </a>
                </li>

                <!-- Manage Jobs Link -->
                <li>
                    <a href="recruiter-manage-jobs.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'recruiter-manage-jobs.php' ? 'active' : ''; ?>">
                        Manage Jobs
                    </a>
                </li>

                <!-- View Applications Link -->
                <li>
                    <a href="view-applicants.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'view-applicants.php' ? 'active' : ''; ?>">
                        View Applications
                    </a>
                </li>

                <!-- Logout Link -->
                <li>
                    <a href="logout.php">Logout</a>
                </li>
            </ul>
        </nav>

        <!-- Hamburger Menu -->
        <div class="hamburger" onclick="toggleNav()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</header>

<!-- Add this JavaScript below the header in your HTML -->
<script>
    // Function to toggle the navigation menu for mobile view
    function toggleNav() {
        var navLinks = document.querySelector('.nav-links');
        navLinks.classList.toggle('active');
    }
</script>
</body>
</html>
