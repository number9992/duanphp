<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();

$res = $conn->query("SELECT * FROM subjects ORDER BY id DESC");
include __DIR__.'/../../includes/header.php';
?>

<style>
/* Tối ưu CSS cho UX/UI */
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
    background-color: #27ae60; /* Màu xanh lá nổi bật cho nút Thêm */
    color: white;
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
    font-weight: 500;
}

.btn:hover {
    background-color: #229954;
}

/* Thiết kế Bảng (Table) */
table {
    width: 100%; 
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
table th:nth-child(4), table td:nth-child(4), /* Số tín chỉ */
table th:last-child, table td:last-child /* Hành động */
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
     text-align: center;
     white-space: nowrap; 
}
table td a {
    margin: 0 5px;
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
    <h2>Danh sách Môn học</h2>
    <a class="btn" href="?url=subjects/add">+ Thêm môn học</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Mã môn</th>
            <th>Tên môn</th>
            <th>Số tín chỉ</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = $res->fetch_assoc()): ?>
    <tr>
        <td><?= esc($row['id']) ?></td>
        <td><strong><?= esc($row['subject_code']) ?></strong></td>
        <td><?= esc($row['subject_name']) ?></td>
        <td><?= esc($row['credit_hours']) ?></td>
        <td>
            <a href="?url=subjects/edit&id=<?= $row['id'] ?>">✏️ Sửa</a>
            <a href="?url=subjects/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa môn học?')">🗑 Xóa</a>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php include __DIR__.'/../../includes/footer.php'; ?>