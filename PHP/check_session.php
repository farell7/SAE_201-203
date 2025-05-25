<?php
session_start();

function checkSession($required_role = null) {
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['utilisateur'])) {
        header('Location: ../index.php');
        exit();
    }

    // Si un rôle spécifique est requis, le vérifier
    if ($required_role !== null) {
        if ($_SESSION['utilisateur']['role'] !== $required_role) {
            // Rediriger vers la page appropriée selon le rôle
            switch($_SESSION['utilisateur']['role']) {
                case 'admin':
                    header('Location: admin.php');
                    break;
                case 'teacher':
                    header('Location: teacher.php');
                    break;
                case 'agent':
                    header('Location: agent.php');
                    break;
                case 'student':
                    header('Location: student.php');
                    break;
                default:
                    header('Location: ../index.php');
            }
            exit();
        }
    }

    return $_SESSION['utilisateur'];
}
?> 