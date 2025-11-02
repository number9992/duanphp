<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $conn->prepare("SELECT id, password, role, name FROM users WHERE username = ?");
        $stmt->bind_param('s',$username);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $hash = $row['password'];
            // If hash is SHA2 from SQL migration, handle both:
            if (password_verify($password, $hash) || hash('sha256',$password) === $hash) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'] ?? $username;
                $_SESSION['user_role'] = $row['role'];
               header('Location: ?url=dashboard');
                exit;
            } else {
                $err = "Sai username hoặc mật khẩu.";
            }
        } else {
            $err = "Sai username hoặc mật khẩu.";
        }
    } else {
        $err = "Nhập username và mật khẩu.";
    }
}
include __DIR__ . '/../includes/header.php';
?>
<h2>Đăng nhập</h2>
<style>
    /* login.css */

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

h2 {
    text-align: center;
    margin-top: 40px;
    color: #333;
}

form {
    max-width: 400px;
    margin: 30px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.form-row {
    margin-bottom: 15px;
}

.form-row label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
}

.form-row input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

.btn {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
}

.btn:hover {
    background-color: #0056b3;
}

p[style="color:red"] {
    text-align: center;
    font-weight: bold;
    margin-top: 10px;
}
</style>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>
<form method="post">
    <div class="form-row"><label>Username</label><input name="username" required></div>
    <div class="form-row"><label>Password</label><input name="password" type="password" required></div>
    <button class="btn">Đăng nhập</button>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
