<?php
require_once '../../config/db.php';
$id = $_GET['id'];
$conn->query("DELETE FROM class_sections WHERE id=$id");
header("Location: list.php");
exit;
?>
