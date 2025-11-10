<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

// 1. LẤY DỮ LIỆU TỪ FORM (DÙNG $_POST, KHÔNG DÙNG $_GET)
// Đây là nơi lỗi cũ của bạn phát sinh (dòng 5)
if (!isset($_POST['class_id'])) {
    die("Lỗi: Không tìm thấy ID lớp học.");
}
$class_section_id = $_POST['class_id'];
$student_ids = $_POST['student_ids'] ?? []; // Lấy danh sách ID sinh viên, nếu không có thì là mảng rỗng

// Bảo mật: Ép kiểu ID sang số nguyên
$safe_class_id = (int) $class_section_id;

// 2. XÓA GHI DANH CŨ
$conn->query("DELETE FROM class_enrollments WHERE class_section_id = $safe_class_id");

// 3. GHI DANH MỚI
// Chỉ chạy nếu có sinh viên được chọn
if (!empty($student_ids)) {
    $stmt = $conn->prepare("INSERT INTO class_enrollments (class_section_id, student_id) VALUES (?, ?)");
    
    foreach ($student_ids as $sid) {
        $safe_sid = (int) $sid; // Bảo mật
        $stmt->bind_param("ii", $safe_class_id, $safe_sid);
        $stmt->execute();
    }
    $stmt->close(); // Đóng statement
}

// 4. LẤY THÔNG TIN ĐỂ HIỂN THỊ TRANG THÀNH CÔNG
// Dòng này (số 8 cũ) cũng là nơi gây lỗi vì dùng biến $id không tồn tại
$classInfo = $conn->query("
    SELECT cs.id, cs.name, c.name AS course_name, s.name AS semester_name
    FROM class_sections cs
    LEFT JOIN courses c ON cs.course_id = c.id
    LEFT JOIN semesters s ON cs.semester_id = s.id
    WHERE cs.id = $safe_class_id
")->fetch_assoc();

// 5. LẤY DANH SÁCH SINH VIÊN VỪA GHI DANH
// Đây là nơi có lỗi 'st.fullname' (dòng 36 cũ)
$sql = "
    SELECT st.id, st.name AS fullname, st.email, st.phone
    FROM class_enrollments ce
    JOIN students st ON ce.student_id = st.id
    WHERE ce.class_section_id = $safe_class_id
    ORDER BY st.name ASC
";
$result = $conn->query($sql);

// 6. HIỂN THỊ HTML
include __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <h2>✅ Ghi danh thành công</h2>
    
    <?php if ($classInfo): ?>
    <p><strong>Lớp học phần:</strong> <?= htmlspecialchars($classInfo['name']) ?><br>
         <strong>Môn học:</strong> <?= htmlspecialchars($classInfo['course_name']) ?><br>
         <strong>Học kỳ:</strong> <?= htmlspecialchars($classInfo['semester_name']) ?></p>
    <?php else: ?>
    <p>Không tìm thấy thông tin lớp học.</p>
    <?php endif; ?>

    <h3>Danh sách sinh viên đã ghi danh (<?= $result ? $result->num_rows : 0 ?>)</h3>
    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Mã SV</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Điện thoại</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($sv = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $sv['id'] ?></td>
                    <td><?= htmlspecialchars($sv['fullname']) ?></td>
                    <td><?= htmlspecialchars($sv['email'] ?? '') // Thêm ?? '' phòng trường hợp email/phone là NULL ?></td>
                    <td><?= htmlspecialchars($sv['phone'] ?? '') ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Không có sinh viên nào được ghi danh vào lớp này.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="?url=class_sections/enroll&id=<?= $safe_class_id ?>" class="btn btn-secondary">← Quay lại ghi danh</a>
    <a href="?url=class_sections" class="btn btn-primary">🏫 Quay lại danh sách lớp học phần</a>
</div>

<?php 
$conn->close(); // Đóng kết nối CSDL
include __DIR__ . '/../../includes/footer.php'; 
?>