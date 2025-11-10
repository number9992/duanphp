<?php
require_once '../../config/db.php';
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM semesters WHERE id=$id");
$semester = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $start_date = $_POST['start_date'];
  $end_date = $_POST['end_date'];

  $stmt = $conn->prepare("UPDATE semesters SET name=?, start_date=?, end_date=? WHERE id=?");
  $stmt->bind_param("sssi", $name, $start_date, $end_date, $id);
  $stmt->execute();

  header("Location: list.php");
  exit;
}
?>

<form method="post">
  <h2>Sửa học kỳ</h2>
  <input type="text" name="name" value="<?= $semester['name'] ?>" required><br>
  <input type="date" name="start_date" value="<?= $semester['start_date'] ?>" required><br>
  <input type="date" name="end_date" value="<?= $semester['end_date'] ?>" required><br>
  <button type="submit">Cập nhật</button>
</form>
