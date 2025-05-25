<?php
session_start();
header('Content-Type: application/json');

// Vérification de la session
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
    exit();
}

require_once 'connexion.php';

// Traitement des requêtes GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    try {
        switch ($_GET['action']) {
            case 'list_materiel':
                // Récupération des réservations de matériel validées
                $stmt = $connexion->prepare("
                    SELECT r.*, m.nom as materiel_nom, m.type as materiel_type,
                           u.nom as user_nom, u.prenom as user_prenom
                    FROM reservation_materiel r 
                    JOIN materiel m ON r.materiel_id = m.id
                    LEFT JOIN utilisateur u ON r.user_id = u.id
                    WHERE r.statut = 'validee'
                    ORDER BY r.date_signature DESC
                ");
                $stmt->execute();
                $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['reservations' => $reservations]);
                break;

            case 'list_salles':
                // Récupération des réservations de salles validées
                $stmt = $connexion->prepare("
                    SELECT rs.*, s.nom as salle_nom, s.capacite,
                           u.nom as user_nom, u.prenom as user_prenom
                    FROM reservation_salle rs 
                    JOIN salle s ON rs.salle_id = s.id
                    LEFT JOIN utilisateur u ON rs.user_id = u.id
                    WHERE rs.statut = 'validee'
                    ORDER BY rs.date_signature DESC
                ");
                $stmt->execute();
                $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['reservations' => $reservations]);
                break;

            default:
                echo json_encode(['error' => 'Action non valide']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur lors de la récupération des réservations']);
    }
    exit();
}

echo json_encode(['error' => 'Méthode non autorisée']);
?> 