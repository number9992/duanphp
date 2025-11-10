<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../includes/functions.php';
requireLogin();

$class_id = intval($_GET['class_id'] ?? 0);
if(!$class_id) { echo "Không có lớp nào được chọn."; exit; }

// Lấy tên lớp
$stmt = $conn->prepare("SELECT class_name FROM classes WHERE id=?");
$stmt->bind_param('i',$class_id);
$stmt->execute();
$class_name = $stmt->get_result()->fetch_assoc()['class_name'] ?? '';

// Lấy tất cả thời khóa biểu
$sql = "
SELECT tt.*, s.subject_name, t.name as teacher_name
FROM timetables tt
LEFT JOIN subjects s ON tt.subject_id = s.id
LEFT JOIN teachers t ON tt.teacher_id = t.id
WHERE tt.class_id=?
ORDER BY FIELD(tt.day_of_week,'Mon','Tue','Wed','Thu','Fri','Sat'), 
         FIELD(tt.session,'Sáng','Chiều'), 
         tt.period
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i',$class_id);
$stmt->execute();
$res = $stmt->get_result();

// Gom dữ liệu theo thứ -> ca -> tiết
$schedule = [];
$max_period = 0;
while($row = $res->fetch_assoc()){
    $schedule[$row['day_of_week']][$row['session']][$row['period']] = $row;
    if($row['period'] > $max_period) $max_period = $row['period'];
}

$days = ['Mon'=>'Thứ 2','Tue'=>'Thứ 3','Wed'=>'Thứ 4','Thu'=>'Thứ 5','Fri'=>'Thứ 6','Sat'=>'Thứ 7'];

include __DIR__.'/../../includes/header.php';
?>

<h2>Thời khóa biểu lớp <?= esc($class_name) ?></h2>

<div class="timetable-container">
    <table class="timetable">
        <thead>
            <tr>
                <th>Tiết / Thứ</th>
                <?php foreach($days as $day_name): ?>
                    <th><?= $day_name ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Chia sáng / chiều
            foreach(['Sáng','Chiều'] as $session):
                echo "<tr class='session-row'><td colspan='7' class='session-label'>{$session}</td></tr>";
                for($p=1; $p<=$max_period; $p++):
            ?>
            <tr>
                <td><?= $p ?></td>
                <?php foreach(array_keys($days) as $day_code): ?>
                    <td>
                        <?php if(isset($schedule[$day_code][$session][$p])): 
                            $lesson = $schedule[$day_code][$session][$p]; ?>
                            <div class="lesson">
                                <strong><?= esc($lesson['subject_name']) ?></strong><br>
                                <span><?= esc($lesson['teacher_name'] ?? '-') ?></span><br>
                                <span><?= esc($lesson['room'] ?? '-') ?></span>
                            </div>
                        <?php else: ?>
                            <div class="lesson empty"></div>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
            <?php endfor; endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.timetable-container {
    max-width: 1000px;
    margin: 20px auto;
    font-family: 'Segoe UI', Tahoma, sans-serif;
}

.timetable {
    width: 100%;
    border-collapse: collapse;
    text-align: center;
}

.timetable th, .timetable td {
    border: 1px solid #ddd;
    padding: 8px;
    vertical-align: top;
}

.timetable th {
    background-color: #34495e;
    color: white;
    font-weight: 600;
}

.session-row td.session-label {
    background-color: #2c3e50;
    color: white;
    font-weight: bold;
    text-align: left;
    padding-left: 10px;
    font-size: 16px;
}

.lesson {
    padding: 4px;
    border-radius: 4px;
    background-color: #ecf0f1;
    margin: 2px 0;
}

.lesson strong {
    display: block;
    font-weight: 600;
}

.lesson span {
    display: block;
    font-size: 12px;
    color: #555;
}

.lesson.empty {
    height: 40px;
    background-color: #fff;
}
</style>

<?php include __DIR__.'/../../includes/footer.php'; ?>
