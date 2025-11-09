<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

// Chỉ giáo viên
if ($_SESSION['role'] !== 'teacher') {
    die("<div style='color:red; text-align:center;'>❌ Bạn không có quyền truy cập!</div>");
}

$user_id = $_SESSION['user_id'];

// Lấy teacher_id từ users
$stmt = $conn->prepare("SELECT teacher_id FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();

if (!$teacher || !$teacher['teacher_id']) {
    die("<div style='color:red; text-align:center;'>⚠️ User không được liên kết với giáo viên nào.</div>");
}

$teacher_id = $teacher['teacher_id'];

// Lấy dữ liệu từ URL
$class_id   = intval($_GET['class_id'] ?? 0);
$subject_id = intval($_GET['subject_id'] ?? 0);
$semester   = $_GET['semester'] ?? '';

if (!$class_id || !$subject_id || !$semester) {
    die("<div style='color:red; text-align:center;'>⚠️ Không có lớp/môn/học kỳ được chọn.</div>");
}

// ✅ Kiểm tra giáo viên có thực sự dạy môn này theo TKB
$stmt = $conn->prepare("
    SELECT c.class_name, s.subject_name
    FROM timetables tt
    JOIN classes c ON tt.class_id = c.id
    JOIN subjects s ON tt.subject_id = s.id
    WHERE tt.class_id=? 
      AND tt.subject_id=? 
      AND tt.semester=? 
      AND tt.teacher_id=?
    LIMIT 1
");
$stmt->bind_param("iisi", $class_id, $subject_id, $semester, $teacher_id);
$stmt->execute();
$timetable = $stmt->get_result()->fetch_assoc();

if (!$timetable) {
    die("<div style='color:red; text-align:center;'>❌ Bạn không được phân công lớp/môn này!</div>");
}

// ✅ Lấy hoặc tạo class_subject_id
$stmt = $conn->prepare("
    SELECT id 
    FROM class_subjects
    WHERE class_id=? AND subject_id=? AND semester=?
    LIMIT 1
");
$stmt->bind_param("iis", $class_id, $subject_id, $semester);
$stmt->execute();
$cs = $stmt->get_result()->fetch_assoc();

if (!$cs) {
    // Tạo mới
    $stmt = $conn->prepare("
        INSERT INTO class_subjects (class_id, subject_id, semester, teacher_id)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iisi", $class_id, $subject_id, $semester, $teacher_id);
    $stmt->execute();
    $class_subject_id = $conn->insert_id;
} else {
    $class_subject_id = $cs['id'];
}

// ✅ Lưu điểm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grades'])) {

    $stmt = $conn->prepare("
        INSERT INTO grades (student_id, class_subject_id, kt1, kt2, final_exam)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            kt1=VALUES(kt1), 
            kt2=VALUES(kt2),
            final_exam=VALUES(final_exam)
    ");

    foreach ($_POST['grades'] as $student_id => $g) {
        $kt1 = $g['kt1'] !== '' ? floatval($g['kt1']) : null;
        $kt2 = $g['kt2'] !== '' ? floatval($g['kt2']) : null;
        $final_exam = $g['final_exam'] !== '' ? floatval($g['final_exam']) : null;

        $stmt->bind_param(
            "idddd",
            $student_id,
            $class_subject_id,
            $kt1,
            $kt2,
            $final_exam
        );
        $stmt->execute();
    }

    $message = "✅ Cập nhật điểm thành công!";
}

// ✅ Lấy danh sách học sinh
$stmt = $conn->prepare("
    SELECT st.id, st.name, g.kt1, g.kt2, g.final_exam,
           ROUND((COALESCE(g.kt1,0) + COALESCE(g.kt2,0) + COALESCE(g.final_exam,0)*2)/4, 2) AS grade
    FROM students st
    LEFT JOIN grades g ON g.student_id = st.id AND g.class_subject_id = ?
    WHERE st.class_id = ?
    ORDER BY st.name
");
$stmt->bind_param("ii", $class_subject_id, $class_id);
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<h2>
    📝 Nhập điểm: 
    <?= htmlspecialchars($timetable['class_name']) ?> - 
    <?= htmlspecialchars($timetable['subject_name']) ?> 
    (<?= htmlspecialchars($semester) ?>)
</h2>

<?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>

<form method="post">
<table border="1" cellpadding="10" cellspacing="0" style="width:90%; margin:auto; border-collapse:collapse;">
<tr style="background:#34495e; color:white;">
    <th>STT</th>
    <th>Học sinh</th>
    <th>KT1</th>
    <th>KT2</th>
    <th>Cuối kỳ</th>
</tr>

<?php 
$i = 1; 
foreach ($students as $st): 
?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= htmlspecialchars($st['name']) ?></td>
    <td><input type="number" name="grades[<?= $st['id'] ?>][kt1]" value="<?= $st['kt1'] ?>" step="0.01" min="0" max="10"></td>
    <td><input type="number" name="grades[<?= $st['id'] ?>][kt2]" value="<?= $st['kt2'] ?>" step="0.01" min="0" max="10"></td>
    <td><input type="number" name="grades[<?= $st['id'] ?>][final_exam]" value="<?= $st['final_exam'] ?>" step="0.01" min="0" max="10"></td>
</tr>
<?php endforeach; ?>
</table>

<div style="text-align:center; margin-top:20px;">
    <button type="submit" 
            style="padding:10px 20px; background:#3498db; color:white; border:none; border-radius:5px; font-size:16px;">
        💾 Lưu điểm
    </button>
</div>
</form>

<h3 style="text-align:center; margin-top:40px;">📊 Bảng tổng kết điểm</h3>

<table border="1" cellpadding="10" cellspacing="0" style="width:90%; margin:auto; border-collapse:collapse;">
<tr style="background:#2ecc71; color:white;">
    <th>STT</th>
    <th>Học sinh</th>
    <th>KT1</th>
    <th>KT2</th>
    <th>Cuối kỳ</th>
    <th>Tổng kết</th>
</tr>

<?php 
$i = 1;
foreach ($students as $st): 
?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= htmlspecialchars($st['name']) ?></td>
    <td><?= htmlspecialchars($st['kt1']) ?></td>
    <td><?= htmlspecialchars($st['kt2']) ?></td>
    <td><?= htmlspecialchars($st['final_exam']) ?></td>
    <td><?= htmlspecialchars($st['grade']) ?></td>
</tr>
<?php endforeach; ?>
</table>
