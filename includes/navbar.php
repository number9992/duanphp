<?php
// includes/navbar.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<style>
  .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #2c3e50;
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
</style>

<nav class="navbar">
  <div class="nav-left">
    <a href="/index.php" class="brand">QL Sinh viên</a>
    <a href="?url=dashboard">Dashboard</a>
    <a href="?url=student">Sinh viên</a>
    <a href="?url=teacher">Giảng viên</a>
    <a href="?url=courses">Môn học</a>
    <a href="?url=scores">Điểm</a>
  </div>
  <div class="nav-right">
    <?php if (isset($_SESSION['user_id'])): ?>
      <span>Xin chào, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
      <a href="auth/logout.php">Đăng xuất</a>
      
    <?php else: ?>
        <a href="?url=register">đăng ký </a>
      <a href="?url=login">Đăng nhập</a>
    <?php endif; ?>
  </div>
</nav>