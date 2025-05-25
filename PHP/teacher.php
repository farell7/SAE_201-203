<?php
session_start();
header('Content-Type: application/json');

// Vérification de la session et du rôle
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    echo json_encode(['error' => 'unauthorized']);
    exit();
}

// Retourner les données de l'utilisateur
echo json_encode([
    'username' => $_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom'],
    'role' => $_SESSION['user']['role']
]);
?>

