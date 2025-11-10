<?php
require_once '../../config/db.php';
$id = $_GET['id'];
$conn->query("DELETE FROM semesters WHERE id=$id");
header("Location: list.php");
exit;
?>
