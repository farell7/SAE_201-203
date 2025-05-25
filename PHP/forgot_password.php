<?php
header('Content-Type: application/json');
require_once 'connexion.php';

// Vérification des données POST
if (!isset($_POST['email'])) {
    echo json_encode(['error' => 'Veuillez fournir une adresse email']);
    exit();
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

// Validation de l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Adresse email invalide']);
    exit();
}

try {
    // Vérification si l'email existe
    $stmt = $connexion->prepare("SELECT id FROM utilisateurs WHERE email = :email");
    $stmt->execute(['email' => $email]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['error' => 'Aucun compte associé à cette adresse email']);
        exit();
    }

    // Génération d'un token unique
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Enregistrement du token
    $stmt = $connexion->prepare("UPDATE utilisateurs SET reset_token = :token, reset_token_expiry = :expiry WHERE email = :email");
    $stmt->execute([
        'token' => $token,
        'expiry' => $expiry,
        'email' => $email
    ]);

    // Envoi de l'email
    $resetLink = "https://" . $_SERVER['HTTP_HOST'] . "/reset_password.html?token=" . $token;
    $to = $email;
    $subject = "Réinitialisation de votre mot de passe - Université Eiffel";
    $message = "Bonjour,\n\n";
    $message .= "Vous avez demandé la réinitialisation de votre mot de passe.\n";
    $message .= "Cliquez sur le lien suivant pour réinitialiser votre mot de passe :\n";
    $message .= $resetLink . "\n\n";
    $message .= "Ce lien est valable pendant 24 heures.\n";
    $message .= "Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.\n\n";
    $message .= "Cordialement,\nL'équipe de l'Université Eiffel";
    
    $headers = "From: no-reply@univ-eiffel.fr";

    if (mail($to, $subject, $message, $headers)) {
        echo json_encode(['success' => 'Un email de réinitialisation a été envoyé à votre adresse']);
    } else {
        echo json_encode(['error' => 'Erreur lors de l\'envoi de l\'email']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur lors du traitement de la demande']);
}
?> 