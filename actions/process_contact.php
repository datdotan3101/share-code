<?php
session_start();
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Kiểm tra dữ liệu nhập vào
    if (empty($name) || empty($email) || empty($message)) {
        $_SESSION['contact_message'] = "❌ Vui lòng nhập đầy đủ thông tin!";
        $_SESSION['contact_status'] = "error";
        header("Location: ../contact/contact.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['contact_message'] = "❌ Email không hợp lệ!";
        $_SESSION['contact_status'] = "error";
        header("Location: ../contact/contact.php");
        exit();
    }

    // Lưu vào database
    $query = "INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':name' => htmlspecialchars($name),
        ':email' => htmlspecialchars($email),
        ':message' => htmlspecialchars($message)
    ]);

    $_SESSION['contact_message'] = "✅ Gửi liên hệ thành công! Admin sẽ phản hồi sớm.";
    $_SESSION['contact_status'] = "success";
    header("Location: ../contact/contact.php");
    exit();
}
?>
