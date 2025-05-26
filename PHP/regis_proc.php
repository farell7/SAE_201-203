<?php
require 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Activation des logs d'erreur
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    // Nettoyage et validation des données
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $pseudo = htmlspecialchars(trim($_POST['pseudo']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $postal = htmlspecialchars(trim($_POST['postal']));
    $birthdate = $_POST['birthdate'];
    $role = $_POST['role'];
    
    try {
        // Vérification de la connexion à la base de données
        if (!$pdo) {
            throw new Exception("La connexion à la base de données a échoué");
        }

        // Vérification si l'email existe déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            showModal('error', 'Cette adresse email est déjà utilisée.');
            exit();
        }

        // Vérification si le pseudo existe déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE pseudo = ?");
        $stmt->execute([$pseudo]);
        if ($stmt->fetchColumn() > 0) {
            showModal('error', 'Ce pseudo est déjà utilisé.');
            exit();
        }

        // Insertion de l'utilisateur
        $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, prenom, pseudo, email, password, code_postal, date_naissance, role, valide) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
        
        $result = $stmt->execute([$nom, $prenom, $pseudo, $email, $password, $postal, $birthdate, $role]);
        
        if (!$result) {
            throw new Exception("Échec de l'insertion dans la base de données");
        }

        // Envoi de l'email
        $to = "admin@resauge.fr";
        $subject = "Nouvelle demande d'inscription - ResaUGE";
        $message = "Une nouvelle demande d'inscription a été reçue :\n\n";
        $message .= "Nom : $nom\n";
        $message .= "Prénom : $prenom\n";
        $message .= "Pseudo : $pseudo\n";
        $message .= "Email : $email\n";
        $message .= "Code Postal : $postal\n";
        $message .= "Date de naissance : $birthdate\n";
        $message .= "Rôle : $role\n\n";
        $message .= "Pour valider ou rejeter cette demande, connectez-vous à l'interface d'administration.";
        
        $headers = "From: noreply@resauge.fr\r\n";
        $headers .= "Reply-To: noreply@resauge.fr\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        mail($to, $subject, $message, $headers);

        showModal('success', 'Votre compte a été créé avec succès et est en attente de validation par l\'administrateur. Vous recevrez un email lorsque votre compte sera validé.');
        
    } catch (Exception $e) {
        error_log("Erreur lors de l'inscription : " . $e->getMessage());
        showModal('error', 'Une erreur est survenue lors de l\'inscription : ' . $e->getMessage());
    }
}

function showModal($type, $message) {
    ?>