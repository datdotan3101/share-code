<?php
session_start();
include '../includes/config.php';

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    die("Lỗi: Bạn phải đăng nhập để bình luận.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $post_id = $_POST['post_id'] ?? null;
    $comment = trim($_POST['comment'] ?? '');
    
    // Kiểm tra dữ liệu hợp lệ
    if (!$post_id || empty($comment)) {
        die("Lỗi: Dữ liệu không hợp lệ.");
    }

    // Thêm bình luận vào cơ sở dữ liệu
    $query = "INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    if ($stmt->execute([$post_id, $user_id, $comment])) {
        header("Location: ../index.php"); // Quay lại trang chính sau khi bình luận
        exit;
    } else {
        die("Lỗi: Không thể thêm bình luận.");
    }
} else {
    die("Lỗi: Phương thức không hợp lệ.");
}
