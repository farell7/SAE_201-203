<?php
session_start();
require_once 'connexion.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$messageType = '';

// Traitement de la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserver'])) {
    $materiel_id = $_POST['materiel_id'];
    $user_id = $_SESSION['utilisateur']['id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    // Vérifier si le matériel est disponible pour cette période
    $stmt = $connexion->prepare("SELECT COUNT(*) FROM reservation_materiel 
                           WHERE materiel_id = ? 
                           AND ((date_debut BETWEEN ? AND ?) 
                           OR (date_fin BETWEEN ? AND ?))
                           AND statut != 'refusee'");
    $stmt->execute([$materiel_id, $date_debut, $date_fin, $date_debut, $date_fin]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $message = "Le matériel n'est pas disponible pour cette période.";
        $messageType = 'error';
    } else {
        // Insérer la réservation
        $stmt = $connexion->prepare("INSERT INTO reservation_materiel (materiel_id, user_id, date_debut, date_fin, statut) 
                               VALUES (?, ?, ?, ?, 'en_attente')");
        if ($stmt->execute([$materiel_id, $user_id, $date_debut, $date_fin])) {
            $message = "Votre demande de réservation a été enregistrée.";
            $messageType = 'success';
        } else {
            $message = "Une erreur est survenue lors de la réservation.";
            $messageType = 'error';
        }
    }
}

// Récupérer la liste du matériel avec leurs réservations
$stmt = $connexion->query("SELECT m.*, 
    GROUP_CONCAT(
        CONCAT(
            rm.date_debut, '|',
            rm.date_fin, '|',
            rm.statut
        ) SEPARATOR ';'
    ) as reservations
    FROM materiel m 
    LEFT JOIN reservation_materiel rm ON m.id = rm.materiel_id 
    GROUP BY m.id");
$materiels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les réservations de l'utilisateur
$stmt = $connexion->prepare("SELECT rm.*, m.nom as materiel_nom, m.type as materiel_type 
                        FROM reservation_materiel rm 
                        JOIN materiel m ON rm.materiel_id = m.id 
                        WHERE rm.user_id = ? 
                        ORDER BY rm.date_debut DESC");
$stmt->execute([$_SESSION['utilisateur']['id']]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>