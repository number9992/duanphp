<?php
require_once 'includes/functions.php';
requireLogin();
include 'includes/header.php';
?>

<h2>Dashboard</h2>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
}

h2 {
    text-align: center;
    margin-top: 40px;
    color: #343a40;
    font-size: 28px;
}

.notice {
    text-align: center;
    font-size: 18px;
    color: #28a745;
    font-weight: bold;
    margin-bottom: 30px;
}

ul {
    max-width: 500px;
    margin: 0 auto;
    padding: 0;
    list-style: none;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

ul li {
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
    font-size: 16px;
    color: #495057;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

ul li:last-child {
    border-bottom: none;
}

ul li::before {
    content: '📌';
    margin-right: 10px;
    color: #007bff;
}
</style>

<p class="notice">
    Xin chào: <?= esc($_SESSION['name'] ?? 'Khách') ?> (<?= esc($_SESSION['role'] ?? '') ?>)
</p>

<?php
require_once 'config/db.php';

// Đếm các mục theo DB hiện có
$counts = [];
$counts['students'] = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'] ?? 0;
$counts['teachers'] = $conn->query("SELECT COUNT(*) as c FROM teachers")->fetch_assoc()['c'] ?? 0;
$counts['subjects'] = $conn->query("SELECT COUNT(*) as c FROM subjects")->fetch_assoc()['c'] ?? 0;
$counts['grades'] = $conn->query("SELECT COUNT(*) as c FROM grades")->fetch_assoc()['c'] ?? 0;
?>

<ul>
    <li>Sinh viên: <?= $counts['students'] ?></li>
    <li>Giảng viên: <?= $counts['teachers'] ?></li>
    <li>Môn học: <?= $counts['subjects'] ?></li>
    <li>Bảng điểm: <?= $counts['grades'] ?></li>
</ul>

<?php include 'includes/footer.php'; ?>
