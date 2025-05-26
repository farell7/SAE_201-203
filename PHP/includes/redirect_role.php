<?php
function redirect_to_role_home() {
    if (!isset($_SESSION['utilisateur'])) {
        header('Location: ../index.php');
        exit();
    }

    switch ($_SESSION['utilisateur']['role']) {
        case 'admin':
            header('Location: admin.php');
            break;
        case 'student':
            header('Location: student.php');
            break;
        case 'teacher':
            header('Location: teacher.php');
            break;
        default:
            header('Location: ../index.php');
    }
    exit();
}
?> 