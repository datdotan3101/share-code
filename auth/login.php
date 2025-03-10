<?php
session_start();
include '../includes/config.php';

// Limit the number of failed login attempts
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if ($_SESSION['login_attempts'] >= 5) {
    die("<p style='color: red;'>You have entered incorrect details too many times. Please try again later!</p>");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username)) {
        $error = "❌ Please enter your username!";
    }

    // Check account
    if (!$error) {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true); // Prevent Session Fixation
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = htmlspecialchars($user['username']);

            $_SESSION['login_attempts'] = 0; // Reset failed attempts

            header("Location: ../index.php");
            exit();
        } else {
            $_SESSION['login_attempts']++; // Increase failed attempts
            $error = "❌ Incorrect username or password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow p-4" style="max-width: 350px; width: 100%;">
        <h2 class="text-center">Login</h2>

        <!-- Display error message -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>

            <div class="mb-3 position-relative">
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                <span class="position-absolute top-50 end-0 translate-middle-y px-2" onclick="togglePassword('password')" style="cursor: pointer;">
                    👁️
                </span>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <p class="mt-3 text-center">Don't have an account? <a href="register.php">Sign up</a></p>
    </div>

    <script>
        function togglePassword(fieldId) {
            var passwordField = document.getElementById(fieldId);
            var toggleIcon = passwordField.nextElementSibling;

            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.textContent = "👁️‍🗨️"; // Closed eye
            } else {
                passwordField.type = "password";
                toggleIcon.textContent = "👁️"; // Open eye
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>