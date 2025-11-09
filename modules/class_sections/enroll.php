<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
$id = $_GET['id'];

// Lấy thông tin lớp học
$class = $conn->query("SELECT name FROM class_sections WHERE id=$id")->fetch_assoc();

// === PHẦN MỚI: Lấy danh sách ID sinh viên ĐÃ GHI DANH ===
$enrolled_ids = []; // Mảng để lưu các ID
$result_enrolled = $conn->query("SELECT student_id FROM class_enrollments WHERE class_section_id = $id");
while ($row = $result_enrolled->fetch_assoc()) {
    $enrolled_ids[] = $row['student_id'];
}
// Giờ $enrolled_ids sẽ là mảng [1, 5, 12] (ví dụ)

// Lấy TẤT CẢ sinh viên
$students = $conn->query("SELECT id, name FROM students ORDER BY name");
?>

<div class="container">
    <h2>Ghi danh sinh viên vào lớp: <?= htmlspecialchars($class['name']) ?></h2>
    <form method="post" action="?url=class_sections/process_enroll">
        <input type="hidden" name="class_id" value="<?= $id ?>">
        
        <?php while($s = $students->fetch_assoc()): ?>
            <?php
            // === THAY ĐỔI: Kiểm tra xem sinh viên này có trong mảng $enrolled_ids không ===
            $is_checked = in_array($s['id'], $enrolled_ids);
            ?>
            
            <div style="padding: 2px 0;"> <label>
                    <input type="checkbox" name="student_ids[]" value="<?= $s['id'] ?>" <?= $is_checked ? 'checked' : '' ?>>
                    <?= htmlspecialchars($s['name']) ?> (ID: <?= $s['id'] ?>)
                </label>
            </div>
        <?php endwhile; ?>
        
        <br>
        <button type="submit" class="btn btn-success">Lưu ghi danh</button>
        <a href="?url=class_sections" class="btn btn-secondary">Hủy</a>
    </form>
</div>