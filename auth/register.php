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
?>
<style>/* register.css using bg-login */

*{box-sizing:border-box;margin:0;padding:0}

body{
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('/duanphp/asset/img/bg-login.jpg') center/cover no-repeat;
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    position:relative;
}

/* translucent centered card like login */
.register-container{
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(8px);
    border-radius: 16px;
    padding: 48px;
    width: 600px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

h2{ 
    text-align:center;
     color:#2c3e50; 
     margin-bottom:16px; 
     font-size:24px 
    }

.form-row{ 
    margin-bottom:16px 
}
.form-row label{ 
    display:block; 
    margin-bottom:6px; 
    font-weight:600; 
    color:#555 
}
.form-row input, .form-row select{ 
    width:100%; 
    padding:12px 14px; 
    border:1px solid #e1e5e9; 
    border-radius:10px; 
    font-size:14px; 
    background:#f8f9fa 
}

.btn{ 
    width:100%; 
    padding:14px; 
    background:linear-gradient(135deg,#4a90e2 0%,#357abd 100%); 
    color:#fff; 
    border:none; 
    border-radius:12px; 
    font-weight:700; 
    cursor:pointer 
}
.btn:hover{ 
    transform:translateY(-2px); 
    box-shadow:0 10px 20px rgba(74,144,226,0.2) 
}

.notice{ 
    text-align:center; 
    color:#28a745; 
    font-weight:700; 
    margin-top:10px 
}
p[style="color:red"]{ 
    text-align:center; 
    font-weight:700; 
    margin-top:10px 
}
</style>
<div class="register-container">
    <h2>Đăng ký user mới</h2>
    <?php if(isset($msg)): ?><p class="notice"><?= esc($msg) ?></p><?php endif; ?>
    <?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>
    <form method="post">
        <div class="form-row">
            <label>Username</label>
            <input name="username" required />
        </div>
        <div class="form-row">
            <label>Password</label>
            <input name="password" type="password" required />
        </div>
        <div class="form-row">
            <label>Tên</label>
            <input name="name" />
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
</div>
