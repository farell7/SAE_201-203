<?php
session_start();
header('Content-Type: application/json');

// Connexion à la base de données
$host = 'localhost';
$dbname = 'resauge';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Vous devez être connecté pour faire une réservation.');
    }

    // Récupérer les données du formulaire
    $materiel_id = (int)$_POST['materiel_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $user_id = $_SESSION['user_id'];

    // Vérifier si le créneau est disponible
    $sql = "SELECT COUNT(*) FROM reservation_materiel 
            WHERE materiel_id = ? 
            AND statut IN ('en_attente', 'validee')
            AND (
                (date_debut BETWEEN ? AND ?) 
                OR (date_fin BETWEEN ? AND ?)
                OR (date_debut <= ? AND date_fin >= ?)
            )";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$materiel_id, $date_debut, $date_fin, $date_debut, $date_fin, $date_debut, $date_fin]);
    
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Ce créneau est déjà réservé.');
    }

    // Insérer la nouvelle réservation
    $sql = "INSERT INTO reservation_materiel (materiel_id, user_id, date_debut, date_fin, statut) 
            VALUES (?, ?, ?, ?, 'en_attente')";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$materiel_id, $user_id, $date_debut, $date_fin]);

    echo json_encode([
        'success' => true,
        'message' => 'Réservation enregistrée avec succès.'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 