<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

// Lấy danh sách sinh viên kèm tên lớp
$sql = "
    SELECT st.id, st.name, st.email, st.phone, st.photo, c.class_name
    FROM students st
    LEFT JOIN classes c ON st.class_id = c.id
    ORDER BY st.id DESC
";
$res = $conn->query($sql);

include __DIR__ . '/../../includes/header.php';
?>
<h2>Danh sách Sinh viên</h2>
<a class="btn" href="?url=student/add">+ Thêm sinh viên</a>

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
    vertical-align: middle;
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

table td img {
    border-radius: 50%;
    border: 2px solid #ddd;
    height: 48px;
    width: 48px;
    object-fit: cover;
}

table td a {
    color: #3498db;
    text-decoration: none;
    font-weight: 500;
    margin: 0 5px;
}

table td a:hover {
    text-decoration: underline;
}
</style>

<table>
    <tr>
        <th>ID</th><th>Ảnh</th><th>Họ tên</th><th>Email</th><th>Phone</th><th>Lớp</th><th>Hành động</th>
    </tr>
    <?php while($row = $res->fetch_assoc()): ?>
    <tr>
        <td><?= esc($row['id']) ?></td>
        <td><?php if($row['photo']): ?><img src="/<?= esc($row['photo']) ?>" style="height:48px"><?php endif; ?></td>
        <td><?= esc($row['name']) ?></td>
        <td><?= esc($row['email']) ?></td>
        <td><?= esc($row['phone']) ?></td>
        <td><?= esc($row['class_name']) ?></td>
        <td>
            <a href="?url=student/edit&id=<?= $row['id'] ?>">Sửa</a> | <a href="?url=student/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
