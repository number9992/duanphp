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
  .navbar { display: flex; 
    justify-content: space-between; 
    align-items: center; background:#215dc6ff; padding: 15px; color: #ecf0f1; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: fixed; top:0; left:0; right:0; z-index:1000; }
  .navbar a { color: #ecf0f1; text-decoration: none; font-weight:500; transition: color 0.3s ease; }
  .navbar a:hover { color: #1abc9c; }
  .brand { font-size: 20px; font-weight: bold; margin-right:20px; }
  .nav-right { display:flex; align-items:center; }
  .nav-right span { margin-right:15px; font-style:italic; }

  /* --- SIDEBAR LEFT --- */
  .sidebar {
    position: fixed;
    top: calc(var(--navbar-height));
    left: 0;
    height: calc(100% - var(--navbar-height));
    width: var(--sidebar-width);
    background: var(--sidebar-bg, #215dc6ff);
    padding-top: 20px;
    transition: width 0.25s ease;
    overflow: hidden;
    z-index: 999;
    box-sizing: border-box;
  }

  .sidebar.collapsed {
    width: var(--sidebar-collapsed-width);
  }

  .sidebar a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 18px;
    color: #ecf0f1;
    text-decoration: none;
    transition: background-color 0.2s ease, padding-left 0.2s ease;
    white-space: nowrap;
  }

  .sidebar a:hover {
    background-color: rgba(0,0,0,0.12);
    color: #fff;
  }

  .sidebar svg.icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    fill: currentColor;
    opacity: 0.95;
    transition: transform 0.15s ease, opacity 0.15s ease;
  }

  .sidebar a:hover svg.icon { transform: scale(1.08); opacity: 1; }

  .sidebar a span { font-size: 14px; font-weight: 500; }

  .sidebar.collapsed a span { display: none; }

  /* --- DROPDOWN MENU --- */
  .dropdown {
    position: relative;
  }
  
  .dropdown-toggle {
    position: relative;
    cursor: pointer;
  }
  
  .dropdown-toggle::after {
    content: '▼';
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 10px;
    transition: transform 0.2s ease;
  }
  
  .dropdown.active .dropdown-toggle::after {
    transform: translateY(-50%) rotate(180deg);
  }
  
  .dropdown-menu {
    max-height: 0;
    overflow: hidden;
    background: rgba(0,0,0,0.15);
    transition: max-height 0.25s ease;
  }
  
  .dropdown.active .dropdown-menu {
    max-height: 200px;
  }
  
  .dropdown-menu a {
    padding: 10px 15px 10px 30px;
    font-size: 13px;
    border-left: 3px solid transparent;
  }
  
  .dropdown-menu a:hover {
    background: rgba(0,0,0,0.2);
    border-left: 3px solid #1abc9c;
  }
  
  .sidebar.collapsed .dropdown-menu {
    display: none;
  }

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
      <img src="asset/img/logo.png" alt="Logo" style="height:30px; margin-right:10px;">
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
      <a href="?url=dashboard">
        <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M341.8 72.6C329.5 61.2 310.5 61.2 298.3 72.6L74.3 280.6C64.7 289.6 61.5 303.5 66.3 315.7C71.1 327.9 82.8 336 96 336L112 336L112 512C112 547.3 140.7 576 176 576L464 576C499.3 576 528 547.3 528 512L528 336L544 336C557.2 336 569 327.9 573.8 315.7C578.6 303.5 575.4 289.5 565.8 280.6L341.8 72.6zM264 320C264 289.1 289.1 264 320 264C350.9 264 376 289.1 376 320C376 350.9 350.9 376 320 376C289.1 376 264 350.9 264 320zM208 496C208 451.8 243.8 416 288 416L352 416C396.2 416 432 451.8 432 496C432 504.8 424.8 512 416 512L224 512C215.2 512 208 504.8 208 496z"/></svg>
        <span>Dashboard</span>
      </a>

      <?php if($userRole === 'teacher'): ?>
        <a href="?url=student">
          <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 248a120 120 0 1 0 0-240 120 120 0 1 0 0 240zm-29.7 56C95.8 304 16 383.8 16 482.3 16 498.7 29.3 512 45.7 512l356.6 0c16.4 0 29.7-13.3 29.7-29.7 0-98.5-79.8-178.3-178.3-178.3l-59.4 0z"/></svg>
          <span>Sinh viên</span>
        </a>
        <a href="?url=courses">
          <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M384 512L96 512c-53 0-96-43-96-96L0 96C0 43 43 0 96 0L400 0c26.5 0 48 21.5 48 48l0 288c0 20.9-13.4 38.7-32 45.3l0 66.7c17.7 0 32 14.3 32 32s-14.3 32-32 32l-32 0zM96 384c-17.7 0-32 14.3-32 32s14.3 32 32 32l256 0 0-64-256 0zm32-232c0 13.3 10.7 24 24 24l176 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-176 0c-13.3 0-24 10.7-24 24zm24 72c-13.3 0-24 10.7-24 24s10.7 24 24 24l176 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-176 0z"/></svg>
          <span>Môn học</span>
        </a>
        <a href="?url=subjects">
          <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M309.5-18.9c-4.1-8-12.4-13.1-21.4-13.1s-17.3 5.1-21.4 13.1L193.1 125.3 33.2 150.7c-8.9 1.4-16.3 7.7-19.1 16.3s-.5 18 5.8 24.4l114.4 114.5-25.2 159.9c-1.4 8.9 2.3 17.9 9.6 23.2s16.9 6.1 25 2L288.1 417.6 432.4 491c8 4.1 17.7 3.3 25-2s11-14.2 9.6-23.2L441.7 305.9 556.1 191.4c6.4-6.4 8.6-15.8 5.8-24.4s-10.1-14.9-19.1-16.3L383 125.3 309.5-18.9z"/></svg>
          <span>Quản lý môn học</span>
        </a>
      <?php elseif($userRole === 'admin'): ?>
        <a href="?url=student">
          <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 248a120 120 0 1 0 0-240 120 120 0 1 0 0 240zm-29.7 56C95.8 304 16 383.8 16 482.3 16 498.7 29.3 512 45.7 512l356.6 0c16.4 0 29.7-13.3 29.7-29.7 0-98.5-79.8-178.3-178.3-178.3l-59.4 0z"/></svg>
          <span>Sinh viên</span>
        </a>
        <a href="?url=register">
          <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M320 312C253.7 312 200 258.3 200 192C200 125.7 253.7 72 320 72C386.3 72 440 125.7 440 192C440 258.3 386.3 312 320 312zM289.5 368L350.5 368C360.2 368 368 375.8 368 385.5C368 389.7 366.5 393.7 363.8 396.9L336.4 428.9L367.4 544L368 544L402.6 405.5C404.8 396.8 413.7 391.5 422.1 394.7C484 418.3 528 478.3 528 548.5C528 563.6 515.7 575.9 500.6 575.9L139.4 576C124.3 576 112 563.7 112 548.6C112 478.4 156 418.4 217.9 394.8C226.3 391.6 235.2 396.9 237.4 405.6L272 544.1L272.6 544.1L303.6 429L276.2 397C273.5 393.8 272 389.8 272 385.6C272 375.9 279.8 368.1 289.5 368.1z"/></svg>
          <span>Thêm người quản lý</span>
        </a>
        
        <div class="dropdown">
          <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event)">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M309.5-18.9c-4.1-8-12.4-13.1-21.4-13.1s-17.3 5.1-21.4 13.1L193.1 125.3 33.2 150.7c-8.9 1.4-16.3 7.7-19.1 16.3s-.5 18 5.8 24.4l114.4 114.5-25.2 159.9c-1.4 8.9 2.3 17.9 9.6 23.2s16.9 6.1 25 2L288.1 417.6 432.4 491c8 4.1 17.7 3.3 25-2s11-14.2 9.6-23.2L441.7 305.9 556.1 191.4c6.4-6.4 8.6-15.8 5.8-24.4s-10.1-14.9-19.1-16.3L383 125.3 309.5-18.9z"/></svg>
            <span>Quản lý</span>
          </a>
          <div class="dropdown-menu">
            <a href="?url=class">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M219.3 .5c3.1-.6 6.3-.6 9.4 0l200 40c9.9 2 17.3 10.1 17.3 20.4l0 360c0 8.4-4.6 16.1-12.1 20.2L388.7 468c-10.1 5.8-22.5 5.8-32.6 0l-144-83.4c-3.1-1.8-7-1.8-10.1 0L57.9 468c-10.1 5.8-22.5 5.8-32.6 0L-19.9 441.1C-27.4 437-32 429.3-32 420.9L-32 60.9c0-10.3 7.4-18.4 17.3-20.4l200-40zM128 176c0-8.8 7.2-16 16-16l128 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L144 192c-8.8 0-16-7.2-16-16zm16 48l128 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L144 256c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>
              <span>Quản lý lớp học phần</span>
            </a>
            <a href="?url=subjects">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M64 448l384 0c35.3 0 64-28.7 64-64l0-240c0-35.3-28.7-64-64-64L298.7 80c-6.9 0-13.7-2.2-19.2-6.4L241.1 44.8C230 36.5 216.5 32 202.7 32L64 32C28.7 32 0 60.7 0 96L0 384c0 35.3 28.7 64 64 64z"/></svg>
              <span>Quản lý môn học</span>
            </a>
            <a href="?url=timetables">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M128 0c17.7 0 32 14.3 32 32l0 32 128 0 0-32c0-17.7 14.3-32 32-32s32 14.3 32 32l0 32 32 0c35.3 0 64 28.7 64 64l0 288c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 128C0 92.7 28.7 64 64 64l32 0 0-32c0-17.7 14.3-32 32-32zM64 240l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm128 0l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM64 368l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zm112 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16z"/></svg>
              <span>Thời khóa biểu</span>
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div id="main-content">
      <div class="content-wrapper">
        <script>
          function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
          }
          
          function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.target.closest('.dropdown');
            if (dropdown) {
              dropdown.classList.toggle('active');
            }
          }
        </script>
