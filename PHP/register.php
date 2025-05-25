<?php
header('Content-Type: application/json');
require_once 'connexion.php';

// Vérification des données POST
if (!isset($_POST['nom']) || !isset($_POST['prenom']) || !isset($_POST['email']) || 
    !isset($_POST['password']) || !isset($_POST['role'])) {
    echo json_encode(['error' => 'Veuillez remplir tous les champs']);
    exit();
}

$nom = filter_var($_POST['nom'], FILTER_SANITIZE_STRING);
$prenom = filter_var($_POST['prenom'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];
$role = $_POST['role'];

// Validation du rôle
$roles_valides = ['student', 'teacher'];
if (!in_array($role, $roles_valides)) {
    echo json_encode(['error' => 'Rôle invalide']);
    exit();
}

// Validation de l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Email invalide']);
    exit();
}

try {
    // Vérification si l'email existe déjà
    $stmt = $connexion->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['error' => 'Cet email est déjà utilisé']);
        exit();
    }

    // Hashage du mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insertion de l'utilisateur
    $stmt = $connexion->prepare("INSERT INTO utilisateur (nom, prenom, email, password, pseudo, code_postal, date_naissance, role, valide) VALUES (:nom, :prenom, :email, :password, :pseudo, :postal, :birthdate, :role, 0)");
    $stmt->execute([
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'password' => $hashed_password,
        'pseudo' => $_POST['pseudo'],
        'postal' => $_POST['postal'],
        'birthdate' => $_POST['birthdate'],
        'role' => $role
    ]);

    echo json_encode(['success' => 'Inscription réussie ! Votre compte sera validé par un administrateur.']);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur lors de l\'inscription: ' . $e->getMessage()]);
}
?> 