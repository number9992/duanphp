<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

// Lấy danh sách lớp để chọn
$classesRes = $conn->query("SELECT id, class_name FROM classes ORDER BY grade_level, class_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $class_id = intval($_POST['class_id'] ?? 0); // ✅ Lưu class_id
    $photo = uploadImage($_FILES['photo'] ?? null);

    if ($name && $class_id) {
        $stmt = $conn->prepare("INSERT INTO students (name, email, phone, class_id, photo) VALUES (?,?,?,?,?)");
        $stmt->bind_param('sssis', $name, $email, $phone, $class_id, $photo);

        if ($stmt->execute()) {
            header('Location: ?url=student');
            exit;
        } else {
            $err = $stmt->error;
        }
    } else {
        $err = "Tên và lớp là bắt buộc.";
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<h2>Thêm Sinh viên</h2>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <div>
        <label>Họ tên</label>
        <input name="name" required>
    </div>
    <div>
        <label>Email</label>
        <input name="email" type="email">
    </div>
    <div>
        <label>Phone</label>
        <input name="phone">
    </div>
    <div>
        <label>Lớp</label>
        <select name="class_id" required>
            <option value="">-- Chọn lớp --</option>
            <?php while($c = $classesRes->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>"><?= esc($c['class_name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div>
        <label>Ảnh</label>
        <input name="photo" type="file" accept="image/*">
    </div>
    <button class="btn">Lưu</button>
</form>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
