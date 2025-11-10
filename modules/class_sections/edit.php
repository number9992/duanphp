<?php
require_once '../../config/db.php';
require_once '../../includes/header.php';

$id = $_GET['id'];
$class = $conn->query("SELECT * FROM class_sections WHERE id=$id")->fetch_assoc();
$semesters = $conn->query("SELECT id, name FROM semesters");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $semester_id = $_POST['semester_id'];
  $teacher_name = $_POST['teacher_name'];

  $stmt = $conn->prepare("UPDATE class_sections SET name=?, semester_id=?, teacher_name=? WHERE id=?");
  $stmt->bind_param("sisi", $name, $semester_id, $teacher_name, $id);
  $stmt->execute();

  header("Location: list.php");
  exit;
}
?>

<div class="container">
  <h2>Sửa lớp học phần</h2>
  <form method="post">
    <label>Tên lớp học phần:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($class['name']) ?>" required><br>

    <label>Học kỳ:</label><br>
    <select name="semester_id" required>
      <?php while ($s = $semesters->fetch_assoc()): ?>
        <option value="<?= $s['id'] ?>" <?= $s['id']==$class['semester_id']?'selected':'' ?>>
          <?= htmlspecialchars($s['name']) ?>
        </option>
      <?php endwhile; ?>
    </select><br>

    <label>Tên giảng viên:</label><br>
    <input type="text" name="teacher_name" value="<?= htmlspecialchars($class['teacher_name']) ?>" required><br><br>

    <button type="submit">Cập nhật</button>
  </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
