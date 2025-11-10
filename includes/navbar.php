<?php
// includes/navbar.php
if (session_status() === PHP_SESSION_NONE) session_start();

// Lấy thông tin người dùng
$userName = $_SESSION['name'] ?? '';
$userRole = $_SESSION['role'] ?? '';
?>
<style>
  :root{
    --sidebar-width: 220px;
    --sidebar-collapsed-width: 60px;
    --navbar-height: 60px;
    --footer-height: 60px;
  }

  /* --- NAVBAR TOP --- */
  .navbar { display: flex; justify-content: space-between; align-items: center; background:#215dc6ff; padding: 15px 30px; color: #ecf0f1; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: fixed; top:0; left:0; right:0; z-index:1000; }
  .navbar a { color: #ecf0f1; text-decoration: none; font-weight:500; transition: color 0.3s ease; }
  .navbar a:hover { color: #1abc9c; }
  .brand { font-size: 20px; font-weight: bold; margin-right:20px; }
  .nav-right { display:flex; align-items:center; }
  .nav-right span { margin-right:15px; font-style:italic; }

  /* --- SIDEBAR LEFT --- */
  .sidebar { position: fixed; top:60px; left:0; height:100%; width:220px; background:#215dc6ff; padding-top:20px; transition: width 0.3s ease, left 0.3s ease; overflow:hidden; z-index:999; }
  .sidebar.collapsed { width:60px; }
  .sidebar a { display:flex; align-items:center; padding:12px 20px; color:#ecf0f1; text-decoration:none; transition: background 0.3s ease; white-space:nowrap; }
  .sidebar a:hover { background-color:#34495e; }
  .sidebar i { margin-right:10px; font-size:18px; width:20px; text-align:center; }
  .sidebar.collapsed a span { display:none; }

  /* Style cho từng icon cụ thể */
  .sidebar a:nth-child(1) svg { /* Dashboard */
    stroke-width: 0.5;
  }

  .sidebar a:nth-child(2) svg { /* Sinh viên */
    stroke-width: 0.3;
  }

  .sidebar a:nth-child(3) svg { /* Giảng viên */
    stroke-width: 0.3;
  }

  .sidebar a:nth-child(4) svg { /* Môn học */
    stroke-width: 0.5;
  }

  .sidebar a:nth-child(5) svg { /* Điểm */
    stroke-width: 0.3;
  }

  /* --- TOGGLE BUTTON --- */
  .toggle-btn { background:none; border:none; color:#ecf0f1; font-size:22px; cursor:pointer; margin-right:15px; }

  /* --- MAIN CONTENT --- */
  #main-content { margin-top:60px; margin-left:220px; padding:20px; min-height:calc(100vh - 60px); transition: all 0.3s ease; background:#f8f9fa; }
  .sidebar.collapsed ~ #main-content { margin-left:60px; }

  @media screen and (min-width:768px) {
    .wrapper { display:flex; min-height:100vh; }
    #main-content { flex:1; width:calc(100% - 220px); }
    .sidebar.collapsed ~ #main-content { width:calc(100% - 60px); }
  }
</style>

<div class="page-container">
  <nav class="navbar">
    <div style="display:flex; align-items:center;">
      <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
      <span class="brand">QL Sinh viên</span>
    </div>
    <div class="nav-right">
      <?php if($userName): ?>
        <span>Xin chào, <?= htmlspecialchars($userName) ?></span>
        <a href="auth/logout.php">Đăng xuất</a>
      <?php else: ?>
        <a href="?url=register">Đăng ký</a>
        <a href="?url=login">Đăng nhập</a>
      <?php endif; ?>
    </div>
  </nav>

  <div class="wrapper">
    <div class="sidebar" id="sidebar">
      <a href="?url=dashboard"><i>🏠</i><span>Dashboard</span></a>

      <?php if($userRole === 'teacher'): ?>
        <a href="?url=student"><i>🎓</i><span>Sinh viên</span></a>
        <a href="?url=courses"><i>📘</i><span>Môn học</span></a>
        <!-- <a href="?url=grades"><i>📊</i><span>Điểm</span></a> -->
        <a href="?url=subjects"><i>📊</i><span>Quản lý môn học</span></a>
      <?php elseif($userRole === 'admin'): ?>
        <a href="?url=student"><i>🎓</i><span>Sinh viên</span></a>
        <a href="?url=register"><i>👨‍🏫</i><span>thêm người quản lý </span></a>
        <!-- <a href="?url=courses"><i>📘</i><span>Môn học</span></a> -->
        <!-- <a href="?url=grades"><i>📊</i><span>Điểm</span></a> -->
        <a href="?url=class"><i>📊</i><span>Quản lý lớp học phần</span></a>
        <a href="?url=subjects"><i>📊</i><span>Quản lý môn học</span></a>
        <!-- <a href="?url=class_subjects"><i>👨‍🏫</i><span>Phân môn cho lớp</span></a> -->
        <a href="?url=timetables"><i>📊</i><span>Thời khóa biểu</span></a>
      <?php endif; ?>
    </div>

    <div id="main-content">
      <div class="content-wrapper">
        <script>
          function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
          }
        </script>
