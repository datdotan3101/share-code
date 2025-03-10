<?php
session_start();

$error_message = $_SESSION['error'] ?? null;
unset($_SESSION['error']);

$role = isset($_GET['role']) ? $_GET['role'] : 'user';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign In</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
        <h2 class="text-center mb-3">Sign In</h2>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="process_register.php">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter username" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
            </div>

            <!-- Dropdown choose role-->
            <div class="mb-3">
                <label class="form-label">User Roles</label>
                <select name="role" class="form-select">
                    <option value="user" <?= $role === 'user' ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?= $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Sign In</button>
        </form>

        <p class="text-center mt-3">
            Don't have an account? <a href="login.php" class="text-decoration-none">Log In</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>