<?php
require_once __DIR__ . '/../../config/db.php';
$id = $_GET['id'];
$class_section_id = $_GET['class_section_id'];

$conn->query("DELETE FROM schedules WHERE id=$id");
header("Location: list.php?class_section_id=$class_section_id");
exit;
?>
