<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: list.php'); exit; }

// Lấy thông tin sinh viên hiện tại
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if (!$row) { header('Location:list.php'); exit; }

// Lấy danh sách lớp để chọn
$classesRes = $conn->query("SELECT id, class_name FROM classes ORDER BY grade_level, class_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    // Lấy class_id từ form SELECT
    $class_id = intval($_POST['class_id'] ?? 0); 
    
    $photo = uploadImage($_FILES['photo'] ?? null) ?? $row['photo'];

    if ($name && $class_id > 0) {
        
        // TRUY VẤN: Cập nhật cột class_id (integer)
        $u = $conn->prepare("UPDATE students SET name=?, email=?, phone=?, class_id=?, photo=? WHERE id=?");
        
        // SỬA LỖI BIND_PARAM: 's' 's' 's' 'i' 's' 'i' (name, email, phone, class_id, photo, id)
        $u->bind_param('sssisi', $name, $email, $phone, $class_id, $photo, $id); 
        
        if ($u->execute()) {
            header('Location: ?url=student');
            exit;
        } else $err = $u->error;
    } else $err = "Họ tên và Lớp là bắt buộc.";
}

include __DIR__ . '/../../includes/header.php';
?>
<style>
/* Tối ưu CSS cho UX/UI */
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background-color: #f0f2f5;
    color: #333;
}

h2 {
    text-align: center;
    margin: 30px 0;
    color: #2c3e50;
    font-size: 28px;
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
    display: inline-block;
    width: 100%;
}

form {
    max-width: 800px;
    margin: 0 auto;
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

/* Bố cục 2 cột */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr; 
    gap: 20px 30px; 
    margin-bottom: 20px;
}

.form-row {
    display: flex;
    flex-direction: column;
    margin-bottom: 0; 
}

/* Thiết lập các trường nhập liệu */
.form-row label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #34495e;
    font-size: 14px;
}

/* SỬA LỖI ĐỒNG NHẤT CHIỀU CAO INPUT/SELECT */
.form-row input[type="text"],
.form-row input[type="email"],
.form-row input[type="file"],
.form-row select { 
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
    background-color: #f9f9f9;
    height: 44px; /* Đặt chiều cao cố định để chúng đồng nhất */
    box-sizing: border-box; /* Đảm bảo padding không làm tăng chiều cao */
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

/* Riêng input file cần reset chiều cao để không bị lệch nút chọn */
.form-row input[type="file"] {
    height: auto; 
    padding: 8px 12px; /* Giảm padding file input để nút 'Choose File' vừa vặn */
}

.form-row input:focus, .form-row select:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
    outline: none;
}

.full-width {
    grid-column: 1 / -1; 
    margin-top: 10px;
}

button.btn {
    display: block;
    width: 100%;
    padding: 14px;
    background-color: #27ae60; 
    color: white;
    font-size: 18px;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button.btn:hover {
    background-color: #229954;
}

p[style="color:red"] {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
}

.current-photo-container {
    text-align: center;
    padding: 15px;
    border: 1px dashed #ccc;
    border-radius: 8px;
    margin-top: 10px;
}

.current-photo-container img {
    display: inline-block;
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #eee;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
}
</style>

<h2>Sửa Sinh viên: <?= esc($row['name'] ?? '...') ?></h2>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
    
    <div class="form-grid">
        <div class="form-row"><label>Họ tên</label><input name="name" required value="<?= esc($row['name'] ?? '') ?>"></div>
        
        <div class="form-row"><label>Email</label><input name="email" type="email" value="<?= esc($row['email'] ?? '') ?>"></div>
        
        <div class="form-row"><label>Phone</label><input name="phone" value="<?= esc($row['phone'] ?? '') ?>"></div>
        
        <div class="form-row">
            <label>Lớp (*)</label>
            <select name="class_id" required>
                <option value="">-- Chọn lớp --</option>
                <?php 
                $classesRes->data_seek(0); 
                while($c = $classesRes->fetch_assoc()): 
                ?>
                    <option value="<?= $c['id'] ?>" <?= ($c['id'] == ($row['class_id'] ?? 0)) ? 'selected' : '' ?>>
                        <?= esc($c['class_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
    
    <div class="form-row full-width">
        <label>Ảnh đại diện (để trống nếu không đổi)</label>
        <input name="photo" type="file" accept="image/*">
    </div>
    
    <?php if($row['photo']): ?>
    <div class="current-photo-container full-width">
        <img src="/<?= esc($row['photo']) ?>" alt="Ảnh hiện tại">
        <p style="margin-top: 5px; font-style: italic; font-size: 13px; color: #6c757d;">Ảnh hiện tại</p>
    </div>
    <?php endif; ?>
    
    <button class="btn">Cập nhật thông tin</button>
</form>