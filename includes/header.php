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
      background: url('/public/images/bg.jpg') no-repeat center center fixed;
      background-size: cover;
      color: #333;
      line-height: 1.6;
    }

    /* ensure main area expands to fill available space */
    main.container {
      flex: 1;
      /* max-width: 1200px; */
      margin: 30px 0 ;
      padding: 30px;
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      box-sizing: border-box;
      transition: margin-left 0.3s ease;
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
    .navbar a {
      color: #ecf0f1;
      text-decoration: none;
      margin-right: 20px;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .navbar a:hover {
      color: #1abc9c;
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
      background:#215dc6ff;
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