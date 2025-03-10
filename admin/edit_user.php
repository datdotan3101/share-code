<?php
session_start();
include '../includes/config.php';
include '../includes/admin_auth.php'; // Chỉ admin mới vào được

// Kiểm tra ID người dùng có tồn tại không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("❌ Không tìm thấy người dùng.");
}

$id = $_GET['id'];

// Truy vấn thông tin người dùng
$query = "SELECT id, username, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Nếu không tìm thấy user
if (!$user) {
    die("❌ Người dùng không tồn tại.");
}

// Xử lý cập nhật thông tin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Kiểm tra dữ liệu hợp lệ
    if (empty($username) || empty($email) || empty($role)) {
        echo "⚠ Vui lòng điền đầy đủ thông tin.";
    } else {
        // Cập nhật thông tin user
        $updateQuery = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        if ($updateStmt->execute([$username, $email, $role, $id])) {
            echo "✅ Cập nhật thành công!";
            header("Location: users.php"); // Quay lại danh sách người dùng
            exit();
        } else {
            echo "❌ Lỗi cập nhật!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa người dùng</title>
    <style>
        .container { max-width: 500px; margin: auto; text-align: center; }
        form { background: #f9f9f9; padding: 20px; border-radius: 8px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px; background: green; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2>✏ Chỉnh sửa thông tin</h2>
        <form action="" method="POST">
            <label>Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label>Quyền:</label>
            <select name="role">
                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>

            <button type="submit">💾 Lưu thay đổi</button>
        </form>
        <p><a href="users.php">⬅ Quay lại</a></p>
    </div>
</body>
</html>
