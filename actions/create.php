<?php
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imagePath = NULL; // Mặc định NULL nếu không có ảnh

    // Kiểm tra nếu có tải ảnh lên
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/"; // Thư mục lưu ảnh
        $imageName = time() . "_" . basename($_FILES['image']['name']); // Đổi tên file để tránh trùng lặp
        $targetFile = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $validExtensions)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = $targetFile; // Lưu đường dẫn ảnh vào database
            }
        }
    }

    // Lưu bài đăng vào database
    $query = "INSERT INTO posts (title, content, image) VALUES (:title, :content, :image)";
    $statement = $conn->prepare($query);
    $statement->execute([
        ':title' => $title,
        ':content' => $content,
        ':image' => $imagePath // Nếu không có ảnh, vẫn sẽ lưu giá trị NULL
    ]);

    header("Location: ../index.php");
    exit;
}
?>
