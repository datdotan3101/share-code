<?php
session_start();
include __DIR__ . '/../../includes/config.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Xóa bài viết
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->execute(['id' => $id]);

    $_SESSION['success'] = "Xóa bài viết thành công!";
    header("Location: posts.php");
    exit();
} else {
    $_SESSION['error'] = "Không tìm thấy bài viết!";
    header("Location: posts.php");
    exit();
}
?>
