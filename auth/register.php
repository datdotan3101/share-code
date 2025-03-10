    <?php
    include '../includes/config.php';

    $errors = []; // Error array

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        // 🛠 Validate username
        if (empty($username)) {
            $errors[] = "⚠ Please enter a username.";
        }

        // 🛠 Validate email format
        if (empty($email)) {
            $errors[] = "⚠ Please enter an email.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "⚠ Invalid email format. Please enter a valid email (e.g., example@email.com).";
        }

        // 🛠 Check if password has at least 6 characters
        if (strlen($password) < 6) {
            $errors[] = "⚠ Password must be at least 6 characters long.";
        }

        // 🛠 Validate password confirmation
        if ($password !== $confirmPassword) {
            $errors[] = "⚠ Password confirmation does not match.";
        }

        // 🛠 Check if email already exists
        if (empty($errors)) {
            $checkQuery = "SELECT id FROM users WHERE email = :email";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->execute([':email' => $email]);

            if ($checkStmt->fetch()) {
                $errors[] = "⚠ This email is already in use. Please choose another email!";
            }
        }

        // If no errors, proceed with registration
        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':username' => htmlspecialchars($username),
                ':email' => $email,
                ':password' => $hashedPassword
            ]);

            echo "<p style='color: green;'>✅ Registration successful! You can <a href='login.php'>log in</a>.</p>";
            exit();
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Sign Up</title>
        <link rel="stylesheet" href="./../assets/style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </head>

    <body class="d-flex justify-content-center align-items-center vh-100 bg-light">
        <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
            <h2 class="text-center">Sign Up</h2>

            <!-- Display error messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger text-center" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <p class="mb-0"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>

                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>

                <div class="mb-3 position-relative">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                    <span class="position-absolute top-50 end-0 translate-middle-y px-2" onclick="togglePassword('password')" style="cursor: pointer;">
                        👁️
                    </span>
                </div>

                <div class="mb-3 position-relative">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                    <span class="position-absolute top-50 end-0 translate-middle-y px-2" onclick="togglePassword('confirm_password')" style="cursor: pointer;">
                        👁️
                    </span>
                </div>

                <button type="submit" class="btn btn-primary w-100">Sign Up</button>
            </form>

            <p class="mt-3 text-center">Already have an account? <a href="login.php">Log in</a></p>
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