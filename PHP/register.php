<?php
session_start();
require_once('connexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $pseudo = htmlspecialchars(trim($_POST['pseudo']));
    $postal = htmlspecialchars(trim($_POST['postal']));
    $birthdate = $_POST['birthdate'];
    $role = $_POST['role'];

    try {
        // Vérifier si l'email existe déjà
        $check_query = "SELECT * FROM utilisateur WHERE email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->execute([$email]);

        if ($check_stmt->rowCount() > 0) {
            $_SESSION['error'] = "Cette adresse email est déjà utilisée.";
            header('Location: ../index.php');
            exit();
        }

        // Insérer le nouvel utilisateur
        $valide = ($role === 'admin') ? 1 : 0; // Les admins sont automatiquement validés
        $query = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, pseudo, code_postal, date_naissance, role, valide) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        if ($stmt->execute([$nom, $prenom, $email, $password, $pseudo, $postal, $birthdate, $role, $valide])) {
            if ($role === 'admin') {
                $_SESSION['success'] = "Compte administrateur créé avec succès. Vous pouvez vous connecter.";
            } else {
                $_SESSION['success'] = "Compte créé avec succès. Un administrateur doit valider votre compte avant que vous puissiez vous connecter.";
            }
            header('Location: ../index.php');
            exit();
        } else {
            throw new PDOException("Erreur lors de l'insertion");
        }
    } catch(PDOException $e) {
        error_log("Erreur d'inscription : " . $e->getMessage());
        $_SESSION['error'] = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
        header('Location: ../index.php');
        exit();
    }
}
?> 