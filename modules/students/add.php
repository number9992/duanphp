<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

//  bắt dữ liệu bên người dùng nhập 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $class = trim($_POST['class'] ?? '');
    $photo = uploadImage($_FILES['photo'] ?? null);

    if ($name) {
        //  đây là xử lsy vs db bằng câu lênh inserinto vào db 
        $stmt = $conn->prepare("INSERT INTO students (name,email,phone,class,photo) VALUES (?,?,?,?,?)");
        $stmt->bind_param('sssss',$name,$email,$phone,$class,$photo);
        //  gán dữ liệu bắt  để truyền vào 
        if ($stmt->execute()) {
            header('Location: ?url=student');
            //  nếu mà thành công thì sẽ sang trang list student 
            exit;
        } else {
            $err = $stmt->error;
            //  nếu thất bại sẽ báo lỗi 
        }
    } else $err = "Tên bắt buộc.";


}

include __DIR__ . '/../../includes/header.php';
?>
<h2>Thêm Sinh viên</h2>
<style>
    /* student_add.css */

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f2f5;
    margin: 0;
    padding: 0;
}

h2 {
    text-align: center;
    margin-top: 40px;
    color: #2c3e50;
    font-size: 28px;
}

form {
    max-width: 500px;
    margin: 30px auto;
    padding: 25px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.form-row {
    margin-bottom: 20px;
}

.form-row label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #34495e;
}

.form-row input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
    font-size: 14px;
}

.form-row input[type="file"] {
    padding: 6px;
    font-size: 13px;
}

.btn {
    width: 100%;
    padding: 12px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #2980b9;
}

p[style="color:red"] {
    text-align: center;
    font-weight: bold;
    margin-top: 10px;
}
</style>
<!--  báo lỗi -->
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>
    <!--  sẽ có 2 dạng get và post  get là để lấy dữ liệu từ db , post sẽ gửi dữ liệu từ người dùng vào db  -->
<form method="post" enctype="multipart/form-data">
    <div class="form-row"><label>Họ tên</label><input name="name" required></div>
    <div class="form-row"><label>Email</label><input name="email" type="email"></div>
    <div class="form-row"><label>Phone</label><input name="phone"></div>
    <div class="form-row"><label>Lớp</label><input name="class"></div>
    <div class="form-row"><label>Ảnh</label><input name="photo" type="file" accept="image/*"></div>
    <button class="btn">Lưu</button>
</form>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
