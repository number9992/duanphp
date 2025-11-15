<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = intval($_GET['id'] ?? 0); // Đây là teacher_id

if ($id) {
    // Bắt đầu Transaction để đảm bảo cả hai thao tác xóa đều thành công
    $conn->begin_transaction();

    try {
        // 1. Tìm user_id liên quan trong bảng users
        // (Liên kết qua teacher_id)
        $stmt_select = $conn->prepare("SELECT id FROM users WHERE teacher_id = ?");
        $stmt_select->bind_param('i', $id);
        $stmt_select->execute();
        $user_data = $stmt_select->get_result()->fetch_assoc();
        
        $user_id = $user_data['id'] ?? null;

        // 2. Xóa hồ sơ giảng viên (teachers)
        $stmt_teacher = $conn->prepare("DELETE FROM teachers WHERE id = ?");
        $stmt_teacher->bind_param('i', $id);
        if (!$stmt_teacher->execute()) {
             throw new Exception("Lỗi khi xóa hồ sơ giảng viên.");
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
        // Xử lý thông báo lỗi (ví dụ: ghi log hoặc hiển thị thông báo)
        error_log("Lỗi xóa giảng viên: " . $e->getMessage());
        // Tùy chọn: Chuyển hướng với thông báo lỗi
        // header('Location: ?url=teacher&error=' . urlencode($e->getMessage()));
        // exit;
    }
}

header('Location: ?url=teacher');
exit;