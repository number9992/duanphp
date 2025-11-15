<?php
require_once 'includes/functions.php';
requireLogin();
include 'includes/header.php';
require_once 'config/db.php'; // Đảm bảo db.php được include

// Lấy tên người dùng và vai trò từ session
$user_name = esc($_SESSION['name'] ?? 'Khách');
$user_role = esc($_SESSION['role'] ?? '');

// Đếm các mục theo DB hiện có
$counts = [];
$counts['students'] = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'] ?? 0;
$counts['teachers'] = $conn->query("SELECT COUNT(*) as c FROM teachers")->fetch_assoc()['c'] ?? 0;
$counts['subjects'] = $conn->query("SELECT COUNT(*) as c FROM subjects")->fetch_assoc()['c'] ?? 0;
$counts['grades'] = $conn->query("SELECT COUNT(*) as c FROM grades")->fetch_assoc()['c'] ?? 0;

// --- Thêm truy vấn cảnh báo: Sinh viên có điểm tổng kết dưới 4 ---
// Giả định: Điểm tổng kết (g.grade) nằm trong bảng grades và < 4
$low_grade_students_count = 0;
$stmt_low_grades = $conn->prepare("
    SELECT COUNT(DISTINCT g.student_id) as c
    FROM grades g
    WHERE g.grade < 4.0
");
if ($stmt_low_grades) {
    $stmt_low_grades->execute();
    $low_grade_students_count = $stmt_low_grades->get_result()->fetch_assoc()['c'] ?? 0;
    $stmt_low_grades->close();
} else {
    // Xử lý lỗi nếu truy vấn không thành công
    error_log("Failed to prepare statement for low grades: " . $conn->error);
}

// Đóng kết nối DB (nếu chưa đóng ở footer)
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5; /* Light gray background */
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            display: grid;
            gap: 20px;
            grid-template-columns: 2fr 1fr; /* Main content and sidebar/chart */
        }

        .main-content {
            padding-right: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
            font-size: 32px;
            font-weight: 600;
        }

        .welcome-message {
            text-align: center;
            font-size: 18px;
            color: #555;
            margin-bottom: 40px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background-color: #f7f9fc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border-left: 5px solid;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .stat-card.students { border-color: #007bff; } /* Blue */
        .stat-card.teachers { border-color: #28a745; } /* Green */
        .stat-card.subjects { border-color: #ffc107; } /* Yellow */
        .stat-card.grades { border-color: #dc3545; } /* Red */
        .stat-card.warning { border-color: #ff8800; background-color: #fff3cd; } /* Orange for warning */


        .stat-card .icon {
            font-size: 36px;
            margin-bottom: 10px;
            color: #6c757d;
        }
        .stat-card.students .icon { color: #007bff; }
        .stat-card.teachers .icon { color: #28a745; }
        .stat-card.subjects .icon { color: #ffc107; }
        .stat-card.grades .icon { color: #dc3545; }
        .stat-card.warning .icon { color: #ff8800; }


        .stat-card .label {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
            color: #343a40;
        }
        .stat-card.warning .value { color: #ff8800; }

        .chart-container {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .chart-container h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 24px;
        }
        canvas {
            max-width: 90%;
            height: auto;
        }
    </style>
</head>
<body>

    <div class="main-content">
        <h2>Dashboard Quản lý</h2>
        <p class="welcome-message">
            Xin chào: <strong><?= $user_name ?></strong> (Vai trò: <em><?= $user_role ?></em>)
        </p>

        <div class="stats-grid">
            <div class="stat-card students">
                <div class="icon">🎓</div>
                <div class="label">Tổng số Sinh viên</div>
                <div class="value"><?= $counts['students'] ?></div>
            </div>
            <div class="stat-card teachers">
                <div class="icon">👨‍🏫</div>
                <div class="label">Tổng số Giảng viên</div>
                <div class="value"><?= $counts['teachers'] ?></div>
            </div>
            <div class="stat-card subjects">
                <div class="icon">📚</div>
                <div class="label">Tổng số Môn học</div>
                <div class="value"><?= $counts['subjects'] ?></div>
            </div>
            <div class="stat-card grades">
                <div class="icon">📊</div>
                <div class="label">Tổng số Bảng điểm</div>
                <div class="value"><?= $counts['grades'] ?></div>
            </div>
            <?php if ($low_grade_students_count > 0): ?>
            <div class="stat-card warning">
                <div class="icon">⚠️</div>
                <div class="label">Sinh viên có điểm < 4.0</div>
                <div class="value"><?= $low_grade_students_count ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="chart-container">
        <h3>Tổng quan dữ liệu</h3>
        <canvas id="myDoughnutChart"></canvas>
    </div>
</div>

<script>
    // Dữ liệu cho biểu đồ
    const data = {
        labels: [
            'Sinh viên',
            'Giảng viên',
            'Môn học',
            'Bảng điểm'
        ],
        datasets: [{
            data: [
                <?= $counts['students'] ?>,
                <?= $counts['teachers'] ?>,
                <?= $counts['subjects'] ?>,
                <?= $counts['grades'] ?>
            ],
            backgroundColor: [
                '#007bff', // Blue for students
                '#28a745', // Green for teachers
                '#ffc107', // Yellow for subjects
                '#dc3545'  // Red for grades
            ],
            hoverOffset: 4
        }]
    };

    // Cấu hình biểu đồ
    const config = {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: false, // Title moved to H3
                }
            }
        },
    };

    // Render biểu đồ
    var myDoughnutChart = new Chart(
        document.getElementById('myDoughnutChart'),
        config
    );
</script>
