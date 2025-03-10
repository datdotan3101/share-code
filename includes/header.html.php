<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/config.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>

<body>
    <header>
        <?php if ($isLoggedIn): ?>
            <p>Welcome, <?= htmlspecialchars($_SESSION['username']); ?> | <a href="auth/logout.php">Logout</a></p>
        <?php else: ?>
            <p><a href="auth/login.php">Login</a> | <a href="auth/register.php">Register</a></p>
        <?php endif; ?>
    </header>

</body>

</html>