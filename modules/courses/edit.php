<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if (!$id) header('Location:list.php');

$stmt = $conn->prepare("SELECT * FROM courses WHERE id=?");
$stmt->bind_param('i',$id); $stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
if (!$course) header('Location:list.php');

$teachers = $conn->query("SELECT id,name FROM teachers ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $teacher_id = intval($_POST['teacher_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if ($name) {
        $u = $conn->prepare("UPDATE courses SET name=?,teacher_id=?,description=? WHERE id=?");
        $u->bind_param('sisi',$name,$teacher_id,$description,$id);
        if ($u->execute()) { header('Location:?url=courses'); exit; } else $err = $u->error;
    } else $err = "Tên môn bắt buộc.";
}

include __DIR__ . '/../../includes/header.php';
?>
<style>
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 20px;
    color: #333;
}

h2 {
    text-align: center;
    color: #2c3e50;
    font-size: 28px;
    margin-top: 10px;
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
.form-row select,
.form-row textarea {
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color .3s ease;
}

.form-row input:focus,
.form-row select:focus,
.form-row textarea:focus {
    border-color: #3498db;
    outline: none;
}

textarea {
    resize: vertical;
    min-height: 100px;
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
    background: #ffe6e6;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ffb3b3;
}
</style>

<h2>Sửa Môn học</h2>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>
<form method="post">
    <div class="form-row"><label>Tên môn</label><input name="name" required value="<?= esc($course['name']) ?>"></div>
    <div class="form-row"><label>Giảng viên</label>
        <select name="teacher_id">
            <option value="0">-- Chọn --</option>
            <?php while($t = $teachers->fetch_assoc()): ?>
                <option value="<?= $t['id'] ?>" <?= $t['id']==$course['teacher_id']?'selected':'' ?>><?= esc($t['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="form-row"><label>Mô tả</label><textarea name="description"><?= esc($course['description']) ?></textarea></div>
    <button class="btn">Cập nhật</button>
</form>
<?php include __DIR__ . '/../../includes/footer.php'; ?>