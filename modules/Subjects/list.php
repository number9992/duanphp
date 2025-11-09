<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();

$res = $conn->query("SELECT * FROM subjects ORDER BY id DESC");
include __DIR__.'/../../includes/header.php';
?>

<h2>Danh sách Môn học</h2>
<a class="btn" href="?url=subjects/add">+ Thêm môn học</a>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th><th>Mã môn</th><th>Tên môn</th><th>Số tín chỉ</th><th>Hành động</th>
</tr>
<?php while($row = $res->fetch_assoc()): ?>
<tr>
    <td><?= esc($row['id']) ?></td>
    <td><?= esc($row['subject_code']) ?></td>
    <td><?= esc($row['subject_name']) ?></td>
    <td><?= esc($row['credit_hours']) ?></td>
    <td>
        <a href="?url=subjects/edit&id=<?= $row['id'] ?>">Sửa</a> |
        <a href="?url=subjects/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa môn học?')">Xóa</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php include __DIR__.'/../../includes/footer.php'; ?>
