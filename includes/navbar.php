<?php
// includes/navbar.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<style>
  :root{
    --sidebar-width: 220px;
    --sidebar-collapsed-width: 60px;
    --navbar-height: 60px;
    --footer-height: 60px;
  }

  /* --- NAVBAR TOP --- */
  .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background:#215dc6ff;
    padding: 10px;
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
   color: #f58d1fff;
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
    top: var(--navbar-height); /* dưới thanh navbar */
    left: 0;
    height: calc(100% - var(--navbar-height));
    width: var(--sidebar-width);
    background:#215dc6ff;
    padding-top: 20px;
    transition: width 0.25s ease, left 0.25s ease;
    overflow: hidden;
    z-index: 999;
    box-sizing: border-box;
  }

  .sidebar.collapsed {
    width: var(--sidebar-collapsed-width);
  }

  .sidebar a {
    display: flex;
    align-items: center; /* center vertically */
    padding: 10px 18px;
    color: #ecf0f1;
    text-decoration: none;
    transition: background-color 0.25s ease, padding-left 0.25s ease;
    white-space: nowrap;
    border-left: 3px solid transparent;
    height: 48px;
    box-sizing: border-box;
  }

  .sidebar a:hover {
    /* background-color: rgba(148, 250, 247, 0.85); */
    color: #f58d1fff;
    border-left: 3px solid #4CAF50;
    padding-left: 22px;
  }

  .sidebar a span {
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 0.3px;
    opacity: 0.95;
    line-height: 24px;
    height: 24px;
  }

  .sidebar svg {
    width: 20px;
    height: 20px;
    margin-right: 12px;
    fill: currentColor;
    opacity: 0.95;
    transition: transform 0.2s ease, opacity 0.2s ease;
    flex-shrink: 0;
  }

  .sidebar a:hover svg {
    transform: scale(1.1);
    opacity: 1;
  }

  .sidebar.collapsed a span {
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
  .toggle-btn {
    background: none;
    border: none;
    color: #ecf0f1;
    font-size: 22px;
    cursor: pointer;
    margin-right: 15px;
  }

  /* --- MAIN CONTENT --- */
  #main-content {
    margin-top: var(--navbar-height);
    margin-left: var(--sidebar-width);
    margin-bottom: var(--footer-height);
    padding: 28px 24px;
    min-height: calc(100vh - var(--navbar-height) - var(--footer-height));
    transition: margin-left 0.25s ease, width 0.25s ease, padding 0.25s ease;
    background: #f8f9fa;
    box-sizing: border-box;
    width: calc(100% - var(--sidebar-width));
  }

  /* When sidebar is collapsed reduce left margin and expand available width */
  .sidebar.collapsed ~ #main-content {
    margin-left: var(--sidebar-collapsed-width);
    width: calc(100% - var(--sidebar-collapsed-width));
  }

  /* Ensure content inside main-content is centered and responsive */
  .content-wrapper {
    max-width: none;
    margin: 0 auto;
    box-sizing: border-box;
  }
  /* Allow certain pages to expand full-width (add class 'wide' on .content-wrapper) */
  .content-wrapper.wide {
    max-width: none;
    width: 100%;
    margin: 0;
    box-sizing: border-box;
  }
</style>

<div class="page-container">
  <nav class="navbar">
    <div style="display: flex; align-items: center;">
      <img src="asset/img/logo.png" alt="Logo" style="height:40px; margin-right:10px;">
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

  <div class="wrapper">
    <div class="sidebar" id="sidebar">
      <a href="?url=dashboard"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M341.8 72.6C329.5 61.2 310.5 61.2 298.3 72.6L74.3 280.6C64.7 289.6 61.5 303.5 66.3 315.7C71.1 327.9 82.8 336 96 336L112 336L112 512C112 547.3 140.7 576 176 576L464 576C499.3 576 528 547.3 528 512L528 336L544 336C557.2 336 569 327.9 573.8 315.7C578.6 303.5 575.4 289.5 565.8 280.6L341.8 72.6zM264 320C264 289.1 289.1 264 320 264C350.9 264 376 289.1 376 320C376 350.9 350.9 376 320 376C289.1 376 264 350.9 264 320zM208 496C208 451.8 243.8 416 288 416L352 416C396.2 416 432 451.8 432 496C432 504.8 424.8 512 416 512L224 512C215.2 512 208 504.8 208 496z"/></svg><span>Dashboard</span></a>
      <a href="?url=student"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 248a120 120 0 1 0 0-240 120 120 0 1 0 0 240zm-29.7 56C95.8 304 16 383.8 16 482.3 16 498.7 29.3 512 45.7 512l356.6 0c16.4 0 29.7-13.3 29.7-29.7 0-98.5-79.8-178.3-178.3-178.3l-59.4 0z"/></svg><span>Sinh viên</span></a>
      <a href="?url=teacher"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M320 312C253.7 312 200 258.3 200 192C200 125.7 253.7 72 320 72C386.3 72 440 125.7 440 192C440 258.3 386.3 312 320 312zM289.5 368L350.5 368C360.2 368 368 375.8 368 385.5C368 389.7 366.5 393.7 363.8 396.9L336.4 428.9L367.4 544L368 544L402.6 405.5C404.8 396.8 413.7 391.5 422.1 394.7C484 418.3 528 478.3 528 548.5C528 563.6 515.7 575.9 500.6 575.9L139.4 576C124.3 576 112 563.7 112 548.6C112 478.4 156 418.4 217.9 394.8C226.3 391.6 235.2 396.9 237.4 405.6L272 544.1L272.6 544.1L303.6 429L276.2 397C273.5 393.8 272 389.8 272 385.6C272 375.9 279.8 368.1 289.5 368.1z"/></svg><span>Giảng viên</span></a>
      <a href="?url=courses"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M384 512L96 512c-53 0-96-43-96-96L0 96C0 43 43 0 96 0L400 0c26.5 0 48 21.5 48 48l0 288c0 20.9-13.4 38.7-32 45.3l0 66.7c17.7 0 32 14.3 32 32s-14.3 32-32 32l-32 0zM96 384c-17.7 0-32 14.3-32 32s14.3 32 32 32l256 0 0-64-256 0zm32-232c0 13.3 10.7 24 24 24l176 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-176 0c-13.3 0-24 10.7-24 24zm24 72c-13.3 0-24 10.7-24 24s10.7 24 24 24l176 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-176 0z"/></svg><span>Môn học</span></a>
      <a href="?url=scores"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M309.5-18.9c-4.1-8-12.4-13.1-21.4-13.1s-17.3 5.1-21.4 13.1L193.1 125.3 33.2 150.7c-8.9 1.4-16.3 7.7-19.1 16.3s-.5 18 5.8 24.4l114.4 114.5-25.2 159.9c-1.4 8.9 2.3 17.9 9.6 23.2s16.9 6.1 25 2L288.1 417.6 432.4 491c8 4.1 17.7 3.3 25-2s11-14.2 9.6-23.2L441.7 305.9 556.1 191.4c6.4-6.4 8.6-15.8 5.8-24.4s-10.1-14.9-19.1-16.3L383 125.3 309.5-18.9z"/></svg><span>Điểm</span></a>
    </div>
    
    <div id="main-content">
      <div class="content-wrapper">
<script>
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('collapsed');
  }
</script>
