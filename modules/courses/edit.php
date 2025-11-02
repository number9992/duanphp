<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if (!$id) header('Location:list.php');

$stmt = $conn->prepare("SELECT * FROM scores WHERE id=?");
$stmt->bind_param('i',$id); $stmt->execute();
$scoreRow = $stmt->get_result()->fetch_assoc();
if (!$scoreRow) header('Location:list.php');

$students = $conn->query("SELECT id,name FROM students ORDER BY name");
$courses = $conn->query("SELECT id,name FROM courses ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = intval($_POST['student_id'] ?? 0);
    $course_id = intval($_POST['course_id'] ?? 0);
    $score = floatval($_POST['score'] ?? 0);

    if ($student_id && $course_id) {
        $u = $conn->prepare("UPDATE scores SET student_id=?,course_id=?,score=? WHERE id=?");
        $u->bind_param('iidi',$student_id,$course_id,$score,$id);
        if ($u->execute()) { header('Location:list.php'); exit; } else $err = $u->error;
    } else $err = "Chọn sinh viên và môn học.";
}

include __DIR__ . '/../../includes/header.php';
?>
<h2>Sửa Điểm</h2>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>
<form method="post">
    <div class="form-row"><label>Sinh viên</label>
        <select name="student_id" required>
            <?php while($s = $students->fetch_assoc()): ?>
                <option value="<?= $s['id'] ?>" <?= $s['id']==$scoreRow['student_id']?'selected':'' ?>><?= esc($s['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="form-row"><label>Môn học</label>
        <select name="course_id" required>
            <?php while($c = $courses->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>" <?= $c['id']==$scoreRow['course_id']?'selected':'' ?>><?= esc($c['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="form-row"><label>Điểm</label><input name="score" type="number" step="0.01" min="0" max="100" value="<?= esc($scoreRow['score']) ?>" required></div>
    <button class="btn">Cập nhật</button>
</form>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
