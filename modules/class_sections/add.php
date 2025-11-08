<?php
require_once __DIR__ . '/../../config/db.php';

// Lấy danh sách môn học và học kỳ
$courses = $conn->query("SELECT id, name FROM courses");
$semesters = $conn->query("SELECT id, name FROM semesters");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $course_id = $_POST['course_id'];
    $semester_id = $_POST['semester_id'];

    // Tự động sinh section_code (ví dụ: LHP + timestamp)
    $section_code = "LHP" . time();

    $stmt = $conn->prepare("INSERT INTO class_sections (name, course_id, semester_id, section_code) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siis", $name, $course_id, $semester_id, $section_code);

    if ($stmt->execute()) {
        header("Location: ?url=class_sections");
        exit;
    } else {
        echo "<p style='color:red'>Lỗi thêm lớp: " . $stmt->error . "</p>";
    }
}
?>

<h2>Thêm lớp học phần</h2>

<form method="POST">
    <label>Tên lớp học phần:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Chọn môn học:</label><br>
    <select name="course_id" required>
        <option value="">--Chọn môn--</option>
        <?php while ($c = $courses->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Chọn học kỳ:</label><br>
    <select name="semester_id" required>
        <option value="">--Chọn học kỳ--</option>
        <?php while ($s = $semesters->fetch_assoc()): ?>
            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <button type="submit">Lưu</button>
    <a href="?module=class_sections&action=list">Hủy</a>
</form>
