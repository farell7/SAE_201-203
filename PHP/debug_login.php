<?php
require_once 'config.php';

try {
    // 1. Vérifier si l'utilisateur admin existe
    $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE email = 'admin@resauge.fr'");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<h2>Vérification du compte admin</h2>";
    if ($user) {
        echo "Le compte admin existe dans la base de données<br>";
        echo "Role: " . $user['role'] . "<br>";
        
        // 2. Tester le mot de passe
        $test_password = 'admin';
        if (password_verify($test_password, $user['mot_de_passe'])) {
            echo "Le mot de passe 'admin' est correct!<br>";
        } else {
            echo "Le mot de passe 'admin' est incorrect!<br>";
            
            // 3. Réinitialiser le mot de passe
            $new_password = password_hash('admin', PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE email = 'admin@resauge.fr'");
            $update->execute([$new_password]);
            
            echo "<br>Le mot de passe a été réinitialisé.<br>";
            echo "Nouveaux identifiants :<br>";
            echo "Email: admin@resauge.fr<br>";
            echo "Mot de passe: admin";
        }
    } else {
        echo "Le compte admin n'existe pas dans la base de données<br>";
        
        // Créer le compte admin
        $password = password_hash('admin', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Admin', 'System', 'admin@resauge.fr', $password, 'admin']);
        
        echo "<br>Un nouveau compte admin a été créé :<br>";
        echo "Email: admin@resauge.fr<br>";
        echo "Mot de passe: admin";
    }
    
    // 4. Afficher la structure de la table
    echo "<h2>Structure de la table utilisateur</h2>";
    $columns = $conn->query("SHOW COLUMNS FROM utilisateur");
    echo "<pre>";
    while($column = $columns->fetch(PDO::FETCH_ASSOC)) {
        print_r($column);
    }
    echo "</pre>";
    
} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?> 