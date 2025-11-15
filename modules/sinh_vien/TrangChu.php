<?php
// TrangChu.php

// Bắt đầu session
if (session_status() === PHP_SESSION_NONE) session_start();

// Yêu cầu đăng nhập (đảm bảo sinh viên đã đăng nhập)
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

// Chuyển hướng đến Dashboard chính của sinh viên
header("Location: ?url=sinhvien/dashboard_student");
exit;
?>