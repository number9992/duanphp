<?php
$passWord = "";
$DB_HOST = 'localhost';  // hoặc 127.0.0.1
$DB_USER = 'root';
$DB_NAME = 'student_management';

$conn = new mysqli($DB_HOST, $DB_USER, $passWord, $DB_NAME);
if ($conn->connect_error) {
    // die("❌ Kết nối DB thất bại: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

// ✅ Nếu đến đây thì kết nối thành công
// echo "✅ Kết nối DB thành công!";
?>