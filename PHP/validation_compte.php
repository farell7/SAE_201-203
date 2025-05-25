<?php
session_start();
header('Content-Type: application/json');

// Vérification de la session et du rôle admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['error' => 'Accès non autorisé']);
    exit();
}

require_once 'connexion.php';

// Traitement des requêtes GET (liste des utilisateurs)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'list') {
    try {
        $stmt = $connexion->prepare("SELECT id, nom, prenom, email, role FROM utilisateurs WHERE valide = 0");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['users' => $users]);
        exit();
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur lors de la récupération des utilisateurs']);
        exit();
    }
}

// Traitement des requêtes POST (validation/rejet)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['action']) || !isset($data['userId'])) {
        echo json_encode(['error' => 'Données manquantes']);
        exit();
    }

    $userId = $data['userId'];
    
    try {
        if ($data['action'] === 'validate') {
            $stmt = $connexion->prepare("UPDATE utilisateurs SET valide = 1 WHERE id = :id");
            $stmt->execute(['id' => $userId]);
            
            echo json_encode(['success' => 'Utilisateur validé avec succès']);
        } elseif ($data['action'] === 'reject') {
            $stmt = $connexion->prepare("DELETE FROM utilisateurs WHERE id = :id AND valide = 0");
            $stmt->execute(['id' => $userId]);
            
            echo json_encode(['success' => 'Utilisateur rejeté avec succès']);
        } else {
            echo json_encode(['error' => 'Action non valide']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur lors du traitement de la demande']);
    }
    exit();
}

// Si aucune action valide n'est spécifiée
echo json_encode(['error' => 'Action non spécifiée']);
?>
