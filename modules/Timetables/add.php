<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();

$classes = $conn->query("SELECT * FROM classes");
$subjects = $conn->query("SELECT * FROM subjects");
$teachers = $conn->query("SELECT * FROM teachers");

// Các giá trị mặc định cho form
$semesters = ['1', '2'];
$days_of_week = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri']; // Phân bổ T2-T6
$sessions = ['Sáng', 'Chiều'];
$periods = range(1, 10); // Các tiết học

// --- HÀM XỬ LÝ PHÂN BỔ LỊCH ---
function generateTimetable($conn, $class_id, $subject_id, $teacher_id, $semester, $room, $total_credits) {
    global $days_of_week, $sessions, $periods;
    $remaining_credits = $total_credits;
    $errors = [];
    $records_created = 0;

    // Bắt đầu từ đầu tuần/tiết
    $day_index = 0;
    $period_index = 0;
    
    // Tự động phân bổ tiết học cho đến khi hết tín chỉ
    while ($remaining_credits > 0 && $records_created < 100) { // Giới hạn vòng lặp an toàn
        
        $day = $days_of_week[$day_index];
        $session = $sessions[floor($period_index / count($periods)) % count($sessions)];
        $period = $periods[$period_index % count($periods)];

        // --- 1. KIỂM TRA XUNG ĐỘT THỜI GIAN TRONG CHÍNH HÀM NÀY ---
        // (Đây là phần quan trọng nhất để logic hoạt động)
        $is_conflicted = false;
        
        // Kiểm tra xung đột Lớp (UNIQUE KEY: class_id, day, session, period, semester)
        $stmt_check_class = $conn->prepare("SELECT 1 FROM timetables WHERE class_id = ? AND day_of_week = ? AND session = ? AND period = ? AND semester = ? LIMIT 1");
        $stmt_check_class->bind_param('issis', $class_id, $day, $session, $period, $semester);
        $stmt_check_class->execute();
        if ($stmt_check_class->get_result()->num_rows > 0) $is_conflicted = true;
        $stmt_check_class->close();
        
        // Kiểm tra xung đột GV (teacher_id)
        if (!$is_conflicted) {
            $stmt_check_teacher = $conn->prepare("SELECT 1 FROM timetables WHERE teacher_id = ? AND day_of_week = ? AND session = ? AND period = ? AND semester = ? LIMIT 1");
            $stmt_check_teacher->bind_param('issis', $teacher_id, $day, $session, $period, $semester);
            $stmt_check_teacher->execute();
            if ($stmt_check_teacher->get_result()->num_rows > 0) $is_conflicted = true;
            $stmt_check_teacher->close();
        }
        
        // Kiểm tra xung đột Phòng (room)
        if (!$is_conflicted && !empty($room)) {
            $stmt_check_room = $conn->prepare("SELECT 1 FROM timetables WHERE room = ? AND day_of_week = ? AND session = ? AND period = ? AND semester = ? LIMIT 1");
            $stmt_check_room->bind_param('sssis', $room, $day, $session, $period, $semester);
            $stmt_check_room->execute();
            if ($stmt_check_room->get_result()->num_rows > 0) $is_conflicted = true;
            $stmt_check_room->close();
        }
        
        // --- 2. THỰC HIỆN INSERT NẾU KHÔNG CÓ XUNG ĐỘT ---
        if (!$is_conflicted) {
            $stmt_insert = $conn->prepare("INSERT INTO timetables (class_id, subject_id, teacher_id, semester, day_of_week, session, period, room) VALUES (?,?,?,?,?,?,?,?)");
            $stmt_insert->bind_param('iiisssis', $class_id, $subject_id, $teacher_id, $semester, $day, $session, $period, $room);
            
            if ($stmt_insert->execute()) {
                $remaining_credits--;
                $records_created++;
            } else {
                 $errors[] = "Lỗi DB khi chèn lịch tại $day, Tiết $period.";
            }
            $stmt_insert->close();
        } 
        
        // --- 3. DI CHUYỂN ĐẾN TIẾT HỌC KẾ TIẾP ---
        $period_index++;
        if ($period_index >= count($periods) * count($sessions)) {
            $period_index = 0;
            $day_index = ($day_index + 1) % count($days_of_week);
        }
        
        // Nếu đã duyệt hết tuần mà vẫn chưa chèn được, thoát
        if ($day_index === 0 && $records_created === 0 && $remaining_credits > 0 && $period_index === 0) {
             $errors[] = "Không thể phân bổ lịch, toàn bộ tuần đã bị bận.";
             break;
        }
    }

    if ($remaining_credits > 0 && empty($errors)) {
         $errors[] = "Chỉ phân bổ được " . $records_created . " tiết. Còn thiếu " . $remaining_credits . " tiết do tuần đã đầy.";
    } elseif (!empty($errors)) {
         $errors[] = "Phân bổ thành công " . $records_created . " tiết. Đã xảy ra lỗi.";
    } else {
         $errors[] = "Phân bổ thành công " . $records_created . " tiết.";
    }
    
    return $errors;
}
// --- KẾT THÚC HÀM XỬ LÝ PHÂN BỔ LỊCH ---


