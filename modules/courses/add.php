
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$teachers = $conn->query("SELECT id,name FROM teachers ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $teacher_id = intval($_POST['teacher_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if ($name) {
        $stmt = $conn->prepare("INSERT INTO courses (name,teacher_id,description) VALUES (?,?,?)");
        $stmt->bind_param('sis',$name,$teacher_id,$description);
        if ($stmt->execute()) { header('Location:?url=courses'); exit; } else $err = $stmt->error;
    } else $err = "Tên môn bắt buộc.";
}

include __DIR__ . '/../../includes/header.php';
?>
<style>
    /* Form container */
form {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    font-family: Arial, sans-serif;
}

/* Form row */
.form-row {
    margin-bottom: 15px;
    display: flex;
    flex-direction: column;
}

/* Labels */
.form-row label {
    font-weight: bold;
    margin-bottom: 5px;
    color: #333;
}

/* Inputs and textarea */
.form-row input,
.form-row select,
.form-row textarea {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-row input:focus,
.form-row select:focus,
.form-row textarea:focus {
    border-color: #007bff;
    outline: none;
}

/* Button */
button.btn {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    font-weight: bold;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button.btn:hover {
    background-color: #0056b3;
}

/* Error message */
p[style*="color:red"] {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
}
</style>
<h2>Thêm Môn học</h2>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>
<form method="post">
    <div class="form-row"><label>Tên môn</label><input name="name" required></div>
    <div class="form-row"><label>Giảng viên</label>
        <select name="teacher_id">
            <option value="0">-- Chọn --</option>
            <?php while($t = $teachers->fetch_assoc()): ?>
                <option value="<?= $t['id'] ?>"><?= esc($t['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="form-row"><label>Mô tả</label><textarea name="description"></textarea></div>
    <button class="btn">Lưu</button>
</form>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
