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
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f6f8;
    margin: 0;
    padding: 20px;
}

h2 {
    color: #333;
    margin-bottom: 20px;
}

.form-row {
    margin-bottom: 15px;
    display: flex;
    flex-direction: column;
}

label {
    font-weight: bold;
    margin-bottom: 5px;
    color: #555;
}

input[type="text"],
textarea,
select {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
    background-color: #fff;
    transition: border-color 0.3s;
}

input[type="text"]:focus,
textarea:focus,
select:focus {
    border-color: #007bff;
    outline: none;
}

textarea {
    resize: vertical;
    min-height: 100px;
}

.btn {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn:hover {
    background-color: #0056b3;
}

p[style*="color:red"] {
    background-color: #ffe6e6;
    padding: 10px;
    border: 1px solid #ff4d4d;
    border-radius: 5px;
}
</style>
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