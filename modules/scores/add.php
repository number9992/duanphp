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
        if ($stmt->execute()) { header('Location:list.php'); exit; } else $err = $stmt->error;
    } else $err = "Tên môn bắt buộc.";
}

include __DIR__ . '/../../includes/header.php';
?>
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
