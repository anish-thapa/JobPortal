<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

   
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
     
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        
        if ($user['role'] === 'recruiter') {
            header("Location: create-job.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        
        $_SESSION['error_message'] = "Invalid email or password!";
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Job Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header.php'; ?> <!-- Include header -->

<!-- Include the error component -->
<?php include 'error_component.php'; ?>

<!-- Include the success component -->
<?php include 'success_component.php'; ?>

<main>
    <div class="form-container">
        <h1>Login</h1>
        <form method="POST" class="styled-form">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-submit">Login</button>
        </form>
        
        <p>Don't have an account? <a class="register-link" href="register.php">Register here</a>.</p>
    </div>
</main>

<?php include 'footer.php'; ?> <!-- Include footer -->
</body>
</html>