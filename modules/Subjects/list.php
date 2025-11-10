<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();

$res = $conn->query("SELECT * FROM subjects ORDER BY id DESC");
include __DIR__.'/../../includes/header.php';
?>

<h2>Danh sách Môn học</h2>

<style>
    body {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 20px;
    color: #333;
}

h2 {
    color: #2c3e50;
    margin-bottom: 20px;
}

a.btn {
    display: inline-block;
    padding: 10px 15px;
    background: #3498db;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.3s ease;
    margin-bottom: 15px;
}

a.btn:hover {
    background: #2980b9;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    border-radius: 6px;
    overflow: hidden;
}

table th, table td {
    padding: 12px 15px;
    text-align: left;
}

table th {
    background: #34495e;
    color: #fff;
    font-weight: 600;
}

table tr:nth-child(even) {
    background: #f9f9f9;
}

table tr:hover {
    background: #ecf0f1;
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

<a class="btn" href="?url=subjects/add">+ Thêm môn học</a>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th><th>Mã môn</th><th>Tên môn</th><th>Số tín chỉ</th><th>Hành động</th>
</tr>
<?php while($row = $res->fetch_assoc()): ?>
<tr>
    <td><?= esc($row['id']) ?></td>
    <td><?= esc($row['subject_code']) ?></td>
    <td><?= esc($row['subject_name']) ?></td>
    <td><?= esc($row['credit_hours']) ?></td>
    <td>
        <a href="?url=subjects/edit&id=<?= $row['id'] ?>">Sửa</a> |
        <a href="?url=subjects/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa môn học?')">Xóa</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php include __DIR__.'/../../includes/footer.php'; ?>
