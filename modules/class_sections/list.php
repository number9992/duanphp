<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
include __DIR__ . '/../../includes/header.php';

// ✅ Lấy danh sách lớp học phần kèm tên môn, học kỳ và giảng viên
$sql = "
    SELECT 
        cs.id,
        cs.name AS class_name,
        c.name AS course_name,
        s.name AS semester_name,
        t.name AS teacher_name
    FROM class_sections cs
    LEFT JOIN courses c ON cs.course_id = c.id
    LEFT JOIN teachers t ON c.teacher_id = t.id
    LEFT JOIN semesters s ON cs.semester_id = s.id
    ORDER BY cs.id DESC
";
$result = $conn->query($sql);
?>

<div class="container">
  <h2>Danh sách lớp học phần</h2>
  <a href="?url=class_sections/add" class="btn btn-primary">+ Thêm lớp học phần</a>
  <table border="1" cellpadding="10" cellspacing="0" width="100%">
    <tr>
      <th>ID</th>
      <th>Tên lớp học phần</th>
      <th>Môn học</th>
      <th>Học kỳ</th>
      <th>Giảng viên</th>
      <th>Hành động</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= htmlspecialchars($row['class_name']) ?></td>
      <td><?= htmlspecialchars($row['course_name']) ?></td>
      <td><?= htmlspecialchars($row['semester_name']) ?></td>
      <td><?= htmlspecialchars($row['teacher_name']) ?></td>
      <td>
        <a href="?url=class_sections/edit&id=<?= $row['id'] ?>">Sửa</a> |
        <a href="?url=class_sections/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa lớp này?')">Xóa</a> |
        <a href="?url=class_sections/enroll&id=<?= $row['id'] ?>">Ghi danh</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
