<?php
session_start();

// Définir le chemin de base
define('BASE_PATH', '/SAE_201-203');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ' . BASE_PATH . '/index.php');
    exit();
}

// Récupérer le rôle de l'utilisateur
$role = $_SESSION['utilisateur']['role'];

// Vérifier le rôle selon la page
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Vérifier les autorisations
switch($current_page) {
    case 'admin':
        if ($role !== 'admin') {
            header('Location: ' . BASE_PATH . '/index.php');
            exit();
        }
        break;
    case 'teacher':
        if ($role !== 'teacher') {
            header('Location: ' . BASE_PATH . '/index.php');
            exit();
        }
        break;
    case 'agent':
        if ($role !== 'agent') {
            header('Location: ' . BASE_PATH . '/index.php');
            exit();
        }
        break;
    case 'student':
        if ($role !== 'student') {
            header('Location: ' . BASE_PATH . '/index.php');
            exit();
        }
        break;
}

$user = $_SESSION['utilisateur'];
?> 