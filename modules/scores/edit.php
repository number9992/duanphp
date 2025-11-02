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
        if ($u->execute()) { header('Location:list.php'); exit; } else $err = $u->error;
    } else $err = "Tên môn bắt buộc.";
}

include __DIR__ . '/../../includes/header.php';
?>
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
