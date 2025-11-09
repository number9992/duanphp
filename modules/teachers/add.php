<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$err = '';
$default_password = '123456'; // Password mặc định cho giáo viên

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $photo = uploadImage($_FILES['photo'] ?? null); // Hàm upload image từ functions.php

    if ($name && $username) {
        // Bắt đầu transaction để đảm bảo đồng bộ
        $conn->begin_transaction();

        try {
            // 1. Thêm vào bảng teachers
            $stmt_teacher = $conn->prepare("INSERT INTO teachers (name, email, phone, department, photo) VALUES (?, ?, ?, ?, ?)");
            $stmt_teacher->bind_param('sssss', $name, $email, $phone, $department, $photo);
            $stmt_teacher->execute();
            $teacher_id = $conn->insert_id;

            // 2. Thêm vào bảng users
            $hash = password_hash($default_password, PASSWORD_DEFAULT);

            // Lấy role_id của giáo viên từ bảng roles
            $stmt_role = $conn->prepare("SELECT id FROM roles WHERE role_name = 'teacher' LIMIT 1");
            $stmt_role->execute();
            $role_id = $stmt_role->get_result()->fetch_assoc()['id'] ?? 5;

            $stmt_user = $conn->prepare("INSERT INTO users (username, password, name, role_id, teacher_id) VALUES (?, ?, ?, ?, ?)");
            $stmt_user->bind_param('sssii', $username, $hash, $name, $role_id, $teacher_id);
            $stmt_user->execute();

            $conn->commit();
            header('Location:?url=teacher');
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $err = "Lỗi: " . $e->getMessage();
        }

    } else {
        $err = "Tên và Username là bắt buộc.";
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<style>
/* giữ nguyên style của bạn, hoặc thêm style nếu muốn */
</style>

<h2>Thêm Giảng viên</h2>

<?php if($err): ?>
    <p class="error"><?= esc($err) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <div class="form-row">
        <label>Họ tên <span style="color:red">*</span></label>
        <input name="name" type="text" required value="<?= esc($_POST['name'] ?? '') ?>">
    </div>

    <div class="form-row">
        <label>Username <span style="color:red">*</span></label>
        <input name="username" type="text" required value="<?= esc($_POST['username'] ?? '') ?>">
        <small>Mật khẩu mặc định: <?= $default_password ?></small>
    </div>

    <div class="form-row">
        <label>Email</label>
        <input name="email" type="email" value="<?= esc($_POST['email'] ?? '') ?>">
    </div>

    <div class="form-row">
        <label>Phone</label>
        <input name="phone" type="text" value="<?= esc($_POST['phone'] ?? '') ?>">
    </div>

    <div class="form-row">
        <label>Khoa</label>
        <input name="department" type="text" value="<?= esc($_POST['department'] ?? '') ?>">
    </div>

    <div class="form-row">
        <label>Ảnh</label>
        <input name="photo" type="file" accept="image/*">
    </div>

    <button class="btn" type="submit">Lưu & Tạo User</button>
</form>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
