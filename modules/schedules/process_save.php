<?php
require_once __DIR__ . '/../../config/db.php';

$class_section_id = $_POST['class_section_id'];
$day_of_week = $_POST['day_of_week'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$room_number = $_POST['room_number'];

$stmt = $conn->prepare("INSERT INTO schedules (class_section_id, day_of_week, start_time, end_time, room_number) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $class_section_id, $day_of_week, $start_time, $end_time, $room_number);
$stmt->execute();

header("Location: list.php?class_section_id=$class_section_id");
exit;
?>
