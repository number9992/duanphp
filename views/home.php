<?php
include_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Quản lý Sinh viên</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');

        body {
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
            background: url('./asset/img/bg-login.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        @keyframes gradientMove {
            0% {
                transform: translate(0, 0) scale(1);
            }
            50% {
                transform: translate(-5%, 5%) scale(1.1);
            }
            100% {
                transform: translate(0, 0) scale(1);
            }
        }

        .wave {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,192C672,181,768,139,864,128C960,117,1056,139,1152,149.3C1248,160,1344,160,1392,160L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') repeat-x;
            background-size: 1440px 100px;
            animation: wave 10s linear infinite;
            z-index: 1;
        }

        .wave:nth-child(2) {
            bottom: 10px;
            opacity: 0.5;
            animation: wave 7s linear infinite;
        }

        .wave:nth-child(3) {
            bottom: 20px;
            opacity: 0.2;
            animation: wave 5s linear infinite;
        }

        @keyframes wave {
            0% {
                background-position-x: 0;
            }
            100% {
                background-position-x: 1440px;
            }
        }

        .welcome-container {
            text-align: center;
            color: white;
            margin-bottom: 40px;
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 800px;
            width: 100%;
        }

        .welcome-container h1 {
            font-size: 2.8em;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-container p {
            font-size: 1.4em;
            opacity: 0.9;
            margin-bottom: 40px;
            font-weight: 400;
        }

        .auth-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }

        .auth-button {
            padding: 16px 48px;
            border-radius: 12px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            border: none;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        .login-btn {
            background: #ffffff;
            color: #0b4182;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                120deg,
                transparent,
                rgba(255, 255, 255, 0.4),
                transparent
            );
            transition: 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            background-color: #ffffff;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .register-btn {
            background-color: transparent;
            color: white;
            border: 2px solid rgba(255,255,255,0.8);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .register-btn:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            border-color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <h1>Chào mừng đến với Hệ thống Quản lý</h1>
        <p>Sinh viên - Giảng viên</p>
        
        <div class="auth-buttons">
            <a href="?url=login" class="auth-button login-btn">Đăng nhập</a>
            <a href="?url=register" class="auth-button register-btn">Đăng ký</a>
        </div>
    </div>
</body>
</html>
