<?php
// student_timetable.php

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

// Đảm bảo thông tin sinh viên có sẵn
if (!isset($_SESSION['student_info'])) {
    // Nếu chưa có, cần chuyển hướng để lấy thông tin (thường xảy ra ở dashboard_student.php)
    header("Location: dashboard_student.php"); 
    exit;
}

// Lấy thông tin từ Session
$student_info = $_SESSION['student_info'];
$class_name = $student_info['class_name']; // Lấy tên lớp

// Lấy class_id từ DB dựa trên class_name (cần thiết nếu class_id chưa lưu trong session)
// Trong môi trường thực tế, nên lưu class_id vào session ngay từ đầu.
$stmt = $conn->prepare("SELECT id FROM classes WHERE class_name = ? LIMIT 1");
$stmt->bind_param('s', $class_name);
$stmt->execute();
$class_id_result = $stmt->get_result()->fetch_assoc();
$class_id = $class_id_result['id'] ?? null;
$stmt->close();


$weekdays = ['Mon'=>'Thứ 2','Tue'=>'Thứ 3','Wed'=>'Thứ 4','Thu'=>'Thứ 5','Fri'=>'Thứ 6','Sat'=>'Thứ 7'];

/* HÀM LẤY THỜI KHÓA BIỂU THEO CLASS_ID */
function loadTimetable($conn, $class_id, $semester) {
    if (!$class_id) return [];
    $sql = "
        SELECT 
            tt.day_of_week, tt.session, tt.period, tt.room,
            t.name AS teacher_name,
            s.subject_name
        FROM timetables tt
        JOIN teachers t ON tt.teacher_id = t.id
        JOIN subjects s ON tt.subject_id = s.id
        WHERE tt.class_id = ? AND tt.semester = ?
        ORDER BY
            FIELD(tt.day_of_week, 'Mon','Tue','Wed','Thu','Fri','Sat'),
            FIELD(tt.session, 'Sáng','Chiều'), tt.period
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('is', $class_id, $semester);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[$row['day_of_week']][$row['session']][$row['period']] = [
            'teacher_name' => $row['teacher_name'],
            'subject_name' => $row['subject_name'],
            'room'         => $row['room']
        ];
    }
    return $data;
}

/* HÀM RENDER TABLE */
function render_timetable($title, $timetable, $weekdays) {
    echo "<h3>$title</h3>";

    if (empty($timetable)) {
        echo "<p style='color:#888;'>Không có lịch cho học kỳ này.</p>";
        return;
    }
    
    echo "<table>";
    echo "<tr><th>Buổi / Tiết</th>";
    // Header cột thứ
    foreach ($weekdays as $day_name) echo "<th>$day_name</th>";
    echo "</tr>";

    // Lặp qua Buổi (Sáng/Chiều)
    foreach (['Sáng','Chiều'] as $session) {
        // Tìm tiết lớn nhất trong buổi để xác định số hàng (rowspan)
        $max_period = 0;
        foreach ($weekdays as $day_short => $name) {
            if (isset($timetable[$day_short][$session])) {
                // max(array_keys) tìm số tiết cao nhất trong buổi của ngày đó
                $max_period = max($max_period, max(array_keys($timetable[$day_short][$session])));
            }
        }
        // Bỏ qua nếu buổi đó không có tiết nào
        if ($max_period == 0) continue;

        // Lặp qua Tiết học (1 đến max_period)
        for ($p=1; $p <= $max_period; $p++) {
            echo "<tr>";
            
            // Chỉ hiển thị cột "Buổi" (Session) ở tiết đầu tiên và dùng rowspan
            if ($p == 1) {
                echo "<td class='session-header' rowspan='$max_period'>$session</td>";
            }
            
            // Lặp qua các ngày trong tuần
            foreach ($weekdays as $d => $n) {
                if (isset($timetable[$d][$session][$p])) {
                    $tt = $timetable[$d][$session][$p];
                    // Hiển thị thông tin: Môn học, Giáo viên, Phòng học
                    echo "<td>";
                    echo "<strong>" . esc($tt['subject_name']) . "</strong><br>";
                    echo "(" . esc($tt['teacher_name']) . ")<br>";
                    echo esc($tt['room']);
                    echo "</td>";
                } else {
                    // Ô trống nếu không có tiết học
                    echo "<td></td>";
                }
            }
            echo "</tr>";
        }
    }

    echo "</table>";
}

/* Thực thi logic */
// Lấy thời khóa biểu cho cả 2 học kỳ
$timetable_hk1 = loadTimetable($conn, $class_id, "1");
$timetable_hk2 = loadTimetable($conn, $class_id, "2");

// ... (Phần HTML, CSS và hiển thị) ...
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thời khóa biểu Lớp <?= htmlspecialchars($class_name) ?></title>
<style>
    /* CSS */
    body { font-family: Arial; background:#f7f9fc; margin:0; padding:30px; }
    h2 { color: #2980b9; }
    h3 { margin-top:30px; color:#34495e; }
    table { border-collapse: collapse; width: 100%; background:white; border-radius:8px; overflow:hidden; box-shadow:0 3px 8px rgba(0,0,0,0.1); margin-top:10px;}
    th, td { border:1px solid #ddd; padding:10px; text-align:center; vertical-align:top; line-height:1.4;}
    th { background:#3498db; color:white; font-weight:bold; }
    .session-header { background:#ecf0f1; font-weight:bold; width: 5%;}
</style>
</head>
<body>
    <h2>📅 Thời khóa biểu Lớp <?= htmlspecialchars($class_name) ?></h2>
    <p><a href="dashboard_student.php">⬅️ Quay lại Dashboard</a></p>
    
    <?php
    render_timetable("📘 Học kỳ 1", $timetable_hk1, $weekdays);
    render_timetable("📗 Học kỳ 2", $timetable_hk2, $weekdays);
    ?>
</body>
</html>

<?php $conn->close(); ?>