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
<<<<<<< HEAD
  font-family: 'Segoe UI', Tahoma, sans-serif;
  background-color: #f4f6f9;
  margin: 0;
  padding: 20px;
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
  background-color: #3498db; /* xanh lá */
  color: white;
  text-decoration: none;
  font-weight: bold;
  border-radius: 6px;
  transition: background-color 0.3s ease;
}

a.btn:hover {
  background-color: #2980b9;
=======
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
    display: block;
    width: fit-content;
    margin: 0 auto 20px auto;
    margin-left:53px;
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
>>>>>>> 2c3a5a4544216ed5a8ccbc5aa81f494f748cec43
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
}

table tr:hover {
  background-color: #f9f9f9;
}

img {
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    height:48px;
}

table td a {
  color: #3498db;
  text-decoration: none;
  font-weight: 500;
}

table td a:hover {
  text-decoration: underline;
}

<<<<<<< HEAD
 footer {
  text-align: center;
} 
=======
footer {
   text-align: center;
   padding: 20px;
   background-color: #f4f6f8;
   color: #2c3e50;
   font-size: 14px;
}
>>>>>>> 2c3a5a4544216ed5a8ccbc5aa81f494f748cec43
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
<<<<<<< HEAD
            <a href="?url=teacher/edit&id=<?= $row['id'] ?>">Sửa</a> | <a href="?url=teacher/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a>
=======
            <a href="?url=teacher/edit&id=<?= $row['id'] ?>">Sửa</a> |
            <a href="?url=teacher/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa giáo viên này?')">Xóa</a>
>>>>>>> 2c3a5a4544216ed5a8ccbc5aa81f494f748cec43
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
