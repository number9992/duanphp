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
        if ($stmt->execute()) { header('Location: ?url=scores'); exit; } else $err = $stmt->error;
    } else $err = "Chọn sinh viên và môn học.";
}

include __DIR__ . '/../../includes/header.php';
?>

<style>
/* student_add.css */

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f2f5;
    margin: 0;
    padding: 0;
}

h2 {
    text-align: center;
    margin-top: 40px;
    color: #2c3e50;
    font-size: 28px;
}

form {
    max-width: 500px;
    margin: 30px auto;
    padding: 25px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.form-row {
    margin-bottom: 20px;
}

.form-row label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #34495e;
}

.form-row input, 
.form-row select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
    font-size: 14px;
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

p[style="color:red"] {
    text-align: center;
    font-weight: bold;
    margin-top: 10px;
}
</style>

<h2>Thêm Điểm</h2>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>

<form method="post">
    <div class="form-row">
        <label>Sinh viên</label>
        <select name="student_id" required>
            <option value="">-- Chọn --</option>
            <?php while($s = $students->fetch_assoc()): ?>
                <option value="<?= $s['id'] ?>"><?= esc($s['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="form-row">
        <label>Môn học</label>
        <select name="course_id" required>
            <option value="">-- Chọn --</option>
            <?php while($c = $courses->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="form-row">
        <label>Điểm</label>
        <input name="score" type="number" step="0.01" min="0" max="100" required>
    </div>

    <button class="btn">Lưu</button>
</form>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
