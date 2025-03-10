<?php
include '../includes/config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Xóa tất cả comments liên quan đến bài viết
    $query = "DELETE FROM comments WHERE post_id = :id";
    $statement = $conn->prepare($query);
    $statement->execute([':id' => $id]);

    // Sau đó xóa bài viết
    $query = "DELETE FROM posts WHERE id = :id";
    $statement = $conn->prepare($query);
    $statement->execute([':id' => $id]);
}

header("Location: ../index.php");
exit;
