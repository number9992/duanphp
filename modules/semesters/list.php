<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
include __DIR__ . '/../../includes/header.php';

$sql = "SELECT * FROM semesters ORDER BY start_date DESC";
$result = $conn->query($sql);
?>

<div class="container">
  <h2>Danh sách học kỳ</h2>
  <a href="?url=semesters/add" class="btn btn-primary">+ Thêm học kỳ</a>
  <table border="1" cellpadding="10" cellspacing="0">
    <tr>
      <th>ID</th><th>Tên học kỳ</th><th>Ngày bắt đầu</th><th>Ngày kết thúc</th><th>Hành động</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= $row['start_date'] ?></td>
      <td><?= $row['end_date'] ?></td>
      <td>
        <a href="edit.php?id=<?= $row['id'] ?>">Sửa</a> |
        <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Xóa học kỳ này?')">Xóa</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

