<?php
// dashboard_student.php

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

// 1. KIỂM TRA QUYỀN VÀ LẤY THÔNG TIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo "<div style='color:red; text-align:center; margin-top:50px;'>❌ Bạn không có quyền truy cập. (Chỉ dành cho Sinh viên)</div>";
    exit;
}

$user_id = $_SESSION['user_id'];

// SỬ DỤNG student_id ĐÃ THÊM VÀO BẢNG users
$stmt = $conn->prepare("
    SELECT st.id AS student_id, st.name AS student_name, c.class_name
    FROM users u
    JOIN students st ON u.student_id = st.id  
    JOIN classes c ON st.class_id = c.id
    WHERE u.id = ? LIMIT 1
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    echo "<div style='color:red; text-align:center; margin-top:50px;'>⚠️ User không liên kết với hồ sơ sinh viên nào!</div>";
    $conn->close();
    exit;
}

// Lưu thông tin cần thiết vào Session để các trang khác tái sử dụng
$_SESSION['student_info'] = [
    'student_id' => $student['student_id'],
    'class_name' => $student['class_name'],
    'student_name' => $student['student_name']
];
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Dashboard Sinh viên</title>
<style>
    /* CSS */
    body { font-family: Arial; background:#f7f9fc; margin:0; padding:30px; }
    .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    h2 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
    .menu a { display: inline-block; background: #3498db; color: white; padding: 12px 20px; margin: 10px 10px 10px 0; border-radius: 5px; text-decoration: none; transition: background 0.3s; }
    .menu a:hover { background: #2980b9; }
</style>
</head>
<body>

<div class="container">
    <h2>👋 Chào mừng Sinh viên: <?= htmlspecialchars($_SESSION['student_info']['student_name']) ?></h2>
    <h3>Bạn thuộc lớp: <?= htmlspecialchars($_SESSION['student_info']['class_name']) ?></h3>

    <div class="menu">
        <a href="?url=sinhvien/student_timetable">📅 Thời khóa biểu</a>
        <a href="?url=sinhvien/student_grades">💯 Bảng điểm</a>
    </div>

    <p style="margin-top: 30px;">Chọn chức năng bạn muốn xem.</p>
</div>

</body>
</html>