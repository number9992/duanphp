<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$teachersRes = $conn->query("SELECT id, name FROM teachers ORDER BY name ASC");

// --- Hàm tạo danh sách năm học cho SELECT ---
function getSchoolYears($currentYear) {
    $years = [];
    $startYear = (int)$currentYear - 2; // Bắt đầu từ 2 năm trước
    for ($i = 0; $i < 5; $i++) { // Liệt kê 5 năm liên tiếp
        $year1 = $startYear + $i;
        $year2 = $year1 + 1;
        $years[] = "$year1-$year2";
    }
    return $years;
}
// Mặc định năm hiện tại (Ví dụ: 2025)
$current_school_year = date('Y') . '-' . (date('Y') + 1); 
$school_years = getSchoolYears(date('Y'));
// ---------------------------------------------


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = trim($_POST['class_name'] ?? '');
    $grade_level = intval($_POST['grade_level'] ?? 0);
    $homeroom_teacher_id = intval($_POST['homeroom_teacher_id'] ?? 0) ?: null;
    $school_year = trim($_POST['school_year'] ?? '');

    if ($class_name && $grade_level && $school_year) {
        $stmt = $conn->prepare("INSERT INTO classes (class_name, grade_level, homeroom_teacher_id, school_year) VALUES (?,?,?,?)");
        $stmt->bind_param('siss', $class_name, $grade_level, $homeroom_teacher_id, $school_year);
        
        if ($stmt->execute()) {
            header('Location: ?url=class');
            exit;
        } else {
            // ✅ TÁI TÍCH HỢP LOGIC XỬ LÝ LỖI TRÙNG LẶP (ERROR CODE 1062)
            if ($conn->errno === 1062) {
                $err = "Lỗi: Tên lớp ' " . esc($class_name) . " ' đã tồn tại trong năm học này.";
            } else {
                // Các lỗi DB khác
                $err = $stmt->error;
            }
        }
    } else $err = "Vui lòng điền đầy đủ thông tin.";
}

include __DIR__ . '/../../includes/header.php';
?>

<style>
/* Tối ưu CSS cho UX/UI */
body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f0f2f5; color: #333; }
h2 { text-align: center; margin: 30px 0; color: #2c3e50; font-size: 28px; border-bottom: 2px solid #3498db; padding-bottom: 10px; display: inline-block; width: 100%; }

form {
    max-width: 700px;
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

.form-row {
    display: flex;
    flex-direction: column;
    margin-bottom: 0; 
}

.form-row label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #34495e;
    font-size: 14px;
}

/* ĐỒNG NHẤT CHIỀU CAO INPUT/SELECT */
.form-row input[type="text"],
.form-row input[type="number"], 
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

.form-row input:focus, .form-row select:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
    outline: none;
}

button.btn {
    display: block;
    width: 100%;
    padding: 14px;
    background-color: #3498db; 
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
    background-color: #2980b9;
}

p[style="color:red"] {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
}
</style>

<h2>Thêm Lớp học Mới</h2>
<?php if(isset($err)): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>

<form method="post">
    <div class="form-grid">
        <div class="form-row">
            <label>Tên lớp (*)</label>
            <input name="class_name" type="text" required value="<?= esc($_POST['class_name'] ?? '') ?>">
        </div>
        
        <div class="form-row">
            <label>Cấp/Khối (*)</label>
            <input name="grade_level" type="number" required value="<?= esc($_POST['grade_level'] ?? '') ?>">
        </div>
        
        <div class="form-row">
            <label>GV Chủ nhiệm</label>
            <select name="homeroom_teacher_id">
                <option value="">-- Chọn giáo viên --</option>
                <?php 
                $selected_teacher = $_POST['homeroom_teacher_id'] ?? '';
                // Di chuyển con trỏ về đầu nếu cần (dùng cho trường hợp lỗi postback)
                $teachersRes->data_seek(0); 
                while($t = $teachersRes->fetch_assoc()): 
                ?>
                    <option value="<?= $t['id'] ?>" <?= ($t['id'] == $selected_teacher) ? 'selected' : '' ?>>
                        <?= esc($t['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-row">
            <label>Năm học (*)</label>
            <select name="school_year" required>
                 <option value="">-- Chọn năm học --</option>
                 <?php 
                 $selected_year = $_POST['school_year'] ?? $current_school_year;
                 foreach($school_years as $year):
                 ?>
                    <option value="<?= $year ?>" <?= ($year == $selected_year) ? 'selected' : '' ?>>
                        <?= $year ?>
                    </option>
                 <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <button class="btn">Lưu Lớp học</button>
</form>

<?php include __DIR__ . '/../../includes/footer.php'; ?>