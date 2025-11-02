<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
include __DIR__ . '/../../includes/header.php';

$res = $conn->query("SELECT s.*, st.name as student_name, c.name as course_name 
    FROM scores s 
    JOIN students st ON s.student_id = st.id
    JOIN courses c ON s.course_id = c.id
    ORDER BY s.id DESC");
?>
<h2>Danh sách Điểm</h2>
<a class="btn" href="add.php">+ Thêm điểm</a>
<table>
    <tr><th>ID</th><th>Sinh viên</th><th>Môn</th><th>Điểm</th><th>Hành động</th></tr>
    <?php while($r = $res->fetch_assoc()): ?>
    <tr>
        <td><?= esc($r['id']) ?></td>
        <td><?= esc($r['student_name']) ?></td>
        <td><?= esc($r['course_name']) ?></td>
        <td><?= esc($r['score']) ?></td>
        <td><a href="edit.php?id=<?= $r['id'] ?>">Sửa</a> | <a href="delete.php?id=<?= $r['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
