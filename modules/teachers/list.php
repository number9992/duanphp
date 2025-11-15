<?php
// Lấy thông tin sinh viên để tránh bị lỗi cú pháp khi gộp 2 file
require_once __DIR__ . '/../../config/db.php'; 
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

include __DIR__ . '/../../includes/header.php';

// Đường dẫn đến ảnh mặc định (Đường dẫn tương đối Web)
$default_avatar = 'public/uploads/default_avatar.png'; 

// Lấy danh sách giáo viên từ DB mới (Đã thêm teacher_code)
$sql = "
    SELECT 
        id, 
        name, 
        teacher_code,
        email, 
        phone, 
        department, 
        photo 
    FROM teachers 
    ORDER BY id DESC
";
$res = $conn->query($sql);
?>

<style>
/* Tối ưu CSS cho UX/UI - Đồng nhất với bảng sinh viên */
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
    width: 100%;
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
table th:nth-child(2), table td:nth-child(2), /* Ảnh */
table th:nth-child(8), table td:nth-child(8) /* Hành động */
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

/* Ảnh: CHUẨN UX/UI (48x48, tròn) */
table img {
    height: 48px;
    width: 48px;
    object-fit: cover; 
    border-radius: 50%; 
    border: 2px solid #ecf0f1;
    display: block; 
    margin: 0 auto; 
}

/* Hành động (Actions) */
table td a {
    margin: 0 5px;
    text-decoration: none;
    font-weight: 500;
    padding: 4px 8px;
    border-radius: 3px;
    transition: background-color 0.2s;
    font-size: 13px;
}

table td a[href*="edit"] {
    color: #2980b9;
    border: 1px solid #3498db;
}

table td a[href*="delete"] {
    color: #c0392b;
    border: 1px solid #e74c3c;
}

table td a:hover {
    background-color: rgba(52, 152, 219, 0.1);
}
</style>

<div class="header-actions">
    <h2>Danh sách Giảng viên</h2>
    <a class="btn" href="?url=teacher/add">+ Thêm giảng viên</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Ảnh</th>
            <th>Mã giảng viên</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Khoa</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = $res->fetch_assoc()): 
        
        $photo_db = $row['photo'] ?? ''; // Lấy đường dẫn ảnh từ DB
        
        // LOGIC ẢNH: Sử dụng đường dẫn tương đối Web (Đã sửa lỗi XAMPP)
        if ($photo_db) {
            $image_src = ltrim(esc($photo_db), '/'); 
        } else {
            $image_src = $default_avatar;
        }
    ?>
    <tr>
        <td><?= esc($row['id']) ?></td>
        <td>
            <img src="<?= $image_src ?>" alt="Ảnh GV">
        </td>
        
        <td><?= esc($row['teacher_code'] ?? 'N/A') ?></td> 
        <td><strong><?= esc($row['name']) ?></strong></td>
        <td><?= esc($row['email'] ?? '') ?></td>
        <td><?= esc($row['phone'] ?? '') ?></td>
        <td><?= esc($row['department'] ?? 'N/A') ?></td>
        <td>
            <a href="?url=teacher/edit&id=<?= $row['id'] ?>">Sửa</a> 
            <a href="?url=teacher/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa giáo viên này?')">Xóa</a>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>
