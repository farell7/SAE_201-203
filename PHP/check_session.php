<?php
session_start();

// Fonction pour vérifier si l'utilisateur est connecté
function checkLogin() {
    if (!isset($_SESSION['utilisateur'])) {
        header('Location: /SAE_201-203/index.php');
        exit();
    }
}

// Fonction pour vérifier le rôle de l'utilisateur
function checkRole($allowed_roles) {
    checkLogin();
    
    if (!in_array($_SESSION['utilisateur']['role'], $allowed_roles)) {
        switch($_SESSION['utilisateur']['role']) {
            case 'admin':
                header('Location: /SAE_201-203/PHP/admin.php');
                break;
            case 'agent':
                header('Location: /SAE_201-203/PHP/agent.php');
                break;
            case 'teacher':
                header('Location: /SAE_201-203/PHP/teacher.php');
                break;
            default:
                header('Location: /SAE_201-203/PHP/student.php');
        }
        exit();
    }
}

// Définir le chemin de base
define('BASE_PATH', '/SAE_201-203');

// Récupérer le rôle de l'utilisateur
$role = $_SESSION['utilisateur']['role'];

// Vérifier le rôle selon la page
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Vérifier les autorisations
switch($current_page) {
    case 'admin':
    case 'admin_demandes_materiel':
    case 'gestion_materiel':
    case 'gestion_salle':
    case 'validation_compte':
        checkRole(['admin']);
        break;
    case 'student':
    case 'reservation_materiel':
    case 'demande_materiel':
        checkRole(['student']);
        break;
    case 'teacher':
        checkRole(['teacher']);
        break;
    case 'agent':
        checkRole(['agent']);
        break;
}

$user = $_SESSION['utilisateur'];
?> 