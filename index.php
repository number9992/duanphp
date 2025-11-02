<?php


$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';

switch ($url) {
    case '':
        include 'views/home.php';
        // include 'config/db.php';

        break;

    case 'login':
        include 'auth/login.php';
        break;

    case 'register':
        include 'auth/register.php';
        break;
    
    case 'dashboard':
        include 'dashboard.php';
        break;

    case 'student':
        include 'modules/students/list.php';
        break;

    case 'student/add':
        include 'modules/students/add.php';
        break;

    case  'student/edit':
        include 'modules/students/edit.php';
        break;
    case  'student/delete':
        include 'modules/students/delete.php';
        break;

    case 'teacher':
        include 'modules/teachers/list.php';
        break;

    case 'teacher/add':
        include 'modules/teachers/add.php';
        break;
    case 'teacher/edit':
        include 'modules/teachers/edit.php';
        break;

    case 'logout':
        include '/logout.php';
        break;

    default:
        echo "<div class='container mt-4'><h2 class='text-danger'>404 - Trang không tồn tại</h2></div>";
        break;
}


?>
