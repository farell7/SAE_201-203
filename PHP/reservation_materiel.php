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
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'list_materiel':
                try {
                    $sql = "SELECT * FROM materiel WHERE disponible = 1";
                    $params = [];

                    if (isset($_GET['type']) && !empty($_GET['type'])) {
                        $sql .= " AND type = ?";
                        $params[] = $_GET['type'];
                    }

                    if (isset($_GET['etat']) && !empty($_GET['etat'])) {
                        $sql .= " AND etat = ?";
                        $params[] = $_GET['etat'];
                    }

                    $sql .= " ORDER BY nom";
                    $stmt = $connexion->prepare($sql);
                    $stmt->execute($params);
                    $materiel = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    echo json_encode(['materiel' => $materiel]);
                    exit();
                } catch (PDOException $e) {
                    echo json_encode(['error' => 'Erreur lors de la récupération du matériel']);
                    exit();
                }
                break;

            case 'list_reservations':
                try {
                    $stmt = $connexion->prepare("
                        SELECT r.*, m.nom as materiel_nom, m.type as materiel_type
                        FROM reservation_materiel r 
                        JOIN materiel m ON r.materiel_id = m.id 
                        WHERE r.user_id = ?
                        ORDER BY r.created_at DESC
                    ");
                    $stmt->execute([$_SESSION['user']['id']]);
                    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    echo json_encode(['reservations' => $reservations]);
                    exit();
                } catch (PDOException $e) {
                    echo json_encode(['error' => 'Erreur lors de la récupération des réservations']);
                    exit();
                }
                break;

            default:
                echo json_encode(['error' => 'Action non valide']);
                exit();
        }
    }
}

// Traitement des requêtes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'reserver') {
        try {
            if (!isset($_POST['materiel_id']) || !isset($_POST['date_debut']) || !isset($_POST['date_fin'])) {
                throw new Exception('Données manquantes');
            }

            $materiel_id = (int)$_POST['materiel_id'];
            $date_debut = $_POST['date_debut'];
            $date_fin = $_POST['date_fin'];
            $user_id = $_SESSION['user']['id'];

            // Vérifier si le matériel est disponible pour cette période
            $stmt = $connexion->prepare("
                SELECT COUNT(*) FROM reservation_materiel 
                WHERE materiel_id = ? 
                AND ((date_debut BETWEEN ? AND ?) 
                OR (date_fin BETWEEN ? AND ?))
                AND statut = 'validee'
            ");
            $stmt->execute([$materiel_id, $date_debut, $date_fin, $date_debut, $date_fin]);
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Ce matériel n\'est pas disponible pour cette période');
            }

            // Créer la réservation
            $stmt = $connexion->prepare("
                INSERT INTO reservation_materiel (materiel_id, user_id, date_debut, date_fin, statut) 
                VALUES (?, ?, ?, ?, 'en_attente')
            ");
            $stmt->execute([$materiel_id, $user_id, $date_debut, $date_fin]);

            echo json_encode(['success' => 'Votre demande de réservation a été enregistrée et est en attente de validation']);
            exit();
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }
}

echo json_encode(['error' => 'Méthode non autorisée']);
?> 