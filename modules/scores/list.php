<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
include __DIR__ . '/../../includes/header.php';

$res = $conn->query("SELECT c.*, t.name as teacher_name FROM courses c LEFT JOIN teachers t ON c.teacher_id = t.id ORDER BY c.id DESC");
?>
<h2>Danh sách Môn học</h2>
<a class="btn" href="add.php">+ Thêm môn</a>
<table>
    <tr><th>ID</th><th>Tên</th><th>Giảng viên</th><th>Mô tả</th><th>Hành động</th></tr>
    <?php while($r = $res->fetch_assoc()): ?>
    <tr>
        <td><?= esc($r['id']) ?></td>
        <td><?= esc($r['name']) ?></td>
        <td><?= esc($r['teacher_name']) ?></td>
        <td><?= esc(mb_strimwidth($r['description'],0,80,'...')) ?></td>
        <td><a href="edit.php?id=<?= $r['id'] ?>">Sửa</a> | <a href="delete.php?id=<?= $r['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
