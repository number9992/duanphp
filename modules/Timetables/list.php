<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();

$sql = "
SELECT tt.id, c.class_name, s.subject_name, t.name as teacher_name, tt.semester, tt.day_of_week, tt.session, tt.period, tt.room
FROM timetables tt
LEFT JOIN classes c ON tt.class_id = c.id
LEFT JOIN subjects s ON tt.subject_id = s.id
LEFT JOIN teachers t ON tt.teacher_id = t.id
ORDER BY tt.class_id, FIELD(tt.day_of_week,'Mon','Tue','Wed','Thu','Fri','Sat'), tt.session, tt.period
";
$res = $conn->query($sql);

include __DIR__.'/../../includes/header.php';
?>
<h2>Thời khóa biểu</h2>
<a class="btn" href="?url=timetables/add">+ Thêm</a>
<table border="1" cellpadding="10">
<tr>
<th>ID</th><th>Lớp</th><th>Môn</th><th>GV</th><th>Học kỳ</th><th>Ngày</th><th>Ca</th><th>Tiết</th><th>Phòng</th><th>Hành động</th>
</tr>
<?php while($row = $res->fetch_assoc()): ?>
<tr>
<td><?= esc($row['id']) ?></td>
<td><?= esc($row['class_name']) ?></td>
<td><?= esc($row['subject_name']) ?></td>
<td><?= esc($row['teacher_name']) ?></td>
<td><?= esc($row['semester']) ?></td>
<td><?= esc($row['day_of_week']) ?></td>
<td><?= esc($row['session']) ?></td>
<td><?= esc($row['period']) ?></td>
<td><?= esc($row['room']) ?></td>
<td>
<a href="?url=timetables/edit&id=<?= $row['id'] ?>">Sửa</a> |
<a href="?url=timetables/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a>
</td>
</tr>
<?php endwhile; ?>
</table>
<?php include __DIR__.'/../../includes/footer.php'; ?>
