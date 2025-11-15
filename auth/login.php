<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        // Lấy user và role_name từ bảng roles
        $stmt = $conn->prepare("
            SELECT u.id, u.username, u.password, u.name, r.role_name 
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.username = ? 
            LIMIT 1
        ");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {
            $hash = $user['password'];

            if (password_verify($password, $hash)) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role_name']; // Lấy từ roles
                $_SESSION['name'] = $user['name'];

                // Chuyển hướng theo role
                switch($user['role_name']) {
                    case 'teacher':
                        header('Location: ?url=giang_vien');
                        break;
                    case 'student':
                        header('Location: ?url=sinhvien');
                        break;
                    case 'admin':
                    default:
                        header('Location: ?url=dashboard');
                        break;
                }
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
?>




<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('asset/img/bg-login.jpg') center/cover no-repeat;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* background: url(/duanphp/asset/img/login-bg.jpg) ; */
            z-index: 1;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 60px;
            width: 600px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }

        .login-container:before {
            position: absolute;
            background: url(/duanphp/asset/img/login-line-1.png) top center no-repeat;
            height: 89px;
            width: 274px;
            top: -20px;
            right: -12px;
            z-index: 1;
            content: "";
        }

        .logo-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #4a90e2;
        }

        .paper-plane svg {
            width: 60px;
            height: 60px;
            fill: #4a90e2;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 30px;
            letter-spacing: 1px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group input {
            width: 100%;
            padding: 15px 45px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .input-group input:focus {
            outline: none;
            border-color: #4a90e2;
            background: white;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 18px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            cursor: pointer;
            font-size: 18px;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(74, 144, 226, 0.3);
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: #4a90e2;
            text-decoration: none;
            font-size: 14px;
        }

        .error-message {
            background: #ffe6e6;
            color: #d32f2f;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #ffcdd2;
        }

        .decorative-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 70%;
            right: 10%;
            animation: float 8s ease-in-out infinite reverse;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation: float 7s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .header-login {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 600px;
            margin-bottom: 20px;
        }

        .university-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: white;
            z-index: 3;
            margin-bottom: 20px;
            width: 100%;
        }

        .university-logo {
            width: auto;
            height: 90px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Header với logo trường -->
    <div class="decorative-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <div class="header-login">
        <div class="university-header">
            <img src="asset/img/logologin.png" alt="Logo trường" class="university-logo">
        </div>
    <div class="login-container">
        <h2>ĐĂNG NHẬP</h2>
        
        <?php if(isset($err)): ?>
            <div class="error-message"><?= esc($err) ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="input-group">
                <span class="input-icon">👤</span>
                <input type="text" name="username" placeholder="Tên đăng nhập" required>
            </div>
            
            <div class="input-group">
                <span class="input-icon">🔐</span>
                <input type="password" name="password" placeholder="Mật khẩu" required id="password">
                <span class="password-toggle" onclick="togglePassword()">👁️</span>
            </div>
            
            <button type="submit" class="login-btn">ĐĂNG NHẬP</button>
        </form>
        
        <div class="forgot-password">
            <a href="#">Quên mật khẩu?</a> • <a href="#">Trợ giúp!</a>
            <a href="?url=register">đăng ký</a>
        </div>
    </div>

    </div>

 

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggle = document.querySelector('.password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggle.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggle.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
