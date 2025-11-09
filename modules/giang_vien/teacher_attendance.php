<?php
session_start();
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();

// Chỉ giáo viên
if ($_SESSION['role'] !== 'teacher') die("❌ Bạn không có quyền truy cập!");

// Lấy teacher_id
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT teacher_id FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();
$teacher_id = $teacher['teacher_id'] ?? 0;
if(!$teacher_id) die("⚠️ Bạn không liên kết với giáo viên nào.");

// Lấy class_subject_id từ URL
$class_subject_id = intval($_GET['class_subject_id'] ?? 0);
if(!$class_subject_id) die("Chưa chọn lớp/môn.");

// Lấy danh sách học sinh
$stmt = $conn->prepare("
    SELECT st.id, st.name
    FROM students st
    JOIN class_subjects cs ON cs.class_id = st.class_id
    WHERE cs.id = ?
");
$stmt->bind_param("i",$class_subject_id);
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Xử lý POST điểm danh
$message = '';
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['attendance'])){
    $date = $_POST['date'] ?? date('Y-m-d');
    $stmt_insert = $conn->prepare("
        INSERT INTO attendance(student_id,class_subject_id,date,status)
        VALUES(?,?,?,?)
        ON DUPLICATE KEY UPDATE status=VALUES(status)
    ");
    foreach($_POST['attendance'] as $student_id => $status){
        $stmt_insert->bind_param("iiss",$student_id,$class_subject_id,$date,$status);
        $stmt_insert->execute();
    }
    $message = "✅ Điểm danh đã lưu thành công cho ngày $date!";
}

// Lấy dữ liệu điểm danh ngày chọn
$date = $_GET['date'] ?? date('Y-m-d');
$stmt = $conn->prepare("
    SELECT student_id, status
    FROM attendance
    WHERE class_subject_id=? AND date=?
");
$stmt->bind_param("is",$class_subject_id,$date);
$stmt->execute();
$attendance_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$attendance_map = [];
foreach($attendance_data as $a) $attendance_map[$a['student_id']] = $a['status'] ?? 'present';

// Lọc học sinh vắng
$absent_students = [];
foreach($students as $st){
    if(($attendance_map[$st['id']] ?? 'present') === 'absent'){
        $absent_students[] = $st;
    }
}
?>

<h2>📋 Điểm danh lớp/môn #<?= $class_subject_id ?> ngày <?= $date ?></h2>
<?php if(!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>

<form method="post">
<p>Ngày: <input type="date" name="date" value="<?= htmlspecialchars($date) ?>"></p>

<table border="1" cellpadding="10" style="border-collapse:collapse; width:80%; margin:auto;">
<tr style="background:#34495e; color:white;">
    <th>STT</th>
    <th>Học sinh</th>
    <th>Có mặt</th>
    <th>Vắng</th>
</tr>

<?php $i=1; foreach($students as $st): 
    $status = $attendance_map[$st['id']] ?? 'present';
?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= htmlspecialchars($st['name']) ?></td>
    <td style="text-align:center;">
        <input type="radio" name="attendance[<?= $st['id'] ?>]" value="present" <?= $status=='present'?'checked':'' ?>>
    </td>
    <td style="text-align:center;">
        <input type="radio" name="attendance[<?= $st['id'] ?>]" value="absent" <?= $status=='absent'?'checked':'' ?>>
    </td>
</tr>
<?php endforeach; ?>
</table>

<div style="text-align:center; margin-top:20px;">
    <button type="submit" style="padding:10px 20px; background:#3498db; color:white; border:none; border-radius:5px; font-size:16px;">💾 Lưu điểm danh</button>
</div>
</form>

<?php if(count($absent_students) > 0): ?>
<h3 style="margin-top:40px; text-align:center; color:red;">❌ Danh sách học sinh vắng hôm nay</h3>
<table border="1" cellpadding="10" style="border-collapse:collapse; width:50%; margin:auto;">
<tr style="background:#e74c3c; color:white;">
    <th>STT</th>
    <th>Học sinh</th>
</tr>
<?php $i=1; foreach($absent_students as $st): ?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= htmlspecialchars($st['name']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p style="text-align:center; color:green; margin-top:20px;">✅ Không có học sinh nào vắng hôm nay.</p>
<?php endif; ?>
