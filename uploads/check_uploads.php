<?php
$dir = "uploads/";

if (!is_dir($dir)) {
    echo "⚠ Thư mục $dir không tồn tại!";
} else {
    echo "📂 Danh sách file trong $dir:<br>";
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file !== "." && $file !== "..") {
            echo "<a href='$dir/$file' target='_blank'>$file</a><br>";
        }
    }
}
?>
