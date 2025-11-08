<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $start_date = $_POST['start_date'];
  $end_date = $_POST['end_date'];

  $stmt = $conn->prepare("INSERT INTO semesters (name, start_date, end_date) VALUES (?,?,?)");
  $stmt->bind_param("sss", $name, $start_date, $end_date);
  $stmt->execute();

  header("Location:?url=semesters ");
  exit;
}
?>

<form method="post">
  <h2>Thêm học kỳ</h2>
  <label>Tên học kỳ</label><br>
  <input type="text" name="name" required><br>
  <label>Ngày bắt đầu</label><br>
  <input type="date" name="start_date" required><br>
  <label>Ngày kết thúc</label><br>
  <input type="date" name="end_date" required><br><br>
  <button type="submit">Lưu</button>
</form>
