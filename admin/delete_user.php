<?php
session_start();
include '../includes/config.php'; // Kết nối database

// Kiểm tra nếu không phải admin thì chuyển hướng
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth/login.php");
    exit();
}

// Kiểm tra nếu có ID cần xóa
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id']; // Ép kiểu thành số nguyên
    $admin_id = $_SESSION['user_id']; // ID của admin hiện tại

    // Kiểm tra nếu admin đang xóa chính mình
    if ($id === $admin_id) {
        $_SESSION['error'] = "Bạn không thể tự xóa tài khoản của mình!";
        header("Location: users.php");
        exit();
    }

    // Lấy thông tin user cần xóa
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch();

    // Kiểm tra nếu user tồn tại
    if (!$user) {
        $_SESSION['error'] = "Người dùng không tồn tại!";
        header("Location: users.php");
        exit();
    }

    // Nếu user là admin, chỉ admin khác mới có thể xóa
    if ($user['role'] === 'admin') {
        // Thực hiện xóa admin
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $_SESSION['success'] = "Xóa admin thành công!";
        header("Location: users.php");
        exit();
    } else {
        // Nếu là user bình thường, xóa như cũ
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $_SESSION['success'] = "Xóa người dùng thành công!";
        header("Location: users.php");
        exit();
    }
} else {
    $_SESSION['error'] = "ID không hợp lệ!";
    header("Location: users.php");
    exit();
}
