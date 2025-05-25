<?php
session_start();
header('Content-Type: application/json');

require_once 'connexion.php';

// Vérification des données POST
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode(['error' => 'Veuillez remplir tous les champs']);
    exit();
}

$email = $_POST['email'];
$password = $_POST['password'];

try {
    // Préparation de la requête
    $query = "SELECT * FROM utilisateurs WHERE email = :email";
    $stmt = $connexion->prepare($query);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['valide'] == 0) {
            echo json_encode(['error' => 'Votre compte n\'a pas encore été validé']);
            exit();
        }

        // Stockage des informations de session
        $_SESSION['utilisateur'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'role' => $user['role']
        ];

        // Redirection selon le rôle
        $redirect = '';
        switch ($user['role']) {
            case 'admin':
                $redirect = 'admin.php';
                break;
            case 'teacher':
                $redirect = 'teacher.php';
                break;
            case 'student':
                $redirect = 'student.php';
                break;
            case 'agent':
                $redirect = 'agent.php';
                break;
            default:
                echo json_encode(['error' => 'Rôle non reconnu']);
                exit();
        }

        echo json_encode(['redirect' => $redirect]);
    } else {
        echo json_encode(['error' => 'Email ou mot de passe incorrect']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur de connexion à la base de données']);
}
?> 