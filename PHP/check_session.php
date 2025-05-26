<?php
session_start();
require_once 'includes/redirect_role.php';

// Définir le chemin de base
define('BASE_PATH', '/SAE_201-203');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit();
}

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
        if ($role !== 'admin') {
            redirect_to_role_home();
        }
        break;
    case 'student':
    case 'reservation_materiel':
    case 'demande_materiel':
        if ($role !== 'student') {
            redirect_to_role_home();
        }
        break;
    case 'teacher':
        if ($role !== 'teacher') {
            redirect_to_role_home();
        }
        break;
    case 'agent':
        if ($role !== 'agent') {
            redirect_to_role_home();
        }
        break;
}

$user = $_SESSION['utilisateur'];
?> 