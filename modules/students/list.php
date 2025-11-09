<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

// Lấy danh sách sinh viên kèm tên lớp
$sql = "
    SELECT st.id, st.name, st.email, st.phone, st.photo, c.class_name
    FROM students st
    LEFT JOIN classes c ON st.class_id = c.id
    ORDER BY st.id DESC
";
$res = $conn->query($sql);

include __DIR__ . '/../../includes/header.php';
?>
<h2>Danh sách Sinh viên</h2>
<a class="btn" href="?url=student/add">+ Thêm sinh viên</a>

<table>
    <tr>
        <th>ID</th><th>Ảnh</th><th>Họ tên</th><th>Email</th><th>Phone</th><th>Lớp</th><th>Hành động</th>
    </tr>
    <?php while($row = $res->fetch_assoc()): ?>
    <tr>
        <td><?= esc($row['id']) ?></td>
        <td><?php if($row['photo']): ?><img src="/<?= esc($row['photo']) ?>" style="height:48px"><?php endif; ?></td>
        <td><?= esc($row['name']) ?></td>
        <td><?= esc($row['email']) ?></td>
        <td><?= esc($row['phone']) ?></td>
        <td><?= esc($row['class_name']) ?></td>
        <td>
            <a href="?url=student/edit&id=<?= $row['id'] ?>">Sửa</a> | <a href="?url=student/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
