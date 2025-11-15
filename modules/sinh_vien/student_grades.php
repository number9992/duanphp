<?php
// student_grades.php

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

if (!isset($_SESSION['student_info'])) {
    header("Location: dashboard_student.php");
    exit;
}

// Lấy thông tin từ Session
$student_id = $_SESSION['student_info']['student_id'];
$student_name = $_SESSION['student_info']['student_name'];

/* HÀM LẤY ĐIỂM CÁ NHÂN */
function loadStudentGrades($conn, $student_id) {
    $sql = "
        SELECT
            s.subject_code, s.subject_name, cs.semester,
            g.kt1, g.kt2, g.final_exam, g.grade AS Diem_Tong_Ket
        FROM grades g
        JOIN class_subjects cs ON g.class_subject_id = cs.id
        JOIN subjects s ON cs.subject_id = s.id
        WHERE g.student_id = ?
        ORDER BY cs.semester, s.subject_code
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/* HÀM RENDER ĐIỂM */
function render_grades($grades) {
    echo "<h3>Chi tiết điểm các môn học</h3>";
    
    if (empty($grades)) {
        echo "<p style='color:#888;'>Chưa có điểm nào được ghi nhận.</p>";
        return;
    }

    // ... (Code HTML để render bảng điểm) ...
    echo "<table class='grade-table'>";
    echo "<tr>
            <th>Học Kỳ</th>
            <th>Mã Môn</th>
            <th>Tên Môn Học</th>
            <th>KT 1</th>
            <th>KT 2</th>
            <th>Cuối Kỳ</th>
            <th>Điểm Tổng Kết</th>
        </tr>";

    foreach ($grades as $grade) {
        echo "<tr>";
        echo "<td>".htmlspecialchars($grade['semester'])."</td>";
        echo "<td>".htmlspecialchars($grade['subject_code'])."</td>";
        echo "<td>".htmlspecialchars($grade['subject_name'])."</td>";
        echo "<td>".htmlspecialchars($grade['kt1'] ?? 'N/A')."</td>";
        echo "<td>".htmlspecialchars($grade['kt2'] ?? 'N/A')."</td>";
        echo "<td>".htmlspecialchars($grade['final_exam'] ?? 'N/A')."</td>";
        echo "<td><strong>".htmlspecialchars($grade['Diem_Tong_Ket'] ?? 'N/A')."</strong></td>";
        echo "</tr>";
    }
    echo "</table>";
}

/* Thực thi logic */
$grades = loadStudentGrades($conn, $student_id);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Bảng Điểm Cá Nhân</title>
<style>
    /* CSS */
    body { font-family: Arial; background:#f7f9fc; margin:0; padding:30px; }
    h2 { color:#2c3e50; }
    table { border-collapse: collapse; width: 100%; background:white; border-radius:8px; overflow:hidden; box-shadow:0 3px 8px rgba(0,0,0,0.1); margin-top:10px;}
    th, td { border:1px solid #ddd; padding:10px; text-align:center; }
    th { background:#2ecc71; color:white; } /* Màu xanh lá cho bảng điểm */
</style>
</head>
<body>

<h2>💯 Bảng Điểm của Sinh viên: <?= htmlspecialchars($student_name) ?></h2>
<p><a href="dashboard_student.php"> Quay lại Dashboard</a></p>

<?php render_grades($grades); ?>

</body>
</html>

<?php $conn->close(); ?>