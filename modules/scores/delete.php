<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
$id = intval($_GET['id'] ?? 0);
if ($id) {
    $stmt = $conn->prepare("DELETE FROM courses WHERE id=?");
    $stmt->bind_param('i',$id);
    $stmt->execute();
}
header('Location:list.php');
exit;
