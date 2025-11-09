<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if(!$id) { header('Location: ?url=class'); exit; }

$teachersRes = $conn->query("SELECT id, name FROM teachers ORDER BY name ASC");
$row = $conn->query("SELECT * FROM classes WHERE id=$id")->fetch_assoc();
if(!$row) { header('Location: ?url=class'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = trim($_POST['class_name'] ?? '');
    $grade_level = intval($_POST['grade_level'] ?? 0);
    $homeroom_teacher_id = intval($_POST['homeroom_teacher_id'] ?? 0) ?: null;
    $school_year = trim($_POST['school_year'] ?? '');

    if($class_name && $grade_level && $school_year) {
        $stmt = $conn->prepare("UPDATE classes SET class_name=?, grade_level=?, homeroom_teacher_id=?, school_year=? WHERE id=?");
        $stmt->bind_param('sissi', $class_name, $grade_level, $homeroom_teacher_id, $school_year, $id);
        if($stmt->execute()){
            header('Location: ?url=class'); exit;
        } else $err = $stmt->error;
    } else $err = "Vui lòng điền đầy đủ thông tin.";
}

include __DIR__ . '/../../includes/header.php';
?>

<h2>Sửa Lớp</h2>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>
<form method="post">
    <div>
        <label>Tên lớp</label>
        <input name="class_name" required value="<?= esc($row['class_name']) ?>">
    </div>
    <div>
        <label>Cấp</label>
        <input name="grade_level" type="number" required value="<?= esc($row['grade_level']) ?>">
    </div>
    <div>
        <label>GVCN</label>
        <select name="homeroom_teacher_id">
            <option value="">-- Chọn giáo viên --</option>
            <?php while($t = $teachersRes->fetch_assoc()): ?>
                <option value="<?= $t['id'] ?>" <?= $row['homeroom_teacher_id']==$t['id']?'selected':'' ?>><?= esc($t['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div>
        <label>Năm học</label>
        <input name="school_year" required value="<?= esc($row['school_year']) ?>">
    </div>
    <button class="btn">Cập nhật</button>
</form>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
