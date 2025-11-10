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

<style>
    body {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 20px;
    color: #333;
}

h2 {
    color: #2c3e50;
    margin-bottom: 20px;
    text-align: center;
}

a.btn {
    display: inline-block;
    padding: 10px 15px;
    background: #3498db;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.3s ease;
    margin: 10px auto;
}

a.btn:hover {
    background: #2980b9;
}

table {
    width: 95%;
    margin: 20px auto;
    border-collapse: collapse;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    border-radius: 6px;
    overflow: hidden;
}

table th, table td {
    padding: 12px 15px;
    text-align: left;
    vertical-align: middle;
}

table th {
    background: #34495e;
    color: #fff;
    font-weight: 600;
}

table tr:nth-child(even) {
    background: #f9f9f9;
}

table tr:hover {
    background: #ecf0f1;
}

table td a {
    color: #3498db;
    text-decoration: none;
    font-weight: 500;
    margin: 0 5px;
}

table td a:hover {
    text-decoration: underline;
}
</style>

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
