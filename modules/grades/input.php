<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();

$class_subject_id = intval($_GET['class_subject_id'] ?? 0);
if(!$class_subject_id) { echo "Không có lớp/môn nào được chọn."; exit; }

// Lấy thông tin lớp + môn
$stmt = $conn->prepare("
SELECT cs.id AS cs_id, c.class_name, s.subject_name, cs.semester
FROM class_subjects cs
JOIN classes c ON cs.class_id = c.id
JOIN subjects s ON cs.subject_id = s.id
WHERE cs.id=?
");
$stmt->bind_param('i', $class_subject_id);
$stmt->execute();
$class_subject = $stmt->get_result()->fetch_assoc();
if(!$class_subject) { echo "Không tìm thấy lớp/môn."; exit; }

// Lấy danh sách học sinh + điểm
$stmt = $conn->prepare("
SELECT st.id, st.name, g.kt1, g.kt2, g.final_exam
FROM students st
LEFT JOIN grades g ON g.student_id=st.id AND g.class_subject_id=?
WHERE st.class_id = (SELECT class_id FROM class_subjects WHERE id=?)
ORDER BY st.name
");
$stmt->bind_param('ii', $class_subject_id, $class_subject_id);
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Xử lý POST lưu điểm
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    foreach($_POST['grades'] as $student_id => $grades){
        $kt1 = $grades['kt1'] !== '' ? floatval($grades['kt1']) : null;
        $kt2 = $grades['kt2'] !== '' ? floatval($grades['kt2']) : null;
        $final_exam = $grades['final_exam'] !== '' ? floatval($grades['final_exam']) : null;

        $stmt = $conn->prepare("
        INSERT INTO grades (student_id, class_subject_id, kt1, kt2, final_exam)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE kt1=VALUES(kt1), kt2=VALUES(kt2), final_exam=VALUES(final_exam)
        ");
        $stmt->bind_param('idddd', $student_id, $class_subject_id, $kt1, $kt2, $final_exam);
        $stmt->execute();
    }
    header("Location:?url=grades/summary&class_subject_id=$class_subject_id");
    exit;
}

include __DIR__.'/../../includes/header.php';
?>

<h2>Nhập điểm: <?= esc($class_subject['class_name']) ?> - <?= esc($class_subject['subject_name']) ?> (<?= esc($class_subject['semester']) ?>)</h2>

<form method="post">
<table border="1" cellpadding="10" cellspacing="0" style="width:90%; margin:auto; border-collapse:collapse;">
<tr style="background:#34495e; color:white;">
    <th>STT</th>
    <th>Học sinh</th>
    <th>KT1</th>
    <th>KT2</th>
    <th>Cuối kỳ</th>
</tr>
<?php $i=1; foreach($students as $st): ?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= esc($st['name']) ?></td>
    <td><input type="number" name="grades[<?= $st['id'] ?>][kt1]" value="<?= esc($st['kt1']) ?>" step="0.01" min="0" max="10" style="width:60px"></td>
    <td><input type="number" name="grades[<?= $st['id'] ?>][kt2]" value="<?= esc($st['kt2']) ?>" step="0.01" min="0" max="10" style="width:60px"></td>
    <td><input type="number" name="grades[<?= $st['id'] ?>][final_exam]" value="<?= esc($st['final_exam']) ?>" step="0.01" min="0" max="10" style="width:60px"></td>
</tr>
<?php endforeach; ?>
</table>
<div style="text-align:center; margin-top:20px;">
    <button type="submit" style="padding:10px 20px; background:#3498db; color:white; border:none; border-radius:5px; font-size:16px;">Lưu điểm</button>
</div>
</form>

<?php include __DIR__.'/../../includes/footer.php'; ?>
