<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
include __DIR__ . '/../../includes/header.php';

// Lấy danh sách lớp học phần kèm thông tin chi tiết
$sql = "
    SELECT 
        cs.id,
        cs.name AS class_name,
        c.name AS course_name,
        s.name AS semester_name,
        t.name AS teacher_name
    FROM class_sections cs
    LEFT JOIN courses c ON cs.course_id = c.id
    LEFT JOIN teachers t ON cs.teacher_id = t.id
    LEFT JOIN semesters s ON cs.semester_id = s.id
    ORDER BY cs.id DESC
";
$result = $conn->query($sql);
?>

<div class="container">
    <h2>Danh sách lớp học phần</h2>
    <a href="?url=class_sections/add" class="btn">+ Thêm lớp học phần</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Lớp học phần</th>
                <th>Môn học</th>
                <th>Học kỳ</th>
                <th>Giảng viên</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= esc($row['id']) ?></td>
                <td><?= esc($row['class_name']) ?></td>
                <td><?= esc($row['course_name']) ?></td>
                <td><?= esc($row['semester_name']) ?></td>
                <td><?= esc($row['teacher_name'] ?? '-') ?></td>
                <td>
                    <a href="?url=class_sections/edit&id=<?= $row['id'] ?>">✏️ Sửa</a> |
                    <a href="?url=class_sections/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa lớp học phần này?')">🗑 Xóa</a> |
                    <a href="?url=class_sections/enroll&id=<?= $row['id'] ?>">👨‍🎓 Ghi danh</a> |
                    <a href="?url=schedules&class_section_id=<?= $row['id'] ?>">📅 Lịch học</a> |
                    <a href="?url=attendance&class_section_id=<?= $row['id'] ?>">📋 Điểm danh</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<style>
.container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 0 15px;
    font-family: 'Segoe UI', Tahoma, sans-serif;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #2c3e50;
}

.btn {
    display: inline-block;
    margin-bottom: 15px;
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #2980b9;
}

.table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

.table th, .table td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
}

.table th {
    background-color: #34495e;
    color: white;
    font-weight: 600;
}

.table tr:hover {
    background-color: #f1f1f1;
}

.table a {
    text-decoration: none;
    color: #2980b9;
}

.table a:hover {
    text-decoration: underline;
    color: #1c6ea4;
}
</style>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
