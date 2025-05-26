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
    $salle_id = $_POST['salle_id'];
    $user_id = $_SESSION['utilisateur']['id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    // Vérifier les réservations existantes
    $stmt = $conn->prepare("SELECT COUNT(*) FROM reservation_salle
                         WHERE salle_id = ? 
                         AND statut = 'validee'
                         AND (
                             (date_debut BETWEEN ? AND ?)
                             OR (date_fin BETWEEN ? AND ?)
                             OR (? BETWEEN date_debut AND date_fin)
                         )");
    $stmt->execute([$salle_id, $date_debut, $date_fin, $date_debut, $date_fin, $date_debut]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $message = "La salle n'est pas disponible pour cette période.";
        $messageType = 'error';
    } else {
        // Insérer la réservation
        $stmt = $conn->prepare("INSERT INTO reservation_salle (salle_id, user_id, date_debut, date_fin, statut)
                             VALUES (?, ?, ?, ?, 'en_attente')");
        if ($stmt->execute([$salle_id, $user_id, $date_debut, $date_fin])) {
            $message = "Votre demande de réservation a été enregistrée.";
            $messageType = 'success';
        } else {
            $message = "Une erreur est survenue lors de la réservation.";
            $messageType = 'error';
        }
    }
}

// Récupérer la liste des salles avec leurs réservations
$stmt = $conn->query("SELECT s.*, 
    GROUP_CONCAT(
        CONCAT(
            rs.date_debut, '|',
            rs.date_fin, '|',
            rs.statut
        ) SEPARATOR ';'
    ) as reservations
    FROM salle s 
    LEFT JOIN reservation_salle rs ON s.id = rs.salle_id 
    GROUP BY s.id");
$salles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les réservations de l'utilisateur
$sql = "SELECT rs.*, s.nom as salle_nom, s.capacite as salle_capacite
        FROM reservation_salle rs
        JOIN salle s ON rs.salle_id = s.id
        WHERE rs.user_id = ?
        ORDER BY rs.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$_SESSION['utilisateur']['id']]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?> 