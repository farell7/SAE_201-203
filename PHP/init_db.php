<?php
require_once 'config.php';

try {
    // Création de l'utilisateur admin par défaut
    $password = password_hash('Admin123!', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) 
                           VALUES (?, ?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE id=id");
    
    $stmt->execute(['Admin', 'System', 'admin@resauge.fr', $password, 'admin']);
    
    echo "Base de données initialisée avec succès!\n";
    echo "Email admin: admin@resauge.fr\n";
    echo "Mot de passe admin: Admin123!\n";
    
} catch(PDOException $e) {
    die("Erreur d'initialisation : " . $e->getMessage());
}
?> 