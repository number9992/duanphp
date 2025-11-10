<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location:?url=teacher'); exit; }

// Lấy thông tin giáo viên
$stmt = $conn->prepare("SELECT * FROM teachers WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) { header('Location:?url=teacher'); exit; }

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $photo = uploadImage($_FILES['photo'] ?? null) ?: $row['photo'];

    if ($name) {
        $stmt = $conn->prepare("UPDATE teachers SET name=?, email=?, phone=?, department=?, photo=? WHERE id=?");
        $stmt->bind_param('sssssi', $name, $email, $phone, $department, $photo, $id);
        if ($stmt->execute()) {
            header('Location:?url=teacher');
            exit;
        } else {
            $err = "Lỗi: " . $stmt->error;
        }
    } else {
        $err = "Tên giảng viên là bắt buộc.";
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<h2>Sửa Giảng viên</h2>

<?php if($err): ?>
    <p style="color:red; text-align:center; font-weight:bold; margin-bottom:20px;"><?= esc($err) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <div class="form-row">
        <label>Họ tên <span style="color:red">*</span></label>
        <input name="name" type="text" required value="<?= esc($_POST['name'] ?? $row['name']) ?>">
    </div>

    <div class="form-row">
        <label>Email</label>
        <input name="email" type="email" value="<?= esc($_POST['email'] ?? $row['email']) ?>">
    </div>

    <div class="form-row">
        <label>Phone</label>
        <input name="phone" type="text" value="<?= esc($_POST['phone'] ?? $row['phone']) ?>">
    </div>

    <div class="form-row">
        <label>Khoa</label>
        <input name="department" type="text" value="<?= esc($_POST['department'] ?? $row['department']) ?>">
    </div>

    <div class="form-row">
        <label>Ảnh</label>
        <input name="photo" type="file" accept="image/*">
        <?php if($row['photo']): ?>
            <div style="margin-top:10px;">
                <img src="/<?= esc($row['photo']) ?>" style="height:80px; border-radius:6px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
            </div>
        <?php endif; ?>
    </div>

    <button class="btn" type="submit">Cập nhật</button>
</form>

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
    margin-bottom: 30px;
}

form {
    max-width: 600px;
    margin: 0 auto;
    background: #fff;
    padding: 25px 30px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.form-row {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.form-row label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #34495e;
}

.form-row input[type="text"],
.form-row input[type="email"],
.form-row input[type="file"] {
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.form-row input:focus {
    border-color: #3498db;
    outline: none;
}

.btn {
    display: block;
    width: 100%;
    padding: 12px;
    background-color: #3498db;
    color: white;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #2980b9;
}
</style>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
