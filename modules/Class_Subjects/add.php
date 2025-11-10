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

    if($class_id && $subject_id && $teacher_id && $semester){
        // Kiểm tra trùng trước khi insert
        $stmtCheck = $conn->prepare("SELECT id FROM class_subjects WHERE class_id=? AND subject_id=? AND semester=?");
        $stmtCheck->bind_param('iis', $class_id, $subject_id, $semester);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        if($resCheck->num_rows > 0){
            $err = "Lớp này đã được phân công môn học này cho học kỳ '$semester'.";
        } else {
            $stmt = $conn->prepare("INSERT INTO class_subjects (class_id, subject_id, teacher_id, semester) VALUES (?,?,?,?)");
            $stmt->bind_param('iiis',$class_id,$subject_id,$teacher_id,$semester);
            if($stmt->execute()){
                header('Location:?url=class_subjects'); 
                exit;
            } else $err = $stmt->error;
        }
    } else {
        $err = "Vui lòng chọn đầy đủ thông tin.";
    }
}

include __DIR__.'/../../includes/header.php';
?>

<h2>Thêm phân công môn học</h2>
<?php if(isset($err)) echo "<p style='color:red; text-align:center;'>$err</p>"; ?>

<form method="post" style="max-width:500px;margin:auto;">
    <div style="margin-bottom:10px;">
        <label>Lớp</label>
        <select name="class_id" required>
            <option value="">-- Chọn lớp --</option>
            <?php
            // Reset lại result nếu cần do fetch_assoc() đã dùng 1 lần
            $classes->data_seek(0);
            while($c = $classes->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>"><?= esc($c['class_name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div style="margin-bottom:10px;">
        <label>Môn học</label>
        <select name="subject_id" required>
            <option value="">-- Chọn môn học --</option>
            <?php
            $subjects->data_seek(0);
            while($s = $subjects->fetch_assoc()): ?>
                <option value="<?= $s['id'] ?>"><?= esc($s['subject_name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div style="margin-bottom:10px;">
        <label>Giảng viên</label>
        <select name="teacher_id" required>
            <option value="">-- Chọn giảng viên --</option>
            <?php
            $teachers->data_seek(0);
            while($t = $teachers->fetch_assoc()): ?>
                <option value="<?= $t['id'] ?>"><?= esc($t['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div style="margin-bottom:10px;">
        <label>Học kỳ</label>
        <input type="text" name="semester" required placeholder="VD: Học kỳ 1">
    </div>

    <div style="text-align:center;">
        <button type="submit" style="padding:10px 20px; background:#3498db; color:white; border:none; border-radius:5px;">Lưu</button>
    </div>
</form>

<?php include __DIR__.'/../../includes/footer.php'; ?>
