<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $err = '';
    $msg = '';

    if ($name && $start_date && $end_date) {
        // Kiểm tra trùng tên học kỳ
        $stmt_check = $conn->prepare("SELECT id FROM semesters WHERE name = ? LIMIT 1");
        $stmt_check->bind_param('s', $name);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $err = "Học kỳ này đã tồn tại!";
        } else {
            $stmt = $conn->prepare("INSERT INTO semesters (name, start_date, end_date) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $start_date, $end_date);
            if ($stmt->execute()) {
                $msg = "Thêm học kỳ thành công!";
                header("Location:?url=semesters");
                exit;
            } else {
                $err = "Lỗi khi thêm học kỳ: " . $stmt->error;
            }
        }
    } else {
        $err = "Vui lòng điền đầy đủ thông tin!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm học kỳ</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f7f9fc;
    margin: 0;
    padding: 30px;
}
form {
    max-width: 400px;
    margin: 0 auto;
    padding: 25px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #2c3e50;
}
label {
    display: block;
    margin-top: 10px;
    font-weight: bold;
    color: #34495e;
}
input[type="text"], input[type="date"] {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
}
button {
    width: 100%;
    padding: 12px;
    margin-top: 20px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
}
button:hover {
    background-color: #2980b9;
}
p.notice {
    text-align: center;
    color: green;
    font-weight: bold;
}
p.error {
    text-align: center;
    color: red;
    font-weight: bold;
}
</style>
</head>
<body>

<form method="post">
    <h2>Thêm học kỳ</h2>

    <?php if (!empty($msg)): ?>
        <p class="notice"><?= esc($msg) ?></p>
    <?php endif; ?>
    <?php if (!empty($err)): ?>
        <p class="error"><?= esc($err) ?></p>
    <?php endif; ?>

    <label>Tên học kỳ</label>
    <input type="text" name="name" required value="<?= esc($_POST['name'] ?? '') ?>">

    <label>Ngày bắt đầu</label>
    <input type="date" name="start_date" required value="<?= esc($_POST['start_date'] ?? '') ?>">

    <label>Ngày kết thúc</label>
    <input type="date" name="end_date" required value="<?= esc($_POST['end_date'] ?? '') ?>">

    <button type="submit">Lưu</button>
</form>

</body>
</html>
