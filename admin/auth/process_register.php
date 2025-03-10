<?php
session_start();
include __DIR__ . '/../../includes/config.php'; // Đường dẫn đúng tới config.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = $_POST['role'] ?? 'user'; // Mặc định là 'user' nếu không có role

    // Kiểm tra mật khẩu có khớp không
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Mật khẩu không khớp!";
        header("Location: register.php");
        exit();
    }

    // Kiểm tra xem username đã tồn tại chưa
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Tên người dùng đã tồn tại!";
        header("Location: register.php");
        exit();
    }

    // Kiểm tra email đã tồn tại chưa
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Email đã được sử dụng!";
        header("Location: register.php");
        exit();
    }

    // Mã hóa mật khẩu
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Thêm người dùng vào database
    try {
        $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password,
            'role' => $role
        ]);

        $_SESSION['success'] = "Đăng ký thành công! Hãy đăng nhập.";
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi đăng ký: " . $e->getMessage();
        header("Location: register.php");
        exit();
    }
}
