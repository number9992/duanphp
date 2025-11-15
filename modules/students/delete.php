<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);

if ($id) {
    // Bắt đầu Transaction
    $conn->begin_transaction();

    try {
        // 1. Tìm student_id và username liên quan để xóa tài khoản USER
        // (Giả định: student_id trong bảng users bằng id trong bảng students)
        $stmt_select = $conn->prepare("SELECT u.id as user_id FROM users u JOIN students s ON u.student_id = s.id WHERE s.id = ?");
        $stmt_select->bind_param('i', $id);
        $stmt_select->execute();
        $user_data = $stmt_select->get_result()->fetch_assoc();
        
        $user_id = $user_data['user_id'] ?? null;

        // 2. Xóa hồ sơ sinh viên (students)
        $stmt_student = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt_student->bind_param('i', $id);
        if (!$stmt_student->execute()) {
             throw new Exception("Lỗi khi xóa hồ sơ sinh viên.");
        }

        // 3. Xóa tài khoản người dùng (users) nếu tìm thấy
        if ($user_id) {
            $stmt_user = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt_user->bind_param('i', $user_id);
            if (!$stmt_user->execute()) {
                 throw new Exception("Lỗi khi xóa tài khoản người dùng.");
            }
        }
        
        // Commit transaction nếu cả hai thao tác thành công
        $conn->commit();

    } catch (Exception $e) {
        $conn->rollback();
        // Ghi lại lỗi hoặc xử lý thông báo lỗi nếu cần
        error_log("Lỗi xóa người dùng: " . $e->getMessage());
        // Có thể thêm thông báo lỗi cho người dùng tại đây
    }
}

header('Location: ?url=student');
exit;