if($_SERVER['REQUEST_METHOD']==='POST'){
    $class_id = intval($_POST['class_id'] ?? 0);
    $subject_id = intval($_POST['subject_id'] ?? 0);
    $teacher_id = intval($_POST['teacher_id'] ?? 0);
    $semester = trim($_POST['semester'] ?? '');
    $room = trim($_POST['room'] ?? '');

    // Lấy số tín chỉ
    $stmt_credits = $conn->prepare("SELECT credit_hours FROM subjects WHERE id = ?");
    $stmt_credits->bind_param('i', $subject_id);
    $stmt_credits->execute();
    $credits = $stmt_credits->get_result()->fetch_assoc()['credit_hours'] ?? 0;
    $stmt_credits->close();

    if($class_id && $subject_id && $teacher_id && $semester && $credits > 0){
        
        $conn->begin_transaction();
        $results = generateTimetable($conn, $class_id, $subject_id, $teacher_id, $semester, $room, $credits);

        if (count($results) === 1 && strpos($results[0], 'thành công') !== false) {
             $conn->commit();
             $_SESSION['success_msg'] = $results[0];
             header('Location:?url=timetables'); exit;
        } else {
             $conn->rollback();
             $err = implode('<br>', $results);
        }
    } else $err = "Vui lòng chọn đủ Lớp, Môn, GV, Học kỳ và đảm bảo Môn học có Tín chỉ (> 0).";
}

$post = $_POST ?? []; 
include __DIR__.'/../../includes/header.php';
?>

<style>
/* ... (Phần CSS giữ nguyên) ... */
</style>

<h2>Tạo Thời khóa biểu Tự động</h2>
<?php 
if(isset($err)) echo "<p style='color:red'>". $err ."</p>"; 
// Hiển thị thông báo thành công sau khi chuyển hướng
if (isset($_SESSION['success_msg'])) {
    echo "<p style='color:green; text-align:center;'>". $_SESSION['success_msg'] ."</p>";
    unset($_SESSION['success_msg']);
}
?>

<form method="post">
    <div class="form-grid">
        <div class="form-row">
            <label>Lớp (*)</label>
            <select name="class_id" required>
                <option value="">-- Chọn Lớp --</option>
                <?php $classes->data_seek(0); while($c=$classes->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>" <?= (int)($post['class_id'] ?? 0) === $c['id'] ? 'selected' : '' ?>>
                    <?= esc($c['class_name']) ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-row">
            <label>Môn học (*)</label>
            <select name="subject_id" required>
                <option value="">-- Chọn Môn --</option>
                <?php $subjects->data_seek(0); while($s=$subjects->fetch_assoc()): ?>
                <option value="<?= $s['id'] ?>" <?= (int)($post['subject_id'] ?? 0) === $s['id'] ? 'selected' : '' ?>>
                    <?= esc($s['subject_name']) ?> (<?= $s['credit_hours'] ?> tín chỉ)
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-row">
            <label>Giảng viên (*)</label>
            <select name="teacher_id" required>
                <option value="">-- Chọn GV --</option>
                <?php $teachers->data_seek(0); while($t=$teachers->fetch_assoc()): ?>
                <option value="<?= $t['id'] ?>" <?= (int)($post['teacher_id'] ?? 0) === $t['id'] ? 'selected' : '' ?>>
                    <?= esc($t['name']) ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-row">
            <label>Học kỳ (*)</label>
            <select name="semester" required>
                 <option value="">-- Chọn Học kỳ --</option>
                 <?php foreach($semesters as $sem): ?>
                 <option value="<?= $sem ?>" <?= ($post['semester'] ?? '') === $sem ? 'selected' : '' ?>>
                    Học kỳ <?= $sem ?>
                 </option>
                 <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-row">
            <label>Phòng học (Tùy chọn)</label>
            <input type="text" name="room" value="<?= esc($post['room'] ?? '') ?>" placeholder="Để trống nếu không cố định">
        </div>

        <div class="form-row">
             <label style="color: #c0392b; font-weight: 600; margin-top: 25px;">⚠ Lưu ý về Tín chỉ:</label>
             <small>Hệ thống sẽ tự động phân bổ **tổng số tiết** của Môn học vào các ngày trống, tránh xung đột GV, Phòng và Lớp.</small>
        </div>
    </div>
    
    <button type="submit">Tự động Phân bổ & Lưu Thời khóa biểu</button>
</form>

<?php include __DIR__.'/../../includes/footer.php'; ?>