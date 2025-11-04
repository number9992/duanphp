<?php
// includes/navbar.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<style>
  /* --- NAVBAR TOP --- */
  .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(180deg, #072f6fff 0%, #215dc6ff 100%);
    padding: 15px 30px;
    color: #ecf0f1;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
  }

  .navbar a {
    color: #ecf0f1;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
  }

  .navbar a:hover {
    color: #1abc9c;
  }

  .brand {
    font-size: 20px;
    font-weight: bold;
    margin-right: 20px;
  }

  .nav-right {
    display: flex;
    align-items: center;
  }

  .nav-right span {
    margin-right: 15px;
    font-style: italic;
  }

  /* --- SIDEBAR LEFT --- */
  .sidebar {
    position: fixed;
    top: 60px; /* dưới thanh navbar */
    left: 0;
    height: 100%;
    width: 220px;
    background: linear-gradient(180deg, #072f6fff 0%, #215dc6ff 100%);
    padding-top: 20px;
    transition: width 0.3s ease, left 0.3s ease;
    overflow: hidden;
    z-index: 999;
  }

  .sidebar.collapsed {
    width: 60px;
  }

  .sidebar a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #ecf0f1;
    text-decoration: none;
    transition: background 0.3s ease;
    white-space: nowrap;
  }

  .sidebar a:hover {
    background-color: #34495e;
  }

  .sidebar i {
    margin-right: 10px;
    font-size: 18px;
    width: 20px;
    text-align: center;
  }

  .sidebar.collapsed a span {
    display: none;
  }

  /* --- TOGGLE BUTTON --- */
  .toggle-btn {
    background: none;
    border: none;
    color: #ecf0f1;
    font-size: 22px;
    cursor: pointer;
    margin-right: 15px;
  }

  /* --- MAIN CONTENT --- */
  .main-content {
    margin-top: 60px;
    margin-left: 220px;
    padding: 20px;
    transition: margin-left 0.3s ease;
  }

  .sidebar.collapsed ~ .main-content {
    margin-left: 60px;
  }
</style>

<nav class="navbar">
  <div style="display: flex; align-items: center;">
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    <span class="brand">QL Sinh viên</span>
  </div>
  <div class="nav-right">
    <?php if (isset($_SESSION['user_id'])): ?>
      <span>Xin chào, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
      <a href="auth/logout.php">Đăng xuất</a>
    <?php else: ?>
      <a href="?url=register">Đăng ký</a>
      <a href="?url=login">Đăng nhập</a>
    <?php endif; ?>
  </div>
</nav>

<div class="sidebar" id="sidebar">
  <a href="?url=dashboard"><i>🏠</i><span>Dashboard</span></a>
  <a href="?url=student"><i>🎓</i><span>Sinh viên</span></a>
  <a href="?url=teacher"><i>👨‍🏫</i><span>Giảng viên</span></a>
  <a href="?url=courses"><i>📘</i><span>Môn học</span></a>
  <a href="?url=scores"><i>📊</i><span>Điểm</span></a>
</div>

<script>
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('collapsed');
  }
</script>
