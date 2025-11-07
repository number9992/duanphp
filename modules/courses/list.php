



<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
include __DIR__ . '/../../includes/header.php';

$res = $conn->query("SELECT c.*, t.name as teacher_name FROM courses c LEFT JOIN teachers t ON c.teacher_id = t.id ORDER BY c.id DESC");
?>
<h2>Danh sách Môn học</h2>
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
</style>
<a class="btn" href="?url=courses/add">+ Thêm môn</a>
<table>
    <tr><th>ID</th><th>Tên</th><th>Giảng viên</th><th>Mô tả</th><th>Hành động</th></tr>
    <?php while($r = $res->fetch_assoc()): ?>
    <tr>
        <td><?= esc($r['id']) ?></td>
        <td><?= esc($r['name']) ?></td>
        <td><?= esc($r['teacher_name']) ?></td>
        <td><?= esc(mb_strimwidth($r['description'],0,80,'...')) ?></td>
        <td><a href="?url=courses/edit&id=<?= $r['id'] ?>">Sửa</a> | <a href="?url=courses/delete&id=<?= $r['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a></td>
    </tr>
    <?php endwhile; ?>
</table>
<script>
document.addEventListener('DOMContentLoaded', function(){
  var cw = document.querySelector('.content-wrapper');
  if(cw) cw.classList.add('wide');
});
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
