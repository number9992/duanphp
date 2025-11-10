<?php
// includes/functions.php

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) session_start();

/**
 * Kiểm tra đăng nhập
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Yêu cầu đăng nhập, nếu chưa sẽ chuyển hướng
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /auth/login.php');
        exit;
    }
}

/**
 * Hàm escape để bảo vệ XSS
 */
if (!function_exists('esc')) {
    function esc(?string $str): string {
        return htmlspecialchars($str??'', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

/**
 * Upload hình ảnh, trả về đường dẫn nếu thành công, null nếu thất bại
 */
function uploadImage(array $file, string $targetDir = __DIR__ . '/../public/uploads/'): ?string {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($ext, $allowed)) return null;

    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
    
    $filename = uniqid() . '.' . $ext;
    $target = $targetDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return 'public/uploads/' . $filename;
    }
    
    return null;
}

/**
 * Lấy ID giáo viên đang đăng nhập
 */
function getLoggedInTeacherId(): int {
    if (!isLoggedIn()) return 0;
    $role = $_SESSION['role'] ?? '';
    return ($role === 'teacher') ? intval($_SESSION['user_id']) : 0;
}

/**
 * Lấy danh sách học sinh theo lớp
 */
function getStudentsByClass(mysqli $conn, int $class_id): array {
    $stmt = $conn->prepare("SELECT * FROM students WHERE class_id=? ORDER BY name");
    $stmt->bind_param('i', $class_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Lấy danh sách lớp mà giáo viên dạy hoặc chủ nhiệm
 */
function getClassesByTeacher(mysqli $conn, int $teacher_id): array {
    $stmt = $conn->prepare("
        SELECT c.id, c.class_name, c.grade_level
        FROM classes c
        LEFT JOIN class_subjects cs ON cs.class_id=c.id
        WHERE cs.teacher_id=? OR c.homeroom_teacher_id=?
        GROUP BY c.id
        ORDER BY c.grade_level, c.class_name
    ");
    $stmt->bind_param('ii', $teacher_id, $teacher_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Lấy danh sách môn học của lớp, có thể lọc theo giáo viên
 */
function getSubjectsByClass(mysqli $conn, int $class_id, ?int $teacher_id = null): array {
    $sql = "
        SELECT cs.id AS class_subject_id, s.subject_name, t.name AS teacher_name
        FROM class_subjects cs
        LEFT JOIN subjects s ON cs.subject_id=s.id
        LEFT JOIN teachers t ON cs.teacher_id=t.id
        WHERE cs.class_id=?
    ";
    if ($teacher_id !== null) $sql .= " AND cs.teacher_id=?";
    
    $stmt = $conn->prepare($sql);
    if ($teacher_id !== null) $stmt->bind_param('ii', $class_id, $teacher_id);
    else $stmt->bind_param('i', $class_id);
    
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Lấy điểm của học sinh theo môn học (class_subject_id)
 */
function getGrades(mysqli $conn, int $class_subject_id): array {
    $stmt = $conn->prepare("
        SELECT g.id, g.student_id, g.kt1, g.kt2, g.final_exam, g.grade, s.name AS student_name
        FROM grades g
        LEFT JOIN students s ON g.student_id=s.id
        WHERE g.class_subject_id=?
        ORDER BY s.name
    ");
    $stmt->bind_param('i', $class_subject_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Lấy lịch dạy của giáo viên từ bảng timetables
 */
function getTeacherTimetable(mysqli $conn, int $teacher_id): array {
    $stmt = $conn->prepare("
        SELECT tt.id, tt.class_id, c.class_name, tt.subject_id, s.subject_name,
               tt.semester, tt.day_of_week, tt.session, tt.period, tt.room
        FROM timetables tt
        JOIN classes c ON tt.class_id = c.id
        JOIN subjects s ON tt.subject_id = s.id
        WHERE tt.teacher_id=?
        ORDER BY FIELD(tt.day_of_week,'Mon','Tue','Wed','Thu','Fri','Sat'),
                 FIELD(tt.session,'Sáng','Chiều'), tt.period
    ");
    $stmt->bind_param('i', $teacher_id);
    $stmt->execute();
    
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $timetable = [];
    foreach ($rows as $row) {
        $day = $row['day_of_week'];
        $session = $row['session'];
        $period = $row['period'];
        $timetable[$day][$session][$period] = [
            'class_name' => $row['class_name'],
            'subject_name' => $row['subject_name'],
            'semester' => $row['semester'],
            'room' => $row['room']
        ];
    }
    
    return $timetable;
}
?>
