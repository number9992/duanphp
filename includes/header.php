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

    body {
      font-family: 'Segoe UI', Tahoma, sans-serif;
      background: url('/public/images/bg.jpg') no-repeat center center fixed;
      background-size: cover;
      color: #333;
      line-height: 1.6;
    }

    /* ======= Navbar ======= */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: rgba(44, 62, 80, 0.95);
      padding: 15px 30px;
      color: #ecf0f1;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

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

    /* ======= Container ======= */
    .container {
      max-width: 1200px;
      margin: 60px auto;
      padding: 30px;
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .container h1 {
      color: #2c3e50;
      text-align: center;
      margin-bottom: 10px;
    }

    .container p {
      text-align: center;
      font-style: italic;
      color: #555;
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<main class="container">