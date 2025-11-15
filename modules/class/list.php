<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
include __DIR__ . '/../../includes/header.php';

// Lấy danh sách lớp và TÊN giáo viên chủ nhiệm
$res = $conn->query("
    SELECT 
        c.id, 
        c.class_name, 
        c.grade_level, 
        t.name AS homeroom_teacher, 
        c.school_year
    FROM classes c
    LEFT JOIN teachers t ON c.homeroom_teacher_id = t.id
    ORDER BY c.grade_level, c.class_name
");
?>

<style>
    /* Tổng quan trang */
    h2 {
        color: #2c3e50;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
        margin-bottom: 20px;
        font-size: 24px;
        display: inline-block;
    }

    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    /* Nút chính */
    .btn {
        background-color: #3498db;
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
        font-weight: 500;
    }

    .btn:hover {
        background-color: #2980b9;
    }

    /* Thiết kế Bảng (Table) */
    table {
        width: 100%; /* Đặt lại width 100% thay vì 90% cố định */
        margin: 0 auto;
        border-collapse: separate; 
        border-spacing: 0;
        background-color: #fff;
        border-radius: 8px; 
        overflow: hidden; 
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    /* Tiêu đề (Header) */
    table th {
        background-color: #2c3e50; 
        color: white;
        font-weight: 600;
        padding: 12px 15px;
        text-align: left;
        border-bottom: 2px solid #243444;
    }
    
    /* Căn giữa một số cột */
    table th:nth-child(1), table td:nth-child(1), /* ID */
    table th:nth-child(3), table td:nth-child(3), /* Cấp */
    table th:nth-child(5), table td:nth-child(5) /* Năm học */
    {
        text-align: center;
    }

    /* Các dòng (Rows) */
    table td {
        padding: 12px 15px;
        border-bottom: 1px solid #ecf0f1; 
        color: #34495e;
        vertical-align: middle;
        font-size: 14px;
    }

    /* Hiệu ứng Hover */
    table tbody tr:hover {
        background-color: #f7f9fc;
        transition: background-color 0.2s;
    }

    /* Hành động (Actions) */
    table td:last-child {
         text-align: right; /* Căn phải cột thao tác cho gọn */
         white-space: nowrap; /* Giữ các nút trên 1 dòng */
    }
    table td a {
        margin-left: 10px;
        text-decoration: none;
        font-weight: 500;
        padding: 4px 8px;
        border-radius: 3px;
        transition: opacity 0.2s;
        font-size: 13px;
    }
    table td a:hover {
        opacity: 0.8;
    }
</style>

<div class="header-actions">
    <h2>Danh sách Lớp học</h2>
    <a class="btn" href="?url=class/add">+ Thêm lớp</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên Lớp</th>
            <th>Cấp</th>
            <th>GV Chủ nhiệm</th>
            <th>Năm học</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = $res->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><strong><?= esc($row['class_name']) ?></strong></td>
        <td><?= esc($row['grade_level']) ?></td>
        <td><?= esc($row['homeroom_teacher'] ?? '---') ?></td> 
        <td><?= esc($row['school_year']) ?></td>
        <td>
            <a href="?url=class_schedule&class_id=<?= $row['id'] ?>">📅 TKB</a> 
            <a href="?url=grades&class_id=<?= $row['id'] ?>">📝 Điểm</a> 
            <a href="?url=class/edit&id=<?= $row['id'] ?>">✏️ Sửa</a> 
            <a href="?url=class/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa lớp này?')">🗑 Xóa</a>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../../includes/footer.php'; ?>