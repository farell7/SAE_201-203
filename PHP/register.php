<?php
require_once 'connexion.php';

// Afficher les données reçues pour debug
echo "<pre>";
print_r($_POST);
echo "</pre>";

try {
    // Validation du rôle
    $roles_valides = ['student', 'teacher', 'agent', 'admin'];
    if (!in_array($_POST['role'], $roles_valides)) {
        echo "<p style='color: red; text-align: center; font-family: Arial;'>Rôle invalide</p>";
        exit();
    }

    // Vérification de l'email unique
    $check = $connexion->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ?");
    $check->execute([$_POST['email']]);
    if($check->fetchColumn() > 0) {
        echo "<p style='color: red; text-align: center; font-family: Arial;'>Cet email est déjà utilisé</p>";
        exit();
    }

    $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, pseudo, code_postal, date_naissance, role) 
            VALUES (:nom, :prenom, :email, :password, :pseudo, :code_postal, :date_naissance, :role)";
    
    $stmt = $connexion->prepare($sql);
    $result = $stmt->execute([
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'],
        'email' => $_POST['email'],
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'pseudo' => $_POST['pseudo'],
        'code_postal' => $_POST['postal'],
        'date_naissance' => $_POST['birthdate'],
        'role' => $_POST['role']
    ]);

    if($result) {
        echo "<p style='color: green; text-align: center; font-family: Arial;'>Inscription réussie ! Votre compte sera validé par un administrateur.</p>";
    } else {
        echo "<p style='color: red; text-align: center; font-family: Arial;'>Erreur lors de l'inscription</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color: red; text-align: center; font-family: Arial;'>Erreur : " . $e->getMessage() . "</p>";
}
?> 