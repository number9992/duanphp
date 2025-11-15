<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();

$sql = "
SELECT tt.id, c.class_name, s.subject_name, t.name as teacher_name, tt.semester, tt.day_of_week, tt.session, tt.period, tt.room
FROM timetables tt
LEFT JOIN classes c ON tt.class_id = c.id
LEFT JOIN subjects s ON tt.subject_id = s.id
LEFT JOIN teachers t ON tt.teacher_id = t.id
ORDER BY tt.class_id, FIELD(tt.day_of_week,'Mon','Tue','Wed','Thu','Fri','Sat'), tt.session, tt.period
";
$res = $conn->query($sql);

include __DIR__.'/../../includes/header.php';

// Ánh xạ ngày tiếng Anh sang tiếng Việt
$day_map = [
    'Mon' => 'Thứ Hai',
    'Tue' => 'Thứ Ba',
    'Wed' => 'Thứ Tư',
    'Thu' => 'Thứ Năm',
    'Fri' => 'Thứ Sáu',
    'Sat' => 'Thứ Bảy',
    'Sun' => 'Chủ Nhật'
];
?>

<style>
/* Tối ưu CSS cho UX/UI */
h2 {
    color: #2c3e50;
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
    margin-bottom: 20px;
    font-size: 24px;
    display: inline-block;
}

.header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

/* Nút chính */
.btn {
    background-color: #3498db;
    color: white;
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
    font-weight: 500;
}

.btn:hover {
    background-color: #2980b9;
}

/* Thiết kế Bảng (Table) */
table {
    width: 100%;
    margin: 0 auto;
    border-collapse: separate; 
    border-spacing: 0;
    background-color: #fff;
    border-radius: 8px; 
    overflow: hidden; 
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* Tiêu đề (Header) */
table th {
    background-color: #2c3e50; 
    color: white;
    font-weight: 600;
    padding: 12px 15px;
    text-align: left;
    border-bottom: 2px solid #243444;
}

/* Căn giữa các cột số và thời gian */
table th:nth-child(1), table td:nth-child(1), /* ID */
table th:nth-child(5), table td:nth-child(5), /* Học kỳ */
table th:nth-child(6), table td:nth-child(6), /* Ngày */
table th:nth-child(7), table td:nth-child(7), /* Ca */
table th:nth-child(8), table td:nth-child(8), /* Tiết */
table th:nth-child(10), table td:nth-child(10) /* Hành động */
{
    text-align: center;
}

/* Các dòng (Rows) */
table td {
    padding: 12px 15px;
    border-bottom: 1px solid #ecf0f1; 
    color: #34495e;
    vertical-align: middle;
    font-size: 14px;
}

/* Hiệu ứng Hover */
table tbody tr:hover {
    background-color: #f7f9fc;
    transition: background-color 0.2s;
}

/* Hành động (Actions) */
table td:last-child {
    text-align: center;
    white-space: nowrap; 
}
table td a {
    margin: 0 5px;
    text-decoration: none;
    font-weight: 500;
    padding: 4px 8px;
    border-radius: 3px;
    transition: opacity 0.2s;
    font-size: 13px;
    color: #3498db;
}
table td a:hover {
    opacity: 0.8;
}
</style>

<div class="header-actions">
    <h2>Danh sách Thời khóa biểu</h2>
    <a class="btn" href="?url=timetables/add">+ Thêm lịch học</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Lớp</th>
            <th>Môn</th>
            <th>Giảng viên</th>
            <th>Học kỳ</th>
            <th>Ngày</th>
            <th>Ca</th>
            <th>Tiết</th>
            <th>Phòng</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = $res->fetch_assoc()): 
        // Áp dụng ánh xạ Thứ
        $display_day = $day_map[$row['day_of_week']] ?? $row['day_of_week'];
    ?>
    <tr>
        <td><?= esc($row['id']) ?></td>
        <td><strong><?= esc($row['class_name']) ?></strong></td>
        <td><?= esc($row['subject_name']) ?></td>
        <td><?= esc($row['teacher_name']) ?></td>
        <td><?= esc($row['semester']) ?></td>
        <td><?= $display_day ?></td> <td><?= esc($row['session']) ?></td>
        <td><?= esc($row['period']) ?></td>
        <td><?= esc($row['room']) ?></td>
        <td>
            <a href="?url=timetables/edit&id=<?= $row['id'] ?>">✏️ Sửa</a>
            <a href="?url=timetables/delete&id=<?= $row['id'] ?>" onclick="return confirm('Xác nhận xóa lịch học này?')">🗑 Xóa</a>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php include __DIR__.'/../../includes/footer.php'; ?>