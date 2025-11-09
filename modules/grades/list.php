<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();

$class_subject_id = intval($_GET['class_subject_id'] ?? 0);
if(!$class_subject_id){ echo "Không có môn học được chọn."; exit; }

// Lấy thông tin lớp + môn
$stmt = $conn->prepare("
SELECT cs.id AS cs_id, c.class_name, s.subject_name, cs.semester
FROM class_subjects cs
JOIN classes c ON cs.class_id = c.id
JOIN subjects s ON cs.subject_id = s.id
WHERE cs.id=?
");
$stmt->bind_param('i',$class_subject_id);
$stmt->execute();
$class_subject = $stmt->get_result()->fetch_assoc();
if(!$class_subject){ echo "Không tìm thấy môn học."; exit; }

// Lấy danh sách học sinh + điểm
$stmt = $conn->prepare("
SELECT st.id, st.name, g.kt1, g.kt2, g.final_exam
FROM students st
LEFT JOIN grades g ON g.student_id=st.id AND g.class_subject_id=?
WHERE st.class_id = (SELECT class_id FROM class_subjects WHERE id=?)
ORDER BY st.name
");
$stmt->bind_param('ii',$class_subject_id,$class_subject_id);
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include __DIR__.'/../../includes/header.php';
?>

<h2>Tổng kết điểm: <?= esc($class_subject['class_name']) ?> - <?= esc($class_subject['subject_name']) ?> (<?= esc($class_subject['semester']) ?>)</h2>

<table border="1" cellpadding="10" cellspacing="0" style="width:90%; margin:auto; border-collapse:collapse;">
<tr style="background:#34495e; color:white;">
    <th>STT</th>
    <th>Học sinh</th>
    <th>KT1</th>
    <th>KT2</th>
    <th>Cuối kỳ</th>
    <th>Tổng kết</th>
    <th>Xếp loại</th>
</tr>
<?php $i=1; foreach($students as $st):
    $kt1 = $st['kt1'] ?? 0;
    $kt2 = $st['kt2'] ?? 0;
    $final_exam = $st['final_exam'] ?? 0;
    // Công thức tổng kết: 30% KT1 + 30% KT2 + 40% cuối kỳ
    $total = round($kt1*0.3 + $kt2*0.3 + $final_exam*0.4, 2);
    $rank = $total >= 8 ? 'Giỏi' : ($total >=6.5 ? 'Khá' : ($total>=5 ? 'Trung bình' : 'Yếu'));
?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= esc($st['name']) ?></td>
    <td><?= $st['kt1'] ?? '-' ?></td>
    <td><?= $st['kt2'] ?? '-' ?></td>
    <td><?= $st['final_exam'] ?? '-' ?></td>
    <td><?= $total ?></td>
    <td><?= $rank ?></td>
</tr>
<?php endforeach; ?>
</table>

<div style="text-align:center; margin-top:20px;">
    <a href="?url=grades/input&class_subject_id=<?= $class_subject_id ?>" style="padding:10px 20px; background:#3498db; color:white; border:none; border-radius:5px; text-decoration:none;">Quay lại nhập điểm</a>
</div>

<?php include __DIR__.'/../../includes/footer.php'; ?>
