<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("ID không hợp lệ");
}

// Lấy dữ liệu thời khóa biểu hiện tại
$stmt = $conn->prepare("
    SELECT * FROM timetables WHERE id = ?
");
$stmt->bind_param('i', $id);
$stmt->execute();
$timetable = $stmt->get_result()->fetch_assoc();
if (!$timetable) {
    die("Không tìm thấy thời khóa biểu");
}

// Lấy dữ liệu các bảng liên quan
$classes = $conn->query("SELECT * FROM classes");
$subjects = $conn->query("SELECT * FROM subjects");
$teachers = $conn->query("SELECT * FROM teachers");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = intval($_POST['class_id']);
    $subject_id = intval($_POST['subject_id']);
    $teacher_id = intval($_POST['teacher_id']);
    $semester = trim($_POST['semester']);
    $day_of_week = $_POST['day_of_week'];
    $session = $_POST['session'];
    $period = intval($_POST['period']);
    $room = trim($_POST['room']);

    if ($class_id && $subject_id && $teacher_id && $semester && $day_of_week && $session && $period) {
        $stmt = $conn->prepare("
            UPDATE timetables 
            SET class_id=?, subject_id=?, teacher_id=?, semester=?, day_of_week=?, session=?, period=?, room=? 
            WHERE id=?
        ");
        $stmt->bind_param('iiiissisi', $class_id, $subject_id, $teacher_id, $semester, $day_of_week, $session, $period, $room, $id);
        if ($stmt->execute()) {
            header('Location:?url=timetables'); exit;
        } else $err = $stmt->error;
    } else $err = "Vui lòng điền đầy đủ thông tin.";
}

include __DIR__.'/../../includes/header.php';
?>

<h2>Sửa Thời khóa biểu</h2>

<style>
    body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        background: #f4f6f9;
        margin: 0;
        padding: 20px;
        color: #333;
    }
    h2 {
        color: #2c3e50;
        text-align: center;
        margin-bottom: 20px;
    }
    form {
        background: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        max-width: 600px;
        margin: 0 auto;
    }
    label {
        display: block;
        margin-top: 12px;
        font-weight: 600;
        color: #34495e;
    }
    select, input[type="text"], input[type="number"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        margin-top: 5px;
        transition: border-color 0.3s ease;
    }
    select:focus, input:focus {
        border-color: #3498db;
        outline: none;
    }
    button {
        background: #3498db;
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 6px;
        font-weight: 600;
        margin-top: 20px;
        width: 100%;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    button:hover {
        background: #2980b9;
    }
    .error {
        background: #ffe5e5;
        border: 1px solid #ff9999;
        padding: 8px;
        border-radius: 4px;
        color: #d9534f;
        text-align: center;
        font-weight: 600;
        margin-bottom: 15px;
    }
</style>

<form method="post">
    <?php if (isset($err)) echo "<p class='error'>$err</p>"; ?>

    <label>Lớp</label>
    <select name="class_id">
        <?php while ($c = $classes->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>" <?= $c['id'] == $timetable['class_id'] ? 'selected' : '' ?>>
                <?= esc($c['class_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Môn học</label>
    <select name="subject_id">
        <?php while ($s = $subjects->fetch_assoc()): ?>
            <option value="<?= $s['id'] ?>" <?= $s['id'] == $timetable['subject_id'] ? 'selected' : '' ?>>
                <?= esc($s['subject_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Giảng viên</label>
    <select name="teacher_id">
        <?php while ($t = $teachers->fetch_assoc()): ?>
            <option value="<?= $t['id'] ?>" <?= $t['id'] == $timetable['teacher_id'] ? 'selected' : '' ?>>
                <?= esc($t['name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Học kỳ</label>
    <input type="text" name="semester" value="<?= esc($timetable['semester']) ?>" required>

    <label>Ngày</label>
    <select name="day_of_week">
        <?php
        $days = [
            'Mon' => 'Thứ 2',
            'Tue' => 'Thứ 3',
            'Wed' => 'Thứ 4',
            'Thu' => 'Thứ 5',
            'Fri' => 'Thứ 6',
            'Sat' => 'Thứ 7'
        ];
        foreach ($days as $k => $v):
        ?>
            <option value="<?= $k ?>" <?= $timetable['day_of_week'] == $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
    </select>

    <label>Ca</label>
    <select name="session">
        <option value="Sáng" <?= $timetable['session'] == 'Sáng' ? 'selected' : '' ?>>Sáng</option>
        <option value="Chiều" <?= $timetable['session'] == 'Chiều' ? 'selected' : '' ?>>Chiều</option>
    </select>

    <label>Tiết</label>
    <input type="number" name="period" value="<?= esc($timetable['period']) ?>" min="1" required>

    <label>Phòng học</label>
    <input type="text" name="room" value="<?= esc($timetable['room']) ?>">

    <button type="submit">💾 Cập nhật thời khóa biểu</button>
</form>

<?php include __DIR__.'/../../includes/footer.php'; ?>
