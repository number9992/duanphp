<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
include __DIR__ . '/../../includes/header.php';

$res = $conn->query("SELECT * FROM students ORDER BY id DESC");
?>
<style>body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f6f8;
    margin: 0;
    padding: 0;
    color: #333;
}

h2 {
    text-align: center;
    margin: 30px;
    color: #2c3e50;
}

a.btn {
    display: inline-block;
    margin: 20px 0 20px 0;
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

a.btn:hover {
    background-color: #3498db;
}

table {
    width: 100%;
    margin: 0 0 40px 0;
    /* use separate so we can add rounded corners to the header */
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    background-color: white;
}

table th, table td {
    padding: 12px 15px;
    border-bottom: 1px solid #ddd;
    text-align: center;
}

table th {
    background-color: #215dc6ff; /* match other tables */
    color: white;
    font-weight: 600;
}

/* rounded corners for the table header cells */
table tr:first-child th:first-child {
    border-top-left-radius: 8px;
}
table tr:first-child th:last-child {
    border-top-right-radius: 8px;
}

table tr:hover {
    background-color: #f1f1f1;
}

img {
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.2);
}

a {
    color: #3498db;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
    color: #2980b9;
}

footer {
    text-align: center;
    padding: 20px;
    background-color: #f4f6f8;
    color: #2c3e50; 
    font-size: 14px;
}</style>
<h2>Danh sách Sinh viên</h2>

<a class="btn" href="?url=student/add">+ Thêm sinh viên</a>
<table>
    <tr><th>ID</th><th>Ảnh</th><th>Họ tên</th><th>Email</th><th>Phone</th><th>Lớp</th><th>Hành động</th></tr>
    <?php while($row = $res->fetch_assoc()): ?>
    <tr>
        <td><?= esc($row['id']) ?></td>
        <td><?php if($row['photo']): ?><img src="/<?= esc($row['photo']) ?>" alt="" style="height:48px"><?php endif; ?></td>
        <td><?= esc($row['name']) ?></td>
        <td><?= esc($row['email']) ?></td>
        <td><?= esc($row['phone']) ?></td>
        <td><?= esc($row['class']) ?></td>
        <td>
            <a href="?url=student/edit&id=<?= $row['id'] ?>">Sửa</a>
            <a href="?url=student/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa?')">Xóa</a>
        </td>
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
