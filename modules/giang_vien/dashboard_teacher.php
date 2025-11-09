<?php
// Bắt đầu session
if (session_status() === PHP_SESSION_NONE) session_start();

// Kết nối DB và hàm tiện ích
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

// Chỉ giáo viên
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    echo "<div style='color:red; text-align:center; margin-top:50px;'>❌ Bạn không có quyền truy cập trang này!</div>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin giáo viên
$stmt = $conn->prepare("
    SELECT t.id, t.name, t.email
    FROM users u
    JOIN teachers t ON u.teacher_id = t.id
    WHERE u.id = ? LIMIT 1
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();

if (!$teacher) {
    echo "<div style='color:red; text-align:center; margin-top:50px;'>⚠️ User này không được liên kết với giáo viên nào!</div>";
    exit;
}

$teacher_id = $teacher['id'];

// Lấy danh sách phân công từ timetables
$sql = "
    SELECT DISTINCT 
        tt.class_id,
        c.class_name,
        tt.subject_id,
        s.subject_name,
        tt.semester,
        cs.id AS class_subject_id
    FROM timetables tt
    JOIN classes c ON tt.class_id = c.id
    JOIN subjects s ON tt.subject_id = s.id
    LEFT JOIN class_subjects cs 
        ON cs.class_id=tt.class_id AND cs.subject_id=tt.subject_id AND cs.semester=tt.semester
    WHERE tt.teacher_id = ?
    ORDER BY c.class_name, s.subject_name
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Bảng điều khiển Giáo viên</title>
<style>
body { font-family: Arial, sans-serif; background: #f7f9fc; margin:0; padding:30px; }
h2 { color:#2c3e50; margin-bottom:10px; }
h3 { color:#34495e; margin-top:30px; }
table { border-collapse: collapse; width: 100%; background: white; border-radius:10px; overflow:hidden; box-shadow:0 3px 8px rgba(0,0,0,0.1); margin-top:15px;}
th, td { border-bottom:1px solid #ddd; padding:12px 16px; text-align:left; }
th { background:#3498db; color:white; text-transform:uppercase; letter-spacing:0.5px; }
tr:hover { background:#f1f9ff; }
a.action { text-decoration:none; color:#2980b9; font-weight:bold; }
a.action:hover { color:#1f618d; }
.no-data { text-align:center; padding:20px; color:#888; }
</style>
</head>
<body>

<h2>👩‍🏫 Xin chào, <?= htmlspecialchars($teacher['name']) ?>!</h2>
<p>Chào mừng bạn đến với bảng điều khiển giảng viên.</p>

<h3>📚 Các lớp và môn học bạn phụ trách</h3>

<table>
<tr>
  <th>Lớp</th>
  <th>Môn học</th>
  <th>Học kỳ</th>
  <th>Thao tác</th>
</tr>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['class_name']) ?></td>
            <td><?= htmlspecialchars($row['subject_name']) ?></td>
            <td><?= htmlspecialchars($row['semester']) ?></td>
            <td>
                <a class="action"
                   href="index.php?url=giang_vien/teacher_grades&class_id=<?= $row['class_id'] ?>&subject_id=<?= $row['subject_id'] ?>&semester=<?= urlencode($row['semester']) ?>">
                    📝 Nhập điểm
                </a>
                |
                <a class="action"
                   href="index.php?url=giang_vien/teacher_attendance&class_subject_id=<?= $row['class_subject_id'] ?: 0 ?>">
                    📌 Điểm danh
                </a>
                |
                <a class="action"
                   href="index.php?url=giang_vien/teacher_timetable&teacher_id=<?= $teacher_id ?>">
                    📅 Xem TKB
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="4" class="no-data">Chưa có phân công trong thời khóa biểu.</td></tr>
<?php endif; ?>

</table>
</body>
</html>
