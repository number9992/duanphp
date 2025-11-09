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
<style>
    body {
  font-family: 'Segoe UI', Tahoma, sans-serif;
  background-color: #f4f6f9;
  margin: 0;
  color: #333;
}

h2 {
  text-align: center;
  color: #2c3e50;
  margin: 30px;
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
}

table tr:hover {
  background-color: #f9f9f9;
}

table td a {
  color: #3498db;
  text-decoration: none;
  font-weight: 500;
}

table td a:hover {
  text-decoration: underline;
}

 footer {
  text-align: center;
} 

</style>
<a class="btn" href="?url=scores/add">+ Thêm điểm</a>
<table>
    <tr><th>ID</th><th>Sinh viên</th><th>Môn</th><th>Điểm</th><th>Hành động</th></tr>
    <?php while($r = $res->fetch_assoc()): ?>
    <tr>
        <td><?= esc($r['id']) ?></td>
        <td><?= esc($r['student_name']) ?></td>
        <td><?= esc($r['course_name']) ?></td>
        <td><?= esc($r['score']) ?></td>
        <td><a href="?url=scores/edit&id=<?= $r['id'] ?>">Sửa</a> | <a href="?url=scores/delete&id=<?= $r['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
