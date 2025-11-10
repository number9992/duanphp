<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if($id <= 0){
    die("ID không hợp lệ");
}

// Lấy dữ liệu môn học hiện tại
$stmt = $conn->prepare("SELECT * FROM subjects WHERE id=?");
$stmt->bind_param('i',$id);
$stmt->execute();
$subject = $stmt->get_result()->fetch_assoc();
if(!$subject){
    die("Không tìm thấy môn học");
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $code = trim($_POST['subject_code'] ?? '');
    $name = trim($_POST['subject_name'] ?? '');
    $credits = intval($_POST['credit_hours'] ?? 0);

    if($code && $name){
        $stmt = $conn->prepare("UPDATE subjects SET subject_code=?, subject_name=?, credit_hours=? WHERE id=?");
        $stmt->bind_param('ssii',$code,$name,$credits,$id);
        if($stmt->execute()){
            header('Location:?url=subjects'); exit;
        } else $err = $stmt->error;
    } else $err = "Mã môn và tên môn là bắt buộc.";
}

include __DIR__.'/../../includes/header.php';
?>
<h2>Sửa Môn học</h2>

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
        margin-bottom: 20px;
        text-align: center;
    }
    form {
        background: #fff;
        padding: 20px;
        border-radius: 6px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        max-width: 400px;
        margin: 0 auto;
    }
    form label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #34495e;
    }
    form input[type="text"],
    form input[type="number"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        transition: border 0.3s ease;
    }
    form input:focus {
        border-color: #3498db;
        outline: none;
    }
    form button {
        background: #3498db;
        color: #fff;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.3s ease;
    }
    form button:hover {
        background: #2980b9;
    }
    p[style="color:red"] {
        background: #ffe5e5;
        border: 1px solid #ff9999;
        padding: 8px;
        border-radius: 4px;
    }
</style>

<?php if(isset($err)) echo "<p style='color:red'>$err</p>"; ?>
<form method="post">
    <label>Mã môn</label>
    <input type="text" name="subject_code" value="<?= esc($subject['subject_code']) ?>" required><br>
    <label>Tên môn</label>
    <input type="text" name="subject_name" value="<?= esc($subject['subject_name']) ?>" required><br>
    <label>Số tín chỉ</label>
    <input type="number" name="credit_hours" value="<?= esc($subject['credit_hours']) ?>"><br>
    <button type="submit">Cập nhật</button>
</form>
<?php include __DIR__.'/../../includes/footer.php'; ?>