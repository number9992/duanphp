<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$err = '';
$default_password = '123456'; // Password mặc định cho giáo viên

// Lấy Role ID của Giáo viên từ bảng roles
$stmt_role = $conn->prepare("SELECT id FROM roles WHERE role_name = 'teacher' LIMIT 1");
$stmt_role->execute();
$TEACHER_ROLE_ID = $stmt_role->get_result()->fetch_assoc()['id'] ?? 5;
$stmt_role->close();


// --- HÀM TẠO MÃ GIẢNG VIÊN TỰ ĐỘNG ---
function generateUniqueTeacherCode($conn) {
    // Định dạng: GVYYXXXX (GV + 2 số cuối năm hiện tại + 4 số thứ tự)
    $year = date('y'); 
    
    $stmt = $conn->prepare("
        SELECT teacher_code FROM teachers 
        WHERE teacher_code LIKE CONCAT('GV', ?, '%') 
        ORDER BY teacher_code DESC LIMIT 1
    ");
    $stmt->bind_param('s', $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $latest_code = $result->fetch_assoc()['teacher_code'] ?? null;
    $stmt->close();
    
    if ($latest_code) {
        $sequence = (int) substr($latest_code, 4) + 1;
    } else {
        $sequence = 1;
    }

    $new_sequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);
    return "GV" . $year . $new_sequence;
}
// --- KẾT THÚC HÀM TẠO MÃ GIẢNG VIÊN ---

$generated_teacher_code = generateUniqueTeacherCode($conn); // Tạo MGV trước khi xử lý POST

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // === 1. LẤY DỮ LIỆU GIẢNG VIÊN ===
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $photo = uploadImage($_FILES['photo'] ?? null); 

    // MGV TỰ ĐỘNG CHÍNH LÀ USERNAME
    $teacher_code = $_POST['teacher_code'] ?? $generated_teacher_code;
    $username = $teacher_code; 
    $password = $_POST['password'] ?? $default_password;

    if (empty($name) || empty($department) || empty($password)) {
        $err = "Họ tên, Khoa và Mật khẩu là bắt buộc.";
    } elseif (strlen($password) < 6) {
        $err = "Mật khẩu phải ít nhất 6 ký tự.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $conn->begin_transaction();

        try {
            // 1. Thêm vào bảng teachers (Thêm teacher_code)
            $stmt_teacher = $conn->prepare("INSERT INTO teachers (name, teacher_code, email, phone, department, photo) VALUES (?, ?, ?, ?, ?, ?)");
            // 's' 's' 's' 's' 's' 's' (name, code, email, phone, department, photo)
            $stmt_teacher->bind_param('ssssss', $name, $teacher_code, $email, $phone, $department, $photo);
            
            if (!$stmt_teacher->execute()) {
                 throw new Exception("Lỗi khi thêm giảng viên (Code có thể trùng lặp).");
            }
            $teacher_id = $conn->insert_id;

            // 2. Thêm vào bảng users
            $stmt_user = $conn->prepare("INSERT INTO users (username, password, name, role_id, teacher_id) VALUES (?, ?, ?, ?, ?)");
            $stmt_user->bind_param('sssii', $username, $hashed_password, $name, $TEACHER_ROLE_ID, $teacher_id);
            
            if (!$stmt_user->execute()) {
                 throw new Exception("Lỗi khi tạo tài khoản.");
            }

            $conn->commit();
            header('Location:?url=teacher');
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $err = "Lỗi: " . $e->getMessage();
        }
    }
    
    $generated_teacher_code = generateUniqueTeacherCode($conn);
}

include __DIR__ . '/../../includes/header.php';
?>

<style>
/* Tối ưu CSS cho UX/UI - Áp dụng bố cục 2 cột */
body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f0f2f5; color: #333; }
h2 { text-align: center; margin: 30px 0; color: #2c3e50; font-size: 28px; border-bottom: 2px solid #3498db; padding-bottom: 10px; display: inline-block; width: 100%; }

form { max-width: 800px; margin: 0 auto; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); }

/* Bố cục 2 cột chính */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 30px; margin-bottom: 20px; }
.form-row { display: flex; flex-direction: column; margin-bottom: 0; }
.form-row label { font-weight: 600; margin-bottom: 8px; color: #34495e; font-size: 14px; }

/* ĐỒNG NHẤT CHIỀU CAO INPUT/SELECT/FILE */
.form-row input[type="text"],
.form-row input[type="email"],
.form-row input[type="password"],
.form-row select { 
    padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 16px; background-color: #f9f9f9; 
    height: 44px; box-sizing: border-box; 
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-row input[type="file"] {
    padding: 8px 12px; height: 44px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 6px; background-color: #f9f9f9;
}

.form-row input:focus, .form-row select:focus { border-color: #3498db; box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2); outline: none; }

.full-width { grid-column: 1 / -1; margin-top: 10px; }

/* MGV box */
.mgv-info {
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
    background-color: #2c3e50; 
    color: white;
    font-size: 18px;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 20px;
}

button.btn:hover {
    background-color: #34495e;
}
.error { color: red; text-align: center; font-weight: bold; margin-bottom: 20px; }
</style>

<h2>Thêm Giảng viên & Tạo Tài khoản</h2>

<?php if(isset($err)): ?><p class="error"><?= esc($err) ?></p><?php endif; ?>

<form method="post" enctype="multipart/form-data">
    
    <div class="mgv-info">
        Mã Giảng viên Tự động (Username): 
        <span style="color:#2c3e50; font-weight:bold;"><?= esc($generated_teacher_code) ?></span>
        <input type="hidden" name="teacher_code" value="<?= esc($generated_teacher_code) ?>">
    </div>
    
    <div class="form-grid">
        <div class="form-row"><label>Họ tên (*)</label><input name="name" type="text" required value="<?= esc($_POST['name'] ?? '') ?>"></div>
        <div class="form-row"><label>Phone</label><input name="phone" type="text" value="<?= esc($_POST['phone'] ?? '') ?>"></div>
        <div class="form-row"><label>Password (*)</label><input name="password" type="password" required minlength="6"></div>
        
        <div class="form-row"><label>Email</label><input name="email" type="email" value="<?= esc($_POST['email'] ?? '') ?>"></div>
        <div class="form-row">
            <label>Khoa (*)</label>
            <input name="department" type="text" required value="<?= esc($_POST['department'] ?? '') ?>">
        </div>
        <div class="form-row">
             </div>
    </div>
    
    <div class="form-row full-width">
        <label>Ảnh đại diện</label>
        <input name="photo" type="file" accept="image/*">
    </div>

    <button class="btn" type="submit">Lưu Giảng viên & Tạo User</button>
</form>

<?php include __DIR__ . '/../../includes/footer.php'; ?>