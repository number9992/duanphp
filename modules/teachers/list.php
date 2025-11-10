<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin(); // Kiểm tra login
include __DIR__ . '/../../includes/header.php';

// Lấy danh sách giáo viên từ DB mới
$res = $conn->query("SELECT * FROM teachers ORDER BY id DESC");
?>
<style>
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background-color: #f7f9fc;
    margin: 0;
    padding: 0;
    color: #333;
}

h2 {
    text-align: center;
    margin: 30px 0;
    color: #2c3e50;
}

a.btn {
    display: inline-block;
    margin: 20px 0 20px 0;
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

a.btn:hover {
    background-color: #2980b9;
}

table {
    width: 100%;
    margin: 0 0 40px 0;
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
  background-color: #215dc6ff;
  color: white;
  font-weight: 600;
}

table tr:hover {
    background-color: #f1f1f1;
}

img {
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    height:48px;
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
   background-color: #f4f6f8;
   color: #2c3e50;
   font-size: 14px;
}
</style>

<h2>Danh sách Giảng viên</h2>
<a class="btn" href="?url=teacher/add">+ Thêm giảng viên</a>

<table>
    <tr>
        <th>ID</th>
        <th>Ảnh</th>
        <th>Họ tên</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Khoa</th>
        <th>Hành động</th>
    </tr>
    <?php while($row = $res->fetch_assoc()): ?>
    <tr>
        <td><?= esc($row['id']) ?></td>
        <td>
            <?php if(!empty($row['photo'])): ?>
                <img src="/<?= esc($row['photo']) ?>" alt="Ảnh GV">
            <?php endif; ?>
        </td>
        <td><?= esc($row['name']) ?></td>
        <td><?= esc($row['email']) ?></td>
        <td><?= esc($row['phone']) ?></td>
        <td><?= esc($row['department']) ?></td>
        <td>
            <a href="?url=teacher/edit&id=<?= $row['id'] ?>">Sửa</a> |
            <a href="?url=teacher/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa giáo viên này?')">Xóa</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
