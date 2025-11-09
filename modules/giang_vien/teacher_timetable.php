<?php
// Bắt đầu session
if (session_status() === PHP_SESSION_NONE) session_start();

// Kết nối DB và hàm tiện ích
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

// Yêu cầu đăng nhập
requireLogin();

// Chỉ giáo viên được truy cập
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    echo "<div style='color:red; text-align:center; margin-top:50px;'>❌ Bạn không có quyền truy cập trang này!</div>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy teacher_id
$stmt = $conn->prepare("
    SELECT t.id, t.name
    FROM users u
    JOIN teachers t ON u.teacher_id = t.id
    WHERE u.id = ? LIMIT 1
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();

if (!$teacher) {
    echo "<div style='color:red; text-align:center; margin-top:50px;'>⚠️ User không liên kết với giáo viên nào!</div>";
    exit;
}

$teacher_id = $teacher['id'];

$weekdays = ['Mon'=>'Thứ 2','Tue'=>'Thứ 3','Wed'=>'Thứ 4','Thu'=>'Thứ 5','Fri'=>'Thứ 6','Sat'=>'Thứ 7'];

/* ✅ HÀM LẤY THỜI KHÓA BIỂU THEO HỌC KỲ */
function loadTimetable($conn, $teacher_id, $semester) {
    $sql = "
        SELECT 
            tt.day_of_week,
            tt.session,
            tt.period,
            tt.room,
            c.class_name,
            s.subject_name
        FROM timetables tt
        JOIN classes c ON tt.class_id = c.id
        JOIN subjects s ON tt.subject_id = s.id
        WHERE tt.teacher_id = ? AND tt.semester = ?
        ORDER BY
            FIELD(tt.day_of_week, 'Mon','Tue','Wed','Thu','Fri','Sat'),
            FIELD(tt.session, 'Sáng','Chiều'),
            tt.period
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('is', $teacher_id, $semester);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $day = $row['day_of_week'];
        $session = $row['session'];
        $period = $row['period'];

        $data[$day][$session][$period] = [
            'class_name'   => $row['class_name'],
            'subject_name' => $row['subject_name'],
            'room'         => $row['room']
        ];
    }
    return $data;
}

/* ✅ Lấy 2 học kỳ */
$timetable_hk1 = loadTimetable($conn, $teacher_id, "1");
$timetable_hk2 = loadTimetable($conn, $teacher_id, "2");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thời khóa biểu giáo viên</title>
<style>
body { font-family: Arial; background:#f7f9fc; margin:0; padding:30px; }
h2 { color:#2c3e50; }
h3 { margin-top:30px; color:#34495e; }
table { border-collapse: collapse; width: 100%; background:white; border-radius:8px; overflow:hidden; box-shadow:0 3px 8px rgba(0,0,0,0.1); margin-top:10px;}
th, td { border:1px solid #ddd; padding:10px; text-align:center; }
th { background:#3498db; color:white; }
.session-header { background:#ecf0f1; font-weight:bold; }
</style>
</head>
<body>

<h2>📅 Thời khóa biểu của <?= htmlspecialchars($teacher['name']) ?></h2>

<?php
// ✅ Hàm render table
function render_timetable($title, $timetable, $weekdays) {
    echo "<h3>$title</h3>";

    if (empty($timetable)) {
        echo "<p style='color:#888;'>Không có lịch cho học kỳ này.</p>";
        return;
    }

    echo "<table>";
    echo "<tr><th>Buổi / Tiết</th>";
    foreach ($weekdays as $day_name) echo "<th>$day_name</th>";
    echo "</tr>";

    foreach (['Sáng','Chiều'] as $session) {
        $max_period = 0;

        foreach ($weekdays as $day_short => $name) {
            if (isset($timetable[$day_short][$session])) {
                $max_period = max($max_period, max(array_keys($timetable[$day_short][$session])));
            }
        }

        for ($p=1; $p <= $max_period; $p++) {
            echo "<tr>";
            if ($p == 1) {
                echo "<td class='session-header' rowspan='$max_period'>$session</td>";
            }

            foreach ($weekdays as $d => $n) {
                if (isset($timetable[$d][$session][$p])) {
                    $tt = $timetable[$d][$session][$p];
                    echo "<td>{$tt['subject_name']}<br>({$tt['class_name']})<br>{$tt['room']}</td>";
                } else {
                    echo "<td></td>";
                }
            }

            echo "</tr>";
        }
    }

    echo "</table>";
}

// ✅ Render HK1 + HK2
render_timetable("📘 Thời khóa biểu Học kỳ 1", $timetable_hk1, $weekdays);
render_timetable("📗 Thời khóa biểu Học kỳ 2", $timetable_hk2, $weekdays);
?>

</body>
</html>
