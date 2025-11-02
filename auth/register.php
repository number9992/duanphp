<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $role = $_POST['role'] ?? 'admin';

    if ($username && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username,password,role,name) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss',$username,$hash,$role,$name);
        if ($stmt->execute()) {
            $msg = "Đăng ký thành công.";
        } else {
            $err = "Lỗi: " . $stmt->error;
        }
    } else {
        $err = "Username & password bắt buộc.";
    }
}
include __DIR__ . '/../includes/header.php';
?>
<h2>Đăng ký user mới</h2>
<style>/* register.css */

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f2f5;
    margin: 0;
    padding: 0;
}

h2 {
    text-align: center;
    margin-top: 40px;
    color: #333;
}

form {
    max-width: 450px;
    margin: 30px auto;
    padding: 25px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.form-row {
    margin-bottom: 20px;
}

.form-row label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #555;
}

.form-row input,
.form-row select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
    font-size: 14px;
}

.btn {
    width: 100%;
    padding: 12px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #218838;
}

.notice {
    text-align: center;
    color: #28a745;
    font-weight: bold;
    margin-top: 10px;
}

p[style="color:red"] {
    text-align: center;
    font-weight: bold;
    margin-top: 10px;
}</style>
<?php if(isset($msg)): ?><p class="notice"><?= esc($msg) ?></p><?php endif; ?>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>
<form method="post">
    <div class="form-row">
        <label>Username</label><input name="username" required>
    </div>
    <div class="form-row">
        <label>Password</label><input name="password" type="password" required>
    </div>
    <div class="form-row">
        <label>Tên</label><input name="name">
    </div>
    <div class="form-row">
        <label>Role</label>
        <select name="role">
            <option value="admin">admin</option>
            <option value="teacher">teacher</option>
        </select>
    </div>
    <button class="btn">Tạo user</button>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
