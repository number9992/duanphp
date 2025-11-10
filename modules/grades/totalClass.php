<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();
include __DIR__.'/../../includes/header.php';

$class_id = intval($_GET['class_id'] ?? 0);
if(!$class_id){
    echo "<p>Chọn lớp để xem môn học.</p>";
    exit;
}

// Lấy tên lớp
$stmt = $conn->prepare("SELECT class_name FROM classes WHERE id=?");
$stmt->bind_param('i',$class_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();
$class_name = $class['class_name'] ?? '';

// Lấy danh sách môn học lớp này
$sql = "
SELECT cs.id AS class_subject_id, s.subject_name, cs.semester
FROM class_subjects cs
LEFT JOIN subjects s ON cs.subject_id = s.id
WHERE cs.class_id=?
ORDER BY cs.semester, s.subject_name
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i',$class_id);
$stmt->execute();
$res = $stmt->get_result();
?>

<h2>Chọn Môn học để nhập điểm - Lớp <?= esc($class_name) ?></h2>

<?php if($res->num_rows === 0): ?>
    <p>Hiện chưa có môn học nào được phân công cho lớp này.</p>
<?php else: ?>
<table border="1" cellpadding="10" cellspacing="0" width="80%" style="margin:auto;border-collapse:collapse">
<tr style="background:#f2f2f2">
    <th>#</th>
    <th>Môn học</th>
    <th>Học kỳ</th>
    <th>Thao tác</th>
</tr>
<?php $i=1; while($row=$res->fetch_assoc()): ?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= esc($row['subject_name']) ?></td>
    <td><?= esc($row['semester']) ?></td>
    <td>
        <a href="?url=grades/input&class_subject_id=<?= $row['class_subject_id'] ?>">Nhập điểm</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php endif; ?>

<?php include __DIR__.'/../../includes/footer.php'; ?>
