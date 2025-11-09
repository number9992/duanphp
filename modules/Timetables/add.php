<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();

$classes = $conn->query("SELECT * FROM classes");
$subjects = $conn->query("SELECT * FROM subjects");
$teachers = $conn->query("SELECT * FROM teachers");

if($_SERVER['REQUEST_METHOD']==='POST'){
    $class_id = intval($_POST['class_id']);
    $subject_id = intval($_POST['subject_id']);
    $teacher_id = intval($_POST['teacher_id']);
    $semester = trim($_POST['semester']);
    $day_of_week = $_POST['day_of_week'];
    $session = $_POST['session'];
    $period = intval($_POST['period']);
    $room = trim($_POST['room']);

    if($class_id && $subject_id && $teacher_id && $semester && $day_of_week && $session && $period){
        $stmt = $conn->prepare("INSERT INTO timetables (class_id, subject_id, teacher_id, semester, day_of_week, session, period, room) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param('iiiissis',$class_id,$subject_id,$teacher_id,$semester,$day_of_week,$session,$period,$room);
        if($stmt->execute()){
            header('Location:?url=timetables'); exit;
        } else $err = $stmt->error;
    } else $err = "Vui lòng điền đầy đủ thông tin.";
}

include __DIR__.'/../../includes/header.php';
?>
<h2>Thêm thời khóa biểu</h2>
<?php if(isset($err)) echo "<p style='color:red'>$err</p>"; ?>
<form method="post">
<label>Lớp</label>
<select name="class_id"><?php while($c=$classes->fetch_assoc()): ?>
<option value="<?= $c['id'] ?>"><?= esc($c['class_name']) ?></option>
<?php endwhile; ?></select><br>

<label>Môn</label>
<select name="subject_id"><?php while($s=$subjects->fetch_assoc()): ?>
<option value="<?= $s['id'] ?>"><?= esc($s['subject_name']) ?></option>
<?php endwhile; ?></select><br>

<label>Giảng viên</label>
<select name="teacher_id"><?php while($t=$teachers->fetch_assoc()): ?>
<option value="<?= $t['id'] ?>"><?= esc($t['name']) ?></option>
<?php endwhile; ?></select><br>

<label>Học kỳ</label><input type="text" name="semester" required><br>
<label>Ngày</label>
<select name="day_of_week">
<option value="Mon">Thứ 2</option>
<option value="Tue">Thứ 3</option>
<option value="Wed">Thứ 4</option>
<option value="Thu">Thứ 5</option>
<option value="Fri">Thứ 6</option>
<option value="Sat">Thứ 7</option>
</select><br>
<label>Ca</label>
<select name="session"><option value="Sáng">Sáng</option><option value="Chiều">Chiều</option></select><br>
<label>Tiết</label><input type="number" name="period" required><br>
<label>Phòng</label><input type="text" name="room"><br>
<button type="submit">Lưu</button>
</form>
<?php include __DIR__.'/../../includes/footer.php'; ?>
