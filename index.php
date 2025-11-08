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

    case 'teacher/delete':
        include 'modules/teachers/delete.php';
        break;

    case 'courses':
        include 'modules/courses/list.php';
        break;
    case 'courses/add':
        include 'modules/courses/add.php';
        break;
    case 'courses/edit':
        include 'modules/courses/edit.php';
        break;

    case 'courses/delete':
        include 'modules/courses/delete.php';
        break;

        case 'scores':
        include 'modules/scores/list.php';
        break;
    case 'scores/add':
        include 'modules/scores/add.php';
        break;
    case 'scores/edit':
        include 'modules/scores/edit.php';
        break;

    case 'scores/delete':
        include 'modules/scores/delete.php';
        break;
    case 'semesters':
        include 'modules/semesters/list.php';
        break;
    case 'semesters/add':
        include 'modules/semesters/add.php';
        break;
    case 'class_sections':
        include 'modules/class_sections/list.php';
        break;
    case 'class_sections/add':
        include 'modules/class_sections/add.php';
        break;
    case 'class_sections/enroll':
        include 'modules/class_sections/enroll.php';
        break;
    case 'class_sections/process_enroll':
         include 'modules/class_sections/process_enroll.php';
         break;

    case 'logout':
        include '/logout.php';
        break;
    

    default:
        echo "<div class='container mt-4'><h2 class='text-danger'>404 - Trang không tồn tại</h2></div>";
        break;
}


?>
