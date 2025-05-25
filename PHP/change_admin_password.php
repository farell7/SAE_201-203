<?php
require_once 'config.php';

try {
    // Nouveau mot de passe simple : 'admin'
    $password = password_hash('admin', PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE email = 'admin@resauge.fr'");
    $stmt->execute([$password]);
    
    if($stmt->rowCount() > 0) {
        echo "Le mot de passe admin a été changé avec succès!<br>";
        echo "Email: admin@resauge.fr<br>";
        echo "Nouveau mot de passe: admin";
    } else {
        echo "Le compte admin n'existe pas encore. Création du compte...<br>";
        
        $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Admin', 'System', 'admin@resauge.fr', $password, 'admin']);
        
        echo "Compte admin créé avec succès!<br>";
        echo "Email: admin@resauge.fr<br>";
        echo "Mot de passe: admin";
    }
    
} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?> 