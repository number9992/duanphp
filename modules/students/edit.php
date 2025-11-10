<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: list.php'); exit; }

// Lấy thông tin sinh viên
$stmt = $conn->prepare("
    SELECT students.*, classes.class_name 
    FROM students 
    LEFT JOIN classes ON students.class_id = classes.id 
    WHERE students.id = ?
");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if (!$row) { header('Location:list.php'); exit; }

// Lấy danh sách lớp cho dropdown
$classes = $conn->query("SELECT id, class_name FROM classes ORDER BY class_name");

// Khi nhấn nút cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $class_id = intval($_POST['class_id'] ?? 0);
    $photo = uploadImage($_FILES['photo'] ?? null) ?? $row['photo'];

    if ($name && $class_id) {
        $u = $conn->prepare("UPDATE students SET name=?, email=?, phone=?, class_id=?, photo=? WHERE id=?");
        $u->bind_param('sssisi', $name, $email, $phone, $class_id, $photo, $id);
        if ($u->execute()) {
            header('Location: ?url=student');
            exit;
        } else $err = $u->error;
    } else {
        $err = "Tên và lớp là bắt buộc.";
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background-color: #f0f2f5;
    margin: 0;
    padding: 0;
    color: #333;
}
h2 {
    text-align: center;
    margin: 30px 0;
    color: #2c3e50;
    font-size: 28px;
}
form {
    max-width: 600px;
    margin: 0 auto;
    background-color: #fff;
    padding: 30px;
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
.form-row input[type="file"],
.form-row select {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}
.form-row input:focus, .form-row select:focus {
    border-color: #3498db;
    outline: none;
}
button.btn {
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
button.btn:hover {
    background-color: #2980b9;
}
p[style="color:red"] {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
}
img {
    display: block;
    margin: 10px auto;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
</style>

<h2>Sửa Sinh viên</h2>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <div class="form-row"><label>Họ tên</label><input name="name" required value="<?= esc($row['name']) ?>"></div>
    <div class="form-row"><label>Email</label><input name="email" type="email" value="<?= esc($row['email']) ?>"></div>
    <div class="form-row"><label>Phone</label><input name="phone" value="<?= esc($row['phone']) ?>"></div>

    <div class="form-row">
        <label>Lớp</label>
        <select name="class_id" required>
            <option value="">-- Chọn lớp --</option>
            <?php while($c = $classes->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>" <?= $c['id'] == $row['class_id'] ? 'selected' : '' ?>>
                    <?= esc($c['class_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="form-row"><label>Ảnh (để trống nếu không đổi)</label><input name="photo" type="file" accept="image/*"></div>
    <?php if($row['photo']): ?><div><img src="/<?= esc($row['photo']) ?>" style="height:80px"></div><?php endif; ?>
    <button class="btn">Cập nhật</button>
</form>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
