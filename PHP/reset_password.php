<?php
header('Content-Type: application/json');
require_once 'connexion.php';

// Vérification des données POST
if (!isset($_POST['token']) || !isset($_POST['password'])) {
    echo json_encode(['error' => 'Données manquantes']);
    exit();
}

$token = $_POST['token'];
$password = $_POST['password'];

try {
    // Vérification du token et de sa validité
    $stmt = $connexion->prepare("SELECT id FROM utilisateurs WHERE reset_token = :token AND reset_token_expiry > NOW()");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['error' => 'Lien de réinitialisation invalide ou expiré']);
        exit();
    }

    // Hashage du nouveau mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Mise à jour du mot de passe et suppression du token
    $stmt = $connexion->prepare("UPDATE utilisateurs SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE id = :id");
    $stmt->execute([
        'password' => $hashed_password,
        'id' => $user['id']
    ]);

    echo json_encode(['success' => 'Votre mot de passe a été réinitialisé avec succès']);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur lors de la réinitialisation du mot de passe']);
}
?> 