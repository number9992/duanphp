<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $code = trim($_POST['subject_code'] ?? '');
    $name = trim($_POST['subject_name'] ?? '');
    $credits = intval($_POST['credit_hours'] ?? 0);

    if($code && $name){
        $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name, credit_hours) VALUES (?,?,?)");
        $stmt->bind_param('ssi',$code,$name,$credits);
        if($stmt->execute()){
            header('Location:?url=subjects'); exit;
        } else $err = $stmt->error;
    } else $err = "Mã môn và tên môn là bắt buộc.";
}

include __DIR__.'/../../includes/header.php';
?>
<h2>Thêm Môn học</h2>
<?php if(isset($err)) echo "<p style='color:red'>$err</p>"; ?>
<form method="post">
    <label>Mã môn</label><input type="text" name="subject_code" required><br>
    <label>Tên môn</label><input type="text" name="subject_name" required><br>
    <label>Số tín chỉ</label><input type="number" name="credit_hours" value="0"><br>
    <button type="submit">Lưu</button>
</form>
<?php include __DIR__.'/../../includes/footer.php'; ?>
