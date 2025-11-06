<?php
require_once 'includes/functions.php';
requireLogin();
include 'includes/header.php';
?>
<h2>Dashboard</h2>
<style>
    /* dashboard.css */

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
}

h2 {
    text-align: center;
    margin: 30px;
    color: #343a40; 
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
<p class="notice">Xin chào: <?= esc($_SESSION['user_name']) ?> (<?= esc($_SESSION['user_role']) ?>)</p>

<?php
require_once 'config/db.php';
$counts = [];
$tables = ['students','teachers','courses','scores'];
foreach($tables as $t){
    $res = $conn->query("SELECT COUNT(*) as c FROM $t");
    $row = $res->fetch_assoc();
    $counts[$t] = $row['c'];
}
?>

<ul>
    <li>Sinh viên: <?= $counts['students'] ?></li>
    <li>Giảng viên: <?= $counts['teachers'] ?></li>
    <li>Môn học: <?= $counts['courses'] ?></li>
    <li>Bảng điểm: <?= $counts['scores'] ?></li>
</ul>

<?php include 'includes/footer.php'; ?>
