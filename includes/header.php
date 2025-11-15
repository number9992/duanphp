<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Quản lý Sinh viên - Giảng viên</title>

    <style>
        /* ======= Reset & Base ======= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* make page a column flex so footer can stick to bottom */
        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            /* Giữ nguyên background */
            background: url('/public/images/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            line-height: 1.6;
        }

        /* ensure main area expands to fill available space */
        main.container {
            flex: 1;
            /* 1. TĂNG MAX-WIDTH HOẶC XÓA ĐỂ NỘI DUNG RỘNG HƠN */
            max-width: 1400px; /* Tăng từ 1200px lên 1400px */
            width: 95%; /* Đảm bảo nó không quá hẹp trên màn hình nhỏ */
            
            /* 2. CHỈNH MARGIN ĐỂ KHÔNG BỊ TRỐNG BÊN TRÁI 
               30px auto: Căn giữa nội dung chính */
            margin: 30px auto; 
            
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.95); /* Tăng độ trong suốt */
            border-radius: 12px; /* Góc bo đẹp hơn */
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15); /* Bóng đổ mạnh hơn */
            box-sizing: border-box;
            /* Xóa transition vì không còn sidebar cố định */
            /* transition: margin-left 0.3s ease; */ 
        }

        main.container h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 10px;
        }

        main.container p {
            text-align: center;
            font-style: italic;
            color: #555;
        }

        /* ======= Navbar ======= */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* Đổi màu Navbar cho hiện đại hơn */
            background-color: #34495e; 
            padding: 15px 30px;
            color: #ecf0f1;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
        }

        .navbar a {
            color: #ecf0f1;
            text-decoration: none;
            margin-right: 20px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .navbar a:hover {
            color: #3498db; /* Màu hover xanh dương đẹp mắt */
        }

        .navbar .brand {
            font-size: 20px;
            font-weight: bold;
            margin-right: 30px;
        }

        .nav-left, .nav-right {
            display: flex;
            align-items: center;
        }

        .nav-right span {
            margin-right: 15px;
            font-style: italic;
        }

        /* ======= Footer (match navbar color) ======= */
        footer.footer {
            /* Đồng bộ màu với Navbar mới */
            background: #34495e; 
            color: #fff;
            padding: 15px 0;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<main class="container">