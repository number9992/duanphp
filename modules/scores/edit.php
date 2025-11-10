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
        if ($u->execute()) { header('Location:?url=scores'); exit; } else $err = $u->error;
    } else $err = "Chọn sinh viên và môn học.";
}

include __DIR__ . '/../../includes/header.php';
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 0;
    color: #333;
}

h2 {
    text-align: center;
    color: #2c3e50;
    font-size: 28px;
    margin-top: 40px;
    margin-bottom: 25px;
}

form {
    max-width: 500px;
    margin: 0 auto;
    background: #fff;
    padding: 25px 30px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.form-row {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.form-row label {
    font-weight: 600;
    margin-bottom: 6px;
    color: #34495e;
}

.form-row input,
.form-row select {
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color .3s ease;
}

.form-row input:focus,
.form-row select:focus {
    border-color: #3498db;
    outline: none;
}

.btn {
    width: 100%;
    padding: 12px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color .3s ease;
}

.btn:hover {
    background-color: #2980b9;
}

p[style*="color:red"] {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
}
</style> 

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
    <div class="form-row"><label>Điểm</label>
        <input name="score" type="number" step="0.01" min="0" max="100" value="<?= esc($scoreRow['score']) ?>" required>
    </div>
    <button class="btn">Cập nhật</button>
</form>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
