<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$err = '';
$msg = '';
$default_password = '123456'; // Password mặc định cho giáo viên

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $name     = trim($_POST['name'] ?? '');
    $role_id  = (int)($_POST['role_id'] ?? 0);

    if ($username && $name) {
        // Kiểm tra role_id hợp lệ
        $stmt_role = $conn->prepare("SELECT id, role_name FROM roles WHERE id = ? LIMIT 1");
        $stmt_role->bind_param('i', $role_id);
        $stmt_role->execute();
        $result_role = $stmt_role->get_result()->fetch_assoc();

        if (!$result_role) {
            $row = $conn->query("SELECT id, role_name FROM roles ORDER BY id ASC LIMIT 1")->fetch_assoc();
            $role_id = $row['id'] ?? 0;
            $role_name = $row['role_name'] ?? '';
        } else {
            $role_name = $result_role['role_name'];
        }

        // Kiểm tra username đã tồn tại chưa
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_check->bind_param('s', $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $err = "Username đã tồn tại.";
        } else {
            $conn->begin_transaction();
            try {
                $teacher_id = null;

                if ($role_name === 'teacher') {
                    // Tạo giáo viên trong bảng teachers
                    $stmt_teacher = $conn->prepare("INSERT INTO teachers (name) VALUES (?)");
                    $stmt_teacher->bind_param('s', $name);
                    $stmt_teacher->execute();
                    $teacher_id = $conn->insert_id;

                    // Nếu password rỗng, đặt mặc định
                    if (!$password) $password = $default_password;
                }

                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt_user = $conn->prepare("INSERT INTO users (username, password, name, role_id, teacher_id) VALUES (?, ?, ?, ?, ?)");
                $stmt_user->bind_param('sssii', $username, $hash, $name, $role_id, $teacher_id);
                $stmt_user->execute();

                $conn->commit();
                $msg = "Đăng ký user thành công.";
            } catch (Exception $e) {
                $conn->rollback();
                $err = "Lỗi: " . $e->getMessage();
            }
        }
    } else {
        $err = "Username và Tên bắt buộc.";
    }
}

// Lấy danh sách role từ DB để hiển thị select
$roles = $conn->query("SELECT id, role_name FROM roles ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<h2>Đăng ký user mới</h2>

<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; margin:0; padding:0; }
h2 { text-align:center; margin-top:40px; color:#333; }
form { max-width:450px; margin:30px auto; padding:25px; background:#fff; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.form-row { margin-bottom:20px; }
.form-row label { display:block; margin-bottom:6px; font-weight:600; color:#555; }
.form-row input, .form-row select { width:100%; padding:10px 12px; border:1px solid #ccc; border-radius:6px; font-size:14px; }
.btn { width:100%; padding:12px; background-color:#28a745; color:white; border:none; border-radius:6px; font-size:16px; font-weight:bold; cursor:pointer; transition: background 0.3s;}
.btn:hover { background-color:#218838; }
.notice { text-align:center; color:#28a745; font-weight:bold; margin-top:10px; }
p[style="color:red"] { text-align:center; font-weight:bold; margin-top:10px; }
</style>

<?php if($msg): ?><p class="notice"><?= esc($msg) ?></p><?php endif; ?>
<?php if($err): ?><p style="color:red"><?= esc($err) ?></p><?php endif; ?>

<form method="post">
    <div class="form-row">
        <label>Username</label>
        <input name="username" required value="<?= esc($_POST['username'] ?? '') ?>">
    </div>
    <div class="form-row">
        <label>Password</label>
        <input name="password" type="password" placeholder="Mặc định: 123456 nếu để trống">
    </div>
    <div class="form-row">
        <label>Tên</label>
        <input name="name" required value="<?= esc($_POST['name'] ?? '') ?>">
    </div>
    <div class="form-row">
        <label>Role</label>
        <select name="role_id" required>
            <?php foreach ($roles as $role): ?>
                <option value="<?= $role['id'] ?>" <?= (($_POST['role_id'] ?? '') == $role['id'] ? 'selected' : '') ?>><?= esc($role['role_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="btn">Tạo user</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
