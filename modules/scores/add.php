<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$students = $conn->query("SELECT id,name FROM students ORDER BY name");
$courses = $conn->query("SELECT id,name FROM courses ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = intval($_POST['student_id'] ?? 0);
    $course_id = intval($_POST['course_id'] ?? 0);
    $score = floatval($_POST['score'] ?? 0);

    if ($student_id && $course_id) {
        $stmt = $conn->prepare("INSERT INTO scores (student_id,course_id,score) VALUES (?,?,?)");
        $stmt->bind_param('iid',$student_id,$course_id,$score);
        if ($stmt->execute()) { header('Location:list.php'); exit; } else $err = $stmt->error;
    } else $err = "Chọn sinh viên và môn học.";
}

include __DIR__ . '/../../includes/header.php';
?>
<h2>Thêm Điểm</h2>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>
<form method="post">
    <div class="form-row"><label>Sinh viên</label>
        <select name="student_id" required>
            <option value="">-- Chọn --</option>
            <?php while($s = $students->fetch_assoc()): ?>
                <option value="<?= $s['id'] ?>"><?= esc($s['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="form-row"><label>Môn học</label>
        <select name="course_id" required>
            <option value="">-- Chọn --</option>
            <?php while($c = $courses->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="form-row"><label>Điểm</label><input name="score" type="number" step="0.01" min="0" max="100" required></div>
    <button class="btn">Lưu</button>
</form>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
