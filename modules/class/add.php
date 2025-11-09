<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$teachersRes = $conn->query("SELECT id, name FROM teachers ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = trim($_POST['class_name'] ?? '');
    $grade_level = intval($_POST['grade_level'] ?? 0);
    $homeroom_teacher_id = intval($_POST['homeroom_teacher_id'] ?? 0) ?: null;
    $school_year = trim($_POST['school_year'] ?? '');

    if ($class_name && $grade_level && $school_year) {
        $stmt = $conn->prepare("INSERT INTO classes (class_name, grade_level, homeroom_teacher_id, school_year) VALUES (?,?,?,?)");
        $stmt->bind_param('siss', $class_name, $grade_level, $homeroom_teacher_id, $school_year);
        if ($stmt->execute()) {
            header('Location: ?url=class');
            exit;
        } else $err = $stmt->error;
    } else $err = "Vui lòng điền đầy đủ thông tin.";
}

include __DIR__ . '/../../includes/header.php';
?>

<h2>Thêm Lớp</h2>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>
<form method="post">
    <div>
        <label>Tên lớp</label>
        <input name="class_name" required>
    </div>
    <div>
        <label>Cấp</label>
        <input name="grade_level" type="number" required>
    </div>
    <div>
        <label>GVCN</label>
        <select name="homeroom_teacher_id">
            <option value="">-- Chọn giáo viên --</option>
            <?php while($t = $teachersRes->fetch_assoc()): ?>
                <option value="<?= $t['id'] ?>"><?= esc($t['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div>
        <label>Năm học</label>
        <input name="school_year" required>
    </div>
    <button class="btn">Lưu</button>
</form>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
