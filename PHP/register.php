<?php
require_once('../includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $pseudo = $_POST['pseudo'];
    $postal = $_POST['postal'];
    $birthdate = $_POST['birthdate'];
    $role = $_POST['role'];

    try {
        // Vérifier si l'email existe déjà
        $check_query = "SELECT * FROM utilisateur WHERE email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->execute([$email]);

        if ($check_stmt->rowCount() > 0) {
            echo "Cette adresse email est déjà utilisée.";
        } else {
            // Insérer le nouvel utilisateur
            $query = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, pseudo, code_postal, date_naissance, role) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$nom, $prenom, $email, $password, $pseudo, $postal, $birthdate, $role]);

            // Rediriger vers la page de connexion
            header('Location: ../index.php?registration=success');
            exit();
        }
    } catch(PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?> 