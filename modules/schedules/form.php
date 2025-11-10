<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
include __DIR__ . '/../../includes/header.php';

// ✅ Kiểm tra và lấy class_section_id
if (!isset($_GET['class_section_id']) || !is_numeric($_GET['class_section_id'])) {
    die("<p style='color:red'>❌ Thiếu hoặc sai mã lớp học phần!</p>");
}
$class_section_id = intval($_GET['class_section_id']);
?>

<div class="container">
  <h2>➕ Thêm lịch học cho lớp học phần #<?= $class_section_id ?></h2>

  <form action="?url=schedules/process_save" method="POST" 
        style="max-width: 400px; margin-top: 20px;"
        onsubmit="return confirm('Xác nhận lưu lịch học này?')">

    <input type="hidden" name="class_section_id" value="<?= $class_section_id ?>">

    <label><strong>Thứ trong tuần:</strong></label><br>
    <select name="day_of_week" required style="width:100%; padding:6px; margin-top:5px;">
      <option value="Monday">Thứ 2</option>
      <option value="Tuesday">Thứ 3</option>
      <option value="Wednesday">Thứ 4</option>
      <option value="Thursday">Thứ 5</option>
      <option value="Friday">Thứ 6</option>
      <option value="Saturday">Thứ 7</option>
      <option value="Sunday">Chủ nhật</option>
    </select><br><br>

    <label><strong>Giờ bắt đầu:</strong></label><br>
    <input type="time" name="start_time" required style="width:100%; padding:6px;"><br><br>

    <label><strong>Giờ kết thúc:</strong></label><br>
    <input type="time" name="end_time" required style="width:100%; padding:6px;"><br><br>

    <label><strong>Phòng học:</strong></label><br>
    <input type="text" name="room_number" placeholder="VD: B204" required
           style="width:100%; padding:6px;"><br><br>

    <button type="submit" class="btn btn-primary">💾 Lưu lịch học</button>
    <a href="?url=schedules/list&class_section_id=<?= $class_section_id ?>" class="btn btn-secondary">⬅ Quay lại</a>
  </form>
</div>
