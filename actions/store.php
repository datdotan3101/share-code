<?php
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content']; // ✅ Lấy nội dung từ form
    $subject_id = isset($_POST['subject_id']) && !empty($_POST['subject_id']) ? $_POST['subject_id'] : NULL;

    // Xử lý upload hình ảnh
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "../uploads/";
        $file_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;

        // Kiểm tra định dạng file
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = "uploads/" . $file_name;
            } else {
                $image_path = NULL;
            }
        } else {
            die("❌ Lỗi: Chỉ cho phép upload JPG, JPEG, PNG, GIF");
        }
    } else {
        $image_path = NULL;
    }

    // ✅ Cập nhật câu truy vấn để lưu cả `content`
    $query = "INSERT INTO posts (title, content, image, subject_id) VALUES (:title, :content, :image, :subject_id)";
    $statement = $conn->prepare($query);

    if (!$statement->execute([
        ':title' => $title,
        ':content' => $content, // ✅ Đã thêm `content`
        ':image' => $image_path,
        ':subject_id' => $subject_id
    ])) {
        print_r($statement->errorInfo()); // Hiển thị lỗi SQL nếu có
        exit();
    }

    // Chuyển hướng về index.php sau khi đăng bài thành công
    header("Location: ../index.php");
    exit();
}
