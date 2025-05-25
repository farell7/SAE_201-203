<?php
session_start();

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
        if ($role !== 'admin') {
            header('Location: ../index.php');
            exit();
        }
        break;
    case 'teacher':
        if ($role !== 'teacher') {
            header('Location: ../index.php');
            exit();
        }
        break;
    case 'agent':
        if ($role !== 'agent') {
            header('Location: ../index.php');
            exit();
        }
        break;
    case 'student':
        if ($role !== 'student') {
            header('Location: ../index.php');
            exit();
        }
        break;
}

$user = $_SESSION['utilisateur'];
?> 