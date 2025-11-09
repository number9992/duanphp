<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
include __DIR__ . '/../../includes/header.php';

$res = $conn->query("
    SELECT c.id, c.class_name, c.grade_level, t.name AS homeroom_teacher, c.school_year
    FROM classes c
    LEFT JOIN teachers t ON c.homeroom_teacher_id = t.id
    ORDER BY c.grade_level, c.class_name
");
?>

<h2>Danh sách Lớp</h2>
<a class="btn" href="?url=class/add">+ Thêm lớp</a>

<table border="1" cellpadding="10" cellspacing="0" width="90%" style="margin: 20px auto; border-collapse: collapse;">
    <tr style="background-color: #f2f2f2;">
        <th>ID</th>
        <th>Tên lớp</th>
        <th>Cấp</th>
        <th>GVCN</th>
        <th>Năm học</th>
        <th>Thao tác</th>
    </tr>
    <?php while($row = $res->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= esc($row['class_name']) ?></td>
        <td><?= esc($row['grade_level']) ?></td>
        <td><?= esc($row['homeroom_teacher']) ?></td>
        <td><?= esc($row['school_year']) ?></td>
        <td>
            <a href="?url=class_schedule&class_id=<?= $row['id'] ?>">📅 Quản lý thời khóa biểu</a> |
            <a href="?url=grades&class_id=<?= $row['id'] ?>">📝 Quản lý điểm</a> |
            <a href="?url=class/edit&id=<?= $row['id'] ?>">✏️ Sửa</a> |
            <a href="?url=class/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa lớp này?')">🗑 Xóa</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
