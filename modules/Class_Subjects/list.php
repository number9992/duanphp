<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();

$sql = "
SELECT cs.id, c.class_name, s.subject_name, t.name as teacher_name, cs.semester
FROM class_subjects cs
LEFT JOIN classes c ON cs.class_id = c.id
LEFT JOIN subjects s ON cs.subject_id = s.id
LEFT JOIN teachers t ON cs.teacher_id = t.id
ORDER BY cs.id DESC
";
$res = $conn->query($sql);
include __DIR__.'/../../includes/header.php';
?>

<h2>Danh sách phân công môn học</h2>
<a class="btn" href="?url=class_subjects/add">+ Thêm</a>
<table border="1" cellpadding="10">
<tr>
    <th>ID</th><th>Lớp</th><th>Môn</th><th>Giảng viên</th><th>Học kỳ</th><th>Hành động</th>
</tr>
<?php while($row = $res->fetch_assoc()): ?>
<tr>
    <td><?= esc($row['id']) ?></td>
    <td><?= esc($row['class_name']) ?></td>
    <td><?= esc($row['subject_name']) ?></td>
    <td><?= esc($row['teacher_name']) ?></td>
    <td><?= esc($row['semester']) ?></td>
    <td>
        <a href="?url=class_subjects/edit&id=<?= $row['id'] ?>">Sửa</a> |
        <a href="?url=class_subjects/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php include __DIR__.'/../../includes/footer.php'; ?>
