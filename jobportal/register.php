<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    // Check if the email already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $_SESSION['error_message'] = "Email already exists. Please use a different email.";
        $check_stmt->close();
        header("Location: register.php"); // Redirect to register page on error
        exit;
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Registration successful! Please login now";
            $stmt->close();
            header("Location: login.php"); // Redirect to login page on success
            exit;
        } else {
            $_SESSION['error_message'] = "Error: " . $stmt->error;
            $stmt->close();
            header("Location: register.php"); // Redirect to register page on error
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Job Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main>
  
    <?php include 'success_component.php'; ?>
    <?php include 'error_component.php'; ?>

    <div class="form-container">
        <h1>Register</h1>
        <form method="POST" class="styled-form">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role">
                    <option value="candidate">Candidate</option>
                    <option value="recruiter">Recruiter</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Register</button>
        </form>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
