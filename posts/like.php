<?php
session_start();
include '../includes/config.php';

// Hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kiểm tra session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập!']);
    exit;
}

// Kiểm tra dữ liệu đầu vào từ AJAX
if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID bài viết không hợp lệ!']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);

// Kiểm tra xem bài viết có tồn tại không
$checkPostQuery = "SELECT id FROM posts WHERE id = ?";
$checkPostStmt = $conn->prepare($checkPostQuery);
$checkPostStmt->execute([$post_id]);

if ($checkPostStmt->rowCount() === 0) {
    echo json_encode(['success' => false, 'message' => 'Bài viết không tồn tại!']);
    exit;
}

// Kiểm tra xem user đã like bài viết chưa
$checkLikeQuery = "SELECT id FROM likes WHERE post_id = ? AND user_id = ?";
$checkLikeStmt = $conn->prepare($checkLikeQuery);
$checkLikeStmt->execute([$post_id, $user_id]);

if ($checkLikeStmt->rowCount() > 0) {
    // Nếu đã like, thì unlike (xóa like)
    $deleteLikeQuery = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
    $deleteLikeStmt = $conn->prepare($deleteLikeQuery);
    $deleteLikeStmt->execute([$post_id, $user_id]);
    $action = 'unliked';
} else {
    // Nếu chưa like, thì thêm like
    $insertLikeQuery = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
    $insertLikeStmt = $conn->prepare($insertLikeQuery);
    $insertLikeStmt->execute([$post_id, $user_id]);
    $action = 'liked';
}

// Lấy lại số lượt like mới nhất
$likeCountQuery = "SELECT COUNT(*) as like_count FROM likes WHERE post_id = ?";
$likeCountStmt = $conn->prepare($likeCountQuery);
$likeCountStmt->execute([$post_id]);
$like_count = $likeCountStmt->fetch(PDO::FETCH_ASSOC)['like_count'];

// Trả về JSON để cập nhật giao diện
echo json_encode([
    'success' => true,
    'action' => $action,
    'like_count' => $like_count
]);
exit();
