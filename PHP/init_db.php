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
    $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE email = ?");
    $stmt->execute(['admin@resauge.fr']);
    $adminExists = $stmt->fetch();

    if (!$adminExists) {
        $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role, valide) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute(['Admin', 'System', 'admin@resauge.fr', $password, 'admin', 1]);
        
        if ($result) {
            echo "✓ Compte administrateur créé avec succès!<br>";
            
            // Vérification de la création
            $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE email = ?");
            $stmt->execute(['admin@resauge.fr']);
            $newAdmin = $stmt->fetch();
            
            if ($newAdmin) {
                echo "✓ Vérification du compte : OK<br>";
                echo "ID: " . $newAdmin['id'] . "<br>";
                echo "Role: " . $newAdmin['role'] . "<br>";
                echo "Validé: " . ($newAdmin['valide'] ? 'Oui' : 'Non') . "<br>";
            } else {
                echo "❌ Erreur : Le compte n'a pas été trouvé après la création<br>";
            }
        } else {
            echo "❌ Erreur lors de la création du compte administrateur<br>";
        }
    } else {
        echo "ℹ Le compte administrateur existe déjà.<br>";
        echo "ID: " . $adminExists['id'] . "<br>";
        echo "Role: " . $adminExists['role'] . "<br>";
        echo "Validé: " . ($adminExists['valide'] ? 'Oui' : 'Non') . "<br>";
        
        // Mise à jour du mot de passe admin
        $stmt = $conn->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE email = ?");
        $stmt->execute([$password, 'admin@resauge.fr']);
        echo "✓ Mot de passe admin mis à jour<br>";
    }
    
    echo "<br>Informations de connexion :<br>";
    echo "Email: admin@resauge.fr<br>";
    echo "Mot de passe: Admin123!<br>";
    
    // Test de vérification du mot de passe
    $stmt = $conn->prepare("SELECT mot_de_passe FROM utilisateur WHERE email = ?");
    $stmt->execute(['admin@resauge.fr']);
    $storedHash = $stmt->fetchColumn();
    
    if (password_verify('Admin123!', $storedHash)) {
        echo "✓ Test de mot de passe : OK<br>";
    } else {
        echo "❌ Test de mot de passe : ÉCHEC<br>";
    }
    
} catch(PDOException $e) {
    die("❌ Erreur d'initialisation : " . $e->getMessage());
}
?> 