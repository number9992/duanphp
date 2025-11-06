<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $photo = uploadImage($_FILES['photo'] ?? null);

    if ($name) {
        $stmt = $conn->prepare("INSERT INTO teachers (name,email,phone,department,photo) VALUES (?,?,?,?,?)");
        $stmt->bind_param('sssss',$name,$email,$phone,$department,$photo);
        if ($stmt->execute()) {
            header('Location:?url=teacher');
            exit;
        } else $err = $stmt->error;
    } else $err = "Tên bắt buộc.";
}

include __DIR__ . '/../../includes/header.php';
?>
<style>body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background-color: #f7f9fc;
    margin: 0;
    padding: 0;
    color: #333;
}

h2 {
    text-align: center;
    margin: 30px 0;
    font-size: 28px;
    color: #2c3e50;
}

form {
    max-width: 500px;
    margin: 0 auto;
    background-color: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.form-row {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.form-row label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #34495e;
}

.form-row input[type="text"],
.form-row input[type="email"],
.form-row input[type="file"] {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.form-row input:focus {
    border-color: #27ae60;
    outline: none;
}

button.btn {
    display: block;
    width: 100%;
    padding: 12px;
    background-color: #3498db;
    color: white;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button.btn:hover {
    background-color: #2980b9;
}

p[style="color:red"] {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
}
</style>
<h2>Thêm Giảng viên</h2>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <div class="form-row"><label>Họ tên</label><input name="name" type="text" required></div>
    <div class="form-row"><label>Email</label><input name="email" type="email"></div>
    <div class="form-row"><label>Phone</label><input name="phone" type="text"></div>
    <div class="form-row"><label>Khoa</label><input name="department" type="text"></div>
    <div class="form-row"><label>Ảnh</label><input name="photo" type="file"></div>
    <button class="btn">Lưu</button>
</form>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
