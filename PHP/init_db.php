<?php
require_once 'connexion.php';

try {
    // Vérification si la table utilisateur existe
    $tableExists = $conn->query("SHOW TABLES LIKE 'utilisateur'")->rowCount() > 0;
    if (!$tableExists) {
        die("La table 'utilisateur' n'existe pas. Veuillez d'abord exécuter setup_database.php");
    }

    // Création de l'utilisateur admin par défaut
    $password = password_hash('Admin123!', PASSWORD_DEFAULT);
    
    // Vérification si l'admin existe déjà
    $stmt = $conn->prepare("SELECT id FROM utilisateur WHERE email = ?");
    $stmt->execute(['admin@resauge.fr']);
    $adminExists = $stmt->fetch();

    if (!$adminExists) {
        $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role, valide) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Admin', 'System', 'admin@resauge.fr', $password, 'admin', 1]);
        echo "✓ Compte administrateur créé avec succès!<br>";
    } else {
        echo "ℹ Le compte administrateur existe déjà.<br>";
    }
    
    echo "<br>Informations de connexion :<br>";
    echo "Email admin: admin@resauge.fr<br>";
    echo "Mot de passe admin: Admin123!<br>";
    
} catch(PDOException $e) {
    die("❌ Erreur d'initialisation : " . $e->getMessage());
}
?> 