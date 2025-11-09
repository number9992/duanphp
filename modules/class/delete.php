<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if($id){
    $conn->query("DELETE FROM classes WHERE id=$id");
}

header('Location: ?url=class');
exit;
