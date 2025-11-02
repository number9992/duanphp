<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location:list.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM teachers WHERE id=?");
$stmt->bind_param('i',$id); $stmt->execute(); $row = $stmt->get_result()->fetch_assoc();
if (!$row) header('Location:list.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $photo = uploadImage($_FILES['photo'] ?? null) ?? $row['photo'];

    if ($name) {
        $u = $conn->prepare("UPDATE teachers SET name=?,email=?,phone=?,department=?,photo=? WHERE id=?");
        $u->bind_param('sssssi',$name,$email,$phone,$department,$photo,$id);
        if ($u->execute()) { header('Location:list.php'); exit; } else $err = $u->error;
    } else $err = "Tên bắt buộc.";
}

include __DIR__ . '/../../includes/header.php';
?>
<h2>Sửa Giảng viên</h2>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <div class="form-row"><label>Họ tên</label><input name="name" required value="<?= esc($row['name']) ?>"></div>
    <div class="form-row"><label>Email</label><input name="email" value="<?= esc($row['email']) ?>"></div>
    <div class="form-row"><label>Phone</label><input name="phone" value="<?= esc($row['phone']) ?>"></div>
    <div class="form-row"><label>Khoa</label><input name="department" value="<?= esc($row['department']) ?>"></div>
    <div class="form-row"><label>Ảnh</label><input name="photo" type="file"></div>
    <?php if($row['photo']): ?><div><img src="/<?= esc($row['photo']) ?>" style="height:80px"></div><?php endif; ?>
    <button class="btn">Cập nhật</button>
</form>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
