<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

// Lấy danh sách lớp để chọn
$classesRes = $conn->query("SELECT id, class_name FROM classes ORDER BY grade_level, class_name");

// Lấy Role ID của Sinh viên (Giả định 'student' có ID là 6 hoặc bạn phải truy vấn)
// **BẠN CẦN THAY 6 BẰNG ID THỰC TẾ CỦA ROLE 'student'**
$STUDENT_ROLE_ID = 6; 

// --- HÀM TẠO MÃ SINH VIÊN TỰ ĐỘNG ---
function generateUniqueStudentCode($conn) {
    // Định dạng: SVYYXXXX (SV + 2 số cuối năm hiện tại + 4 số thứ tự)
    $year = date('y'); 
    
    // Tìm mã sinh viên lớn nhất trong năm hiện tại
    $stmt = $conn->prepare("
        SELECT student_code FROM students 
        WHERE student_code LIKE CONCAT('SV', ?, '%') 
        ORDER BY student_code DESC LIMIT 1
    ");
    $stmt->bind_param('s', $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $latest_code = $result->fetch_assoc()['student_code'] ?? null;
    $stmt->close();
    
    if ($latest_code) {
        $sequence = (int) substr($latest_code, 4) + 1;
    } else {
        $sequence = 1;
    }

    $new_sequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);
    
    return "SV" . $year . $new_sequence;
}
// --- KẾT THÚC HÀM TẠO MÃ SINH VIÊN ---

$generated_student_code = generateUniqueStudentCode($conn); // Tạo MSV trước khi xử lý POST

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // === 1. LẤY DỮ LIỆU SINH VIÊN ===
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $class_id = intval($_POST['class_id'] ?? 0);
    $photo = uploadImage($_FILES['photo'] ?? null);

    // MSV TỰ ĐỘNG CHÍNH LÀ USERNAME
    $student_code = $_POST['student_code'] ?? $generated_student_code; // Lấy MSV từ form (đã ẩn)
    $username = $student_code; 
    $password = $_POST['password'] ?? '';
    
    // Kiểm tra tính hợp lệ cơ bản
    if (empty($name) || empty($class_id) || empty($password)) {
        $err = "Họ tên, Lớp và Mật khẩu là bắt buộc.";
    } elseif (strlen($password) < 6) {
        $err = "Mật khẩu phải ít nhất 6 ký tự.";
    } else {
        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $conn->begin_transaction();
        $success = false;
        
        try {
            // A. INSERT VÀO BẢNG STUDENTS (THÊM student_code)
            $stmt_student = $conn->prepare("INSERT INTO students (name, student_code, email, phone, class_id, photo) VALUES (?,?,?,?,?,?)");
            $stmt_student->bind_param('sssiis', $name, $student_code, $email, $phone, $class_id, $photo);
            
            if (!$stmt_student->execute()) {
                throw new Exception("Lỗi khi thêm sinh viên (MSV có thể trùng lặp): " . $stmt_student->error);
            }
            
            $new_student_id = $conn->insert_id;
            
            // B. INSERT VÀO BẢNG USERS (USERNAME = MSV)
            $stmt_user = $conn->prepare("INSERT INTO users (username, password, name, role_id, student_id) VALUES (?,?,?,?,?)");
            $stmt_user->bind_param('sssii', $username, $hashed_password, $name, $STUDENT_ROLE_ID, $new_student_id);

            if (!$stmt_user->execute()) {
                throw new Exception("Lỗi khi tạo tài khoản: " . $stmt_user->error);
            }

            $conn->commit();
            $success = true;

        } catch (Exception $e) {
            $conn->rollback();
            $err = $e->getMessage();
        }

        if ($success) {
            $_SESSION['last_student_code'] = $student_code; 
            header('Location: ?url=student');
            exit;
        }
    }
    
    $generated_student_code = generateUniqueStudentCode($conn);
}

include __DIR__ . '/../../includes/header.php';
?>

<style>
/* Tối ưu CSS cho UX/UI - Bố cục 2 cột */
/* Giữ nguyên CSS body, h2, form từ form edit đã sửa */

form {
    max-width: 800px; 
    margin: 0 auto;
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

/* Bố cục 2 cột chính */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr; 
    gap: 20px 30px; 
    margin-bottom: 20px;
}

/* Form row bên trong grid */
.form-row {
    display: flex;
    flex-direction: column;
    margin-bottom: 0; 
}

/* Thiết lập các trường nhập liệu */
.form-row label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #34495e;
    font-size: 14px;
}

/* SỬA LỖI ĐỒNG NHẤT CHIỀU CAO INPUT/SELECT/FILE */
.form-row input[type="text"],
.form-row input[type="email"],
.form-row input[type="password"], /* Thêm password */
.form-row select { 
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
    background-color: #f9f9f9;
    height: 44px; /* Chiều cao cố định */
    box-sizing: border-box; 
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

/* Input file: Chiếm 2 cột */
.input-file-wrapper {
    grid-column: 1 / -1;
    margin-top: 10px;
}
.input-file-wrapper input[type="file"] {
    padding: 8px 12px;
    height: 44px; 
    box-sizing: border-box;
    border: 1px solid #ddd;
    border-radius: 6px;
    background-color: #f9f9f9;
}

.form-row input:focus, .form-row select:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
    outline: none;
}

.full-width {
    grid-column: 1 / -1; 
}
/* MSV box */
.msv-info {
    grid-column: 1 / -1;
    background:#e8f0fe; 
    padding:15px; 
    border-radius:8px; 
    margin-bottom:25px; 
    font-size: 16px;
}

button.btn {
    display: block;
    width: 100%;
    padding: 14px;
    background-color: #27ae60; 
    color: white;
    font-size: 18px;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button.btn:hover {
    background-color: #229954;
}

</style>

<h2>Thêm Sinh viên & Tạo Tài khoản</h2>

<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>

<form method="post" enctype="multipart/form-data">
    
    <div class="msv-info">
        Mã Sinh viên Tự động (Username): 
        <span style="color:#2c3e50; font-weight:bold;"><?= esc($generated_student_code) ?></span>
        <input type="hidden" name="student_code" value="<?= esc($generated_student_code) ?>">
    </div>
    
    <div class="form-grid">
        <div class="form-row"><label>Họ tên (*)</label><input name="name" required value="<?= esc($_POST['name'] ?? '') ?>"></div>
        <div class="form-row"><label>Phone</label><input name="phone" value="<?= esc($_POST['phone'] ?? '') ?>"></div>
        <div class="form-row"><label>Password (*)</label><input name="password" type="password" required minlength="6"></div>
        
        <div class="form-row"><label>Email</label><input name="email" type="email" value="<?= esc($_POST['email'] ?? '') ?>"></div>
        <div class="form-row">
            <label>Lớp (*)</label>
            <select name="class_id" required>
                <option value="">-- Chọn lớp --</option>
                <?php 
                $selected_class_id = $_POST['class_id'] ?? 0;
                $classesRes->data_seek(0); 
                while($c = $classesRes->fetch_assoc()): 
                ?>
                    <option value="<?= $c['id'] ?>" <?= $c['id'] == $selected_class_id ? 'selected' : '' ?>>
                        <?= esc($c['class_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
         <div class="form-row full-width">
            </div>
    </div>
    
    <div class="form-row input-file-wrapper">
        <label>Ảnh đại diện</label>
        <input name="photo" type="file" accept="image/*">
    </div>

    <button class="btn" type="submit" style="margin-top: 20px;">Lưu Sinh viên & Tạo Tài khoản</button>
</form>
