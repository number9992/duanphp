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
        } else {
            // Xử lý lỗi UNIQUE KEY nếu Mã môn đã tồn tại
            if ($conn->errno === 1062) {
                $err = "Lỗi: Mã môn '" . esc($code) . "' đã tồn tại.";
            } else {
                $err = $stmt->error;
            }
        }
    } else $err = "Mã môn và tên môn là bắt buộc.";
}

include __DIR__.'/../../includes/header.php';
?>

<style>
/* Tối ưu CSS cho UX/UI */
body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f0f2f5; color: #333; }
h2 { 
    text-align: center; margin: 30px 0; color: #2c3e50; font-size: 28px; 
    border-bottom: 2px solid #3498db; padding-bottom: 10px; display: block; width: 100%; 
}

form {
    max-width: 500px; /* Độ rộng vừa phải */
    margin: 0 auto;
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
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
    font-size: 14px;
}

/* ĐỒNG NHẤT CHIỀU CAO INPUT */
.form-row input[type="text"],
.form-row input[type="number"] { 
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
    background-color: #f9f9f9;
    height: 44px; /* Chiều cao cố định */
    box-sizing: border-box; 
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-row input:focus {
    border-color: #27ae60; /* Màu xanh lá cây khi focus */
    box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.2);
    outline: none;
}

button[type="submit"] {
    display: block;
    width: 100%;
    padding: 12px;
    background-color: #27ae60; /* Màu xanh lá cây nổi bật */
    color: white;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 10px;
}

button[type="submit"]:hover {
    background-color: #229954;
}

p[style*="color:red"] {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
}
</style>

<h2>Thêm Môn học</h2>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>

<form method="post">
    
    <div class="form-row">
        <label>Mã môn (*)</label>
        <input type="text" name="subject_code" required value="<?= esc($_POST['subject_code'] ?? '') ?>">
    </div>
    
    <div class="form-row">
        <label>Tên môn (*)</label>
        <input type="text" name="subject_name" required value="<?= esc($_POST['subject_name'] ?? '') ?>">
    </div>
    
    <div class="form-row">
        <label>Số tín chỉ</label>
        <input type="number" name="credit_hours" value="<?= intval($_POST['credit_hours'] ?? 0) ?>" min="0">
    </div>
    
    <button type="submit">Lưu Môn học</button>
</form>

<?php include __DIR__.'/../../includes/footer.php'; ?>