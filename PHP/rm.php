<?php
session_start();
require_once 'connexion.php';

// Vérification de la session
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit();
}

// Messages de retour pour l'utilisateur
$message = '';
$messageType = '';

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reserver'])) {
        try {
            $materiel_id = (int)$_POST['materiel_id'];
            $date_debut = $_POST['date_debut'];
            $date_fin = $_POST['date_fin'];
            $user_id = $_SESSION['utilisateur']['id'];

            // Créer la réservation
            $stmt = $conn->prepare("
                INSERT INTO reservation_materiel (materiel_id, user_id, date_debut, date_fin, statut) 
                VALUES (?, ?, ?, ?, 'en_attente')
            ");
            $stmt->execute([$materiel_id, $user_id, $date_debut, $date_fin]);

            $message = "Votre demande de réservation a été enregistrée et est en attente de validation";
            $messageType = "success";
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// Récupérer la liste du matériel
$materiels = $conn->query("
    SELECT m.*, 
           GROUP_CONCAT(
               CONCAT(r.date_debut, '|', r.date_fin, '|', r.statut)
               SEPARATOR ';'
           ) as reservations
    FROM materiel m
    LEFT JOIN reservation_materiel r ON m.id = r.materiel_id
    WHERE m.disponible = 1
    GROUP BY m.id
    ORDER BY m.nom
")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les réservations de l'utilisateur
$stmt = $conn->prepare("
    SELECT r.*, m.nom as materiel_nom, m.type as materiel_type
    FROM reservation_materiel r 
    JOIN materiel m ON r.materiel_id = m.id 
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$_SESSION['utilisateur']['id']]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>