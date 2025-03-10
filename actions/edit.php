<?php
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Kiểm tra ID hợp lệ
    if (!is_numeric($id) || $id <= 0) {
        die("❌ Lỗi: ID bài viết không hợp lệ!");
    }

    // Lấy ảnh hiện tại từ database
    $query = "SELECT image FROM posts WHERE id = ?";
    $statement = $conn->prepare($query);
    $statement->execute([$id]);
    $post = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        die("❌ Lỗi: Không tìm thấy bài viết!");
    }

    $currentImage = $post['image'];
    $imagePath = $currentImage; // Giữ ảnh cũ nếu không chọn ảnh mới

    // Xử lý ảnh mới nếu có tải lên
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";
        $imageName = time() . "_" . basename($_FILES['image']['name']); // Đổi tên file để tránh trùng
        $targetFile = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Kiểm tra định dạng file
        if (in_array($imageFileType, $validExtensions)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = "uploads/" . $imageName;

                // Xóa ảnh cũ nếu có
                if (!empty($currentImage) && file_exists("../" . $currentImage)) {
                    unlink("../" . $currentImage);
                }
            }
        }
    }

    // Cập nhật bài đăng trong database
    $query = "UPDATE posts SET title = :title, content = :content, image = :image WHERE id = :id";
    $statement = $conn->prepare($query);
    $statement->execute([
        ':title' => $title,
        ':content' => $content,
        ':image' => $imagePath,
        ':id' => $id
    ]);

    header("Location: ../index.php");
    exit;
}
