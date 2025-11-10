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

<style>
    /* ===== Khung form tổng ===== */
form {
    background-color: #ffffff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 40px auto;
    display: flex;
    flex-direction: column;
    gap: 18px;
}

/* ===== Tiêu đề ===== */
h2 {
    color: #0a2a5c;
    text-align: center;
    margin-bottom: 20px;
}

/* ===== Nhóm input ===== */
form div {
    display: flex;
    flex-direction: column;
}

/* ===== Label ===== */
form label {
    font-weight: 600;
    color: #1e3a8a;
    margin-bottom: 6px;
    font-size: 15px;
}

/* ===== Input, select, file ===== */
form input[type="text"],
form input[type="email"],
form input[type="file"],
form input[type="tel"],
form select {
    padding: 10px 14px;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    font-size: 15px;
    transition: border-color 0.2s, box-shadow 0.2s;
    background-color: #f9fafb;
}

form input:focus,
form select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
    outline: none;
    background-color: #ffffff;
}

/* ===== Nút lưu ===== */
form .btn {
    background-color: #3c66d7ff;
    color: #fff;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    padding: 12px;
    font-size: 15px;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
}

form .btn:hover {
    background-color: #1e40af;
    transform: translateY(-1px);
}

/* ===== Thông báo lỗi ===== */
p[style*="color:red"] {
    text-align: center;
    font-weight: 500;
    background: #fee2e2;
    color: #b91c1c !important;
    border: 1px solid #fecaca;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 10px;
}

</style>

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
