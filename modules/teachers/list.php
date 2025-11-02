<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
include __DIR__ . '/../../includes/header.php';

$res = $conn->query("SELECT * FROM teachers ORDER BY id DESC");
?>
<style>body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background-color: #f7f9fc;
    margin: 0;
    padding: 0;
    color: #333;
}

h2 {
    text-align: center;
    margin: 30px 0;
    font-size: 28px;
    color: #2c3e50;
}

a.btn {
    display: block;
    width: fit-content;
    margin: 0 auto 20px auto;
    padding: 10px 20px;
    background-color: #27ae60;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

a.btn:hover {
    background-color: #219150;
}

table {
    width: 90%;
    margin: 0 auto 40px auto;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

table th, table td {
    padding: 14px 16px;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
}

table th {
    background-color: #34495e;
    color: white;
    font-weight: 600;
}

table tr:hover {
    background-color: #f1f1f1;
}

img {
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

a {
    color: #2980b9;
    text-decoration: none;
    font-weight: 500;
}

a:hover {
    text-decoration: underline;
    color: #1c6ea4;
}

footer {
    text-align: center;
    padding: 20px;
    background-color: #34495e;
    color: white;
    font-size: 14px;
}</style>
<h2>Danh sách Giảng viên</h2>
<a class="btn" href="?url=teacher/add">+ Thêm giảng viên</a>
<table>
    <tr><th>ID</th><th>Ảnh</th><th>Họ tên</th><th>Email</th><th>Phone</th><th>Khoa</th><th>Hành động</th></tr>
    <?php while($row = $res->fetch_assoc()): ?>
    <tr>
        <td><?= esc($row['id']) ?></td>
        <td><?php if($row['photo']): ?><img src="/<?= esc($row['photo']) ?>" style="height:48px"><?php endif; ?></td>
        <td><?= esc($row['name']) ?></td>
        <td><?= esc($row['email']) ?></td>
        <td><?= esc($row['phone']) ?></td>
        <td><?= esc($row['department']) ?></td>
        <td>
            <a href="?url=teacher/edit&id=<?= $row['id'] ?>">Sửa</a>
            <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
