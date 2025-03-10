<?php
session_start();
include '../includes/config.php'; // Kết nối database

// Truy vấn tất cả bài viết
$sql = "SELECT posts.*, users.username AS user_name, users.email 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC";



$stmt = $conn->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bài viết</title>
</head>
<body>
    <h2>Quản lý bài viết</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tiêu đề</th>
                <th>Nội dung</th>
                <th>Người đăng</th>
                <th>Email</th>
                <th>Hình ảnh</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <tr>
                <td><?= htmlspecialchars($post['id']) ?></td>
                <td><?= htmlspecialchars($post['title']) ?></td>
                <td><?= nl2br(htmlspecialchars($post['content'])) ?></td>
                <td><?= htmlspecialchars($post['user_name']) ?></td>
                <td><?= htmlspecialchars($post['email']) ?></td>
                <td>
                    <?php if (!empty($post['image'])): ?>
                        <img src="../<?= htmlspecialchars($post['image']) ?>" width="100" alt="Hình ảnh">
                    <?php else: ?>
                        Không có ảnh
                    <?php endif; ?>
                </td>
                <td>
                    <?= $post['status'] ? '<span style="color: green;">Đã duyệt</span>' : '<span style="color: red;">Chờ duyệt</span>' ?>
                </td>
                <td>
                    <?php if ($post['status'] == 0): ?>
                        <form method="POST">
                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                            <button type="submit" name="approve">Duyệt</button>
                        </form>
                    <?php else: ?>
                        <button disabled>Đã duyệt</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="8">Không có bài viết nào cần duyệt.</td></tr>
    <?php endif; ?>
</tbody>

    </table>

    <?php
    // Xử lý duyệt bài viết
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["approve"])) {
        $post_id = intval($_POST["post_id"]);
        $sql = "UPDATE posts SET status = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([$post_id])) {
            echo "<script>alert('Bài viết đã được duyệt!'); window.location.href='posts.php';</script>";
        } else {
            echo "<script>alert('Lỗi khi duyệt bài!');</script>";
        }
    }
    ?>
</body>
</html>
