<?php
// includes/functions.php
if (session_status() === PHP_SESSION_NONE) session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /auth/login.php');
        exit;
    }
}

function esc($str) {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function uploadImage($file, $targetDir = __DIR__ . '/../public/uploads/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg','jpeg','png','gif'];
    if (!in_array(strtolower($ext), $allowed)) return null;

    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
    $name = uniqid() . '.' . $ext;
    $target = $targetDir . $name;
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return 'public/uploads/' . $name;
    }
    return null;
}
?>
