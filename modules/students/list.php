<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
include __DIR__ . '/../../includes/header.php';

$res = $conn->query("SELECT * FROM students ORDER BY id DESC");
?>
<style>
.page-container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 20px;
}

body {
  font-family: 'Segoe UI', Tahoma, sans-serif;
  background-color: #f4f6f9;
  margin: 0;
  padding: 0;
  color: #333;
}

h2 {
  text-align: center;
  color: #2c3e50;
  margin-bottom: 30px;
}

a.btn {
  display: inline-block;
  margin-bottom: 20px;
  padding: 10px 16px;
  background-color: #3498db;
  color: white;
  text-decoration: none;
  font-weight: bold;
  border-radius: 6px;
  transition: background-color 0.3s ease;
}

a.btn:hover {
  background-color: #2980b9;
}

table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  border-radius: 8px;
  overflow: hidden;
}

table th, table td {
  padding: 14px 16px;
  text-align: left;
  border-bottom: 1px solid #eaeaea;
}

table th {
  background-color: #ecf0f1;
  color: #34495e;
  font-weight: 600;
  text-align: center;
}

table td {
  text-align: center;
}

table tr:hover {
  background-color: #f9f9f9;
}

table td a {
  color: #3498db;
  text-decoration: none;
  font-weight: 500;
  padding: 4px 6px;
}

table td a:hover {
  text-decoration: underline;
}

 footer {
  text-align: center;
} 

</style>

<h2>Danh sách Sinh viên</h2>

<a class="btn" href="?url=student/add">+ Thêm sinh viên</a>
<table>
    <tr><th>ID</th><th>Ảnh</th><th>Họ tên</th><th>Email</th><th>Phone</th><th>Lớp</th><th>Hành động</th></tr>
    <?php while($row = $res->fetch_assoc()): ?>
    <tr>
        <td><?= esc($row['id']) ?></td>
        <td><?php if($row['photo']): ?><img src="/<?= esc($row['photo']) ?>" alt="" style="height:48px"><?php endif; ?></td>
        <td><?= esc($row['name']) ?></td>
        <td><?= esc($row['email']) ?></td>
        <td><?= esc($row['phone']) ?></td>
        <td><?= esc($row['class']) ?></td>
        <td>
            <a href="?url=student/edit&id=<?= $row['id'] ?>">Sửa</a> | <a href="?url=student/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